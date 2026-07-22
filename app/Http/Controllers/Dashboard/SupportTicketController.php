<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\TicketAssignedMail;
use App\Mail\TicketClosedMail;
use App\Mail\TicketReplyMail;
use App\Models\Admin;
use App\Models\PlatformEmailLog;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportTicketController extends Controller
{
    public function index(Request $request, string $page)
    {
        $type = $this->resolveType($page);

        try {
            $tickets = Ticket::query()
                ->with('assignedEmployee')
                ->ofType($type)
                ->filterAdmin($request->only([
                    'status',
                    'email',
                    'ticket_number',
                    'date_from',
                    'date_to',
                ]))
                ->sortAdmin($request->input('sort', 'newest'))
                ->paginate(20)
                ->withQueryString();
        } catch (\Throwable $e) {
            Log::error('Failed to load support tickets: ' . $e->getMessage(), [
                'page' => $page,
            ]);

            return redirect()
                ->route('dashboard.index')
                ->with('error', __('Failed to load tickets. Please try again.'));
        }

        return view('dashboard.support_tickets.index', [
            'tickets' => $tickets,
            'page' => $page,
            'pageTitle' => Ticket::pageTypeLabel($page),
            'statuses' => Ticket::filterStatuses(),
            'filters' => $request->only([
                'status',
                'email',
                'ticket_number',
                'date_from',
                'date_to',
                'sort',
            ]),
        ]);
    }

    public function show(string $page, Ticket $ticket)
    {
        $this->assertTicketBelongsToPage($page, $ticket);

        $ticket->load([
            'replies.employee',
            'attachments',
            'assignedEmployee',
            'statusLogs.changedBy',
        ]);

        $employees = Admin::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'type']);

        return view('dashboard.support_tickets.show', [
            'ticket' => $ticket,
            'page' => $page,
            'pageTitle' => Ticket::pageTypeLabel($page),
            'employees' => $employees,
        ]);
    }

    public function reply(Request $request, string $page, Ticket $ticket)
    {
        $this->assertTicketBelongsToPage($page, $ticket);

        if ($ticket->isClosed()) {
            return redirect()
                ->route('support-tickets.show', [$page, $ticket])
                ->with('error', __('Cannot reply to a closed ticket.'));
        }

        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
        ]);

        $employee = auth()->guard('admin')->user();

        try {
            DB::beginTransaction();

            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'sender_type' => Ticket::SENDER_EMPLOYEE,
                'employee_id' => $employee->id,
                'message' => $request->input('message'),
                'created_at' => now(),
            ]);

            $this->storeReplyAttachments($request, $ticket);

            if ($ticket->status === Ticket::STATUS_NEW) {
                $ticket->changeStatus(Ticket::STATUS_IN_PROGRESS, $employee->id);
            } else {
                $ticket->touch();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to save ticket reply: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
            ]);

            return redirect()
                ->route('support-tickets.show', [$page, $ticket])
                ->with('error', __('Failed to save the reply. Please try again.'));
        }

        $this->sendTicketEmail(
            $ticket->customer_email,
            new TicketReplyMail($ticket->fresh(), $reply),
            'ticket_reply'
        );

        return redirect()
            ->route('support-tickets.show', [$page, $ticket])
            ->with('success', __('Reply sent successfully.'));
    }

    public function assign(Request $request, string $page, Ticket $ticket)
    {
        $this->assertTicketBelongsToPage($page, $ticket);

        $request->validate([
            'assigned_employee_id' => ['required', 'exists:admins,id'],
        ]);

        $assignee = Admin::query()
            ->where('is_active', 1)
            ->findOrFail($request->input('assigned_employee_id'));

        $assignedBy = auth()->guard('admin')->user();

        try {
            $ticket->update([
                'assigned_employee_id' => $assignee->id,
            ]);

            if ($ticket->status === Ticket::STATUS_NEW) {
                $ticket->changeStatus(Ticket::STATUS_IN_PROGRESS, $assignedBy->id);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to assign ticket: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
            ]);

            return redirect()
                ->route('support-tickets.show', [$page, $ticket])
                ->with('error', __('Failed to assign the ticket. Please try again.'));
        }

        if ($assignee->email) {
            $this->sendTicketEmail(
                $assignee->email,
                new TicketAssignedMail($ticket->fresh(), $assignee, $assignedBy),
                'ticket_assigned'
            );
        }

        return redirect()
            ->route('support-tickets.show', [$page, $ticket])
            ->with('success', __('Ticket assigned successfully.'));
    }

    public function close(string $page, Ticket $ticket)
    {
        $this->assertTicketBelongsToPage($page, $ticket);

        if ($ticket->isClosed()) {
            return redirect()
                ->route('support-tickets.show', [$page, $ticket])
                ->with('error', __('Ticket is already closed.'));
        }

        if (!$ticket->hasEmployeeReply()) {
            return redirect()
                ->route('support-tickets.show', [$page, $ticket])
                ->with('error', __('Cannot close the ticket before sending a reply.'));
        }

        $employee = auth()->guard('admin')->user();

        try {
            $ticket->changeStatus(Ticket::STATUS_CLOSED, $employee->id);
        } catch (\Throwable $e) {
            Log::error('Failed to close ticket: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
            ]);

            return redirect()
                ->route('support-tickets.show', [$page, $ticket])
                ->with('error', __('Failed to close the ticket. Please try again.'));
        }

        $this->sendTicketEmail(
            $ticket->customer_email,
            new TicketClosedMail($ticket->fresh()),
            'ticket_closed'
        );

        return redirect()
            ->route('support-tickets.show', [$page, $ticket])
            ->with('success', __('Ticket closed successfully.'));
    }

    private function resolveType(string $page): string
    {
        $map = Ticket::pageTypeMap();

        abort_unless(isset($map[$page]), 404);

        return $map[$page];
    }

    private function assertTicketBelongsToPage(string $page, Ticket $ticket): void
    {
        $type = $this->resolveType($page);

        if ($ticket->ticket_type !== $type) {
            abort(404);
        }
    }

    private function storeReplyAttachments(Request $request, Ticket $ticket): void
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

            $extension = $file->getClientOriginalExtension();
            $filename = 'reply_' . time() . "_{$index}." . $extension;
            $file->move($path, $filename);

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'uploaded_by' => Ticket::SENDER_EMPLOYEE,
                'file_path' => 'uploads/support-tickets/' . date('Y/m') . '/' . $filename,
                'file_type' => $extension,
                'created_at' => now(),
            ]);
        }
    }

    private function sendTicketEmail(string $email, $mailable, string $emailType): void
    {
        try {
            Mail::to($email)->send($mailable);

            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => null,
                'driver_email' => $email,
                'email_type' => $emailType,
                'status' => 'sent',
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Ticket email failed: ' . $e->getMessage(), [
                'email_type' => $emailType,
                'email' => $email,
            ]);

            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => null,
                'driver_email' => $email,
                'email_type' => $emailType,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
