<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    public function index()
    {
        return view('support.index', [
            'types' => $this->ticketTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        if (!array_key_exists($type, $this->ticketTypes())) {
            return back()->withInput()->withErrors(['type' => __('Invalid request type.')]);
        }

        $rules = $this->validationRules($type);
        $validator = Validator::make($request->all(), $rules, $this->validationMessages());

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            $ticket = Ticket::create([
                'ticket_number' => Ticket::generateTicketNumber(),
                'ticket_type' => $type,
                'status' => Ticket::STATUS_NEW,
                'customer_name' => $data['name'] ?? null,
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'] ?? null,
                'title' => $this->resolveTitle($type, $data),
                'description' => $data['description'],
                'complaint_type' => $data['complaint_type'] ?? null,
                'related_service_reference' => $data['trip_reference'] ?? null,
                'device_type' => $data['device_type'] ?? null,
            ]);

            TicketStatusLog::create([
                'ticket_id' => $ticket->id,
                'old_status' => null,
                'new_status' => Ticket::STATUS_NEW,
                'changed_by_employee_id' => null,
                'created_at' => now(),
            ]);

            $this->storeAttachments($request, $ticket);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->withErrors([
                'description' => __('Failed to save the request. Please try again.'),
            ]);
        }

        return redirect()
            ->route('support')
            ->with('success', __('Your request has been received successfully.'))
            ->with('ticket_number', $ticket->ticket_number);
    }

    public function track()
    {
        return view('support.track');
    }

    public function lookup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_number' => 'required|string',
            'email' => 'required|email',
        ], [
            'email.required' => __('Please enter a valid email address so we can contact you.'),
            'email.email' => __('Please enter a valid email address so we can contact you.'),
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $ticket = Ticket::query()
            ->with(['replies.attachments', 'ticketLevelAttachments'])
            ->where('ticket_number', trim($request->input('ticket_number')))
            ->where('customer_email', trim($request->input('email')))
            ->first();

        if (!$ticket) {
            return back()
                ->withInput()
                ->withErrors(['ticket_number' => __('No matching request was found for the provided details.')]);
        }

        return view('support.track', compact('ticket'));
    }

    private function ticketTypes(): array
    {
        return [
            Ticket::TYPE_INQUIRY => __('Inquiry'),
            Ticket::TYPE_COMPLAINT => __('Complaint'),
            Ticket::TYPE_TECHNICAL => __('Technical Issue'),
        ];
    }

    private function resolveTitle(string $type, array $data): string
    {
        return match ($type) {
            Ticket::TYPE_INQUIRY => $data['subject'],
            Ticket::TYPE_COMPLAINT => $data['complaint_type'],
            Ticket::TYPE_TECHNICAL => __('Technical Issue') . ' - ' . strtoupper($data['device_type']),
            default => __('Support Tickets'),
        };
    }

    private function validationRules(string $type): array
    {
        $common = [
            'name' => 'nullable|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'description' => 'required|string|max:5000',
        ];

        $attachmentRules = [
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,jpg,png,pdf|max:5120',
        ];

        return match ($type) {
            Ticket::TYPE_INQUIRY => array_merge($common, [
                'subject' => 'required|string|max:255',
            ], $attachmentRules),
            Ticket::TYPE_COMPLAINT => array_merge($common, [
                'complaint_type' => 'required|string|max:100',
                'trip_reference' => 'nullable|string|max:100',
            ], $attachmentRules),
            Ticket::TYPE_TECHNICAL => array_merge($common, [
                'device_type' => 'required|string|in:android,ios,web',
            ], [
                'attachments' => 'nullable|array|max:3',
                'attachments.*' => 'file|mimes:jpeg,jpg,png|max:5120',
            ]),
            default => $common,
        };
    }

    private function validationMessages(): array
    {
        return [
            'email.required' => __('Please enter a valid email address so we can contact you.'),
            'email.email' => __('Please enter a valid email address so we can contact you.'),
        ];
    }

    private function storeAttachments(Request $request, Ticket $ticket): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        $path = public_path('uploads/support-tickets/' . date('Y/m'));
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true);
        }

        foreach ($request->file('attachments') as $index => $file) {
            if (!$file->isValid()) {
                continue;
            }

            $filename = 'attachment_' . time() . "_{$index}." . $file->getClientOriginalExtension();
            $file->move($path, $filename);

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'uploaded_by' => Ticket::SENDER_CUSTOMER,
                'file_path' => 'uploads/support-tickets/' . date('Y/m') . '/' . $filename,
                'file_type' => $file->getClientOriginalExtension(),
                'created_at' => now(),
            ]);
        }
    }
}
