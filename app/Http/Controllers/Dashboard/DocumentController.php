<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\DocumentUpdatedMail;
use App\Models\Document;
use App\Models\DocumentUpdateLog;
use App\Models\PlatformEmailLog;
use App\Models\User;
use App\Services\ActionsLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $documents = Document::orderByDesc('date-of-edit')->orderByDesc('id')->get();

        return view('dashboard.documents.index', compact('documents'));
    }

    public function download(Document $document): BinaryFileResponse
    {
        $path = public_path($document->getRawOriginal('file-link'));

        if (!File::exists($path)) {
            abort(404, __('Sorry, the link is invalid or the document does not exist.'));
        }

        return response()->download($path, basename($path));
    }

    public function replace(Request $request, Document $document)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $oldLink = $document->getRawOriginal('file-link');
        $path = public_path('uploads/documents');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true);
        }

        $file = $request->file('file');
        $filename = 'document_' . $document->id . '_' . time() . '.pdf';
        $file->move($path, $filename);
        $newLink = 'uploads/documents/' . $filename;

        $document->update([
            'file-link' => $newLink,
            'date-of-edit' => now(),
        ]);

        DocumentUpdateLog::create([
            'assigned_from_employee_id' => auth()->guard('admin')->id(),
            'document_id' => $document->id,
            'document_link_old' => $oldLink,
            'document_link_new' => $newLink,
            'status' => 'change',
            'created_at' => now(),
        ]);

        $this->actionsLog->logEdit('document', $document->id, [
            'file-link' => $oldLink,
        ]);

        $this->notifyUsersAboutDocument($document, public_path($newLink));

        return redirect()
            ->route('documents.index')
            ->with('success', __('Document replaced successfully.'));
    }

    private function notifyUsersAboutDocument(Document $document, string $attachmentPath): void
    {
        $employeeId = auth()->guard('admin')->id();
        $failedCount = 0;

        User::whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('id')
            ->chunk(100, function ($users) use ($document, $attachmentPath, $employeeId, &$failedCount) {
                foreach ($users as $user) {
                    try {
                        Mail::to($user->email)->send(new DocumentUpdatedMail($document, $user));

                        PlatformEmailLog::create([
                            'assigned_from_employee_id' => $employeeId,
                            'user_id' => $user->id,
                            'driver_id' => $user->id,
                            'driver_email' => $user->email,
                            'email_type' => 'update_document',
                            'status' => 'sent',
                            'error_message' => null,
                        ]);
                    } catch (\Throwable $e) {
                        $failedCount++;
                        Log::error('Document update email failed', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ]);

                        PlatformEmailLog::create([
                            'assigned_from_employee_id' => $employeeId,
                            'user_id' => $user->id,
                            'driver_id' => $user->id,
                            'driver_email' => $user->email,
                            'email_type' => 'update_document',
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                    }
                }
            });

        if ($failedCount > 0) {
            session()->flash('warning', __('Document was updated but some notification emails failed to send.'));
        }
    }
}
