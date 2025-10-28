<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index()
    {
        $attachments = Attachment::with(['uploader', 'attachable'])
                                ->latest()
                                ->paginate(15);

        return view('attachments.index', compact('attachments'));
    }

    public function create()
    {
        $projects = Project::all();
        $tasks = Task::all();

        return view('attachments.create', compact('projects', 'tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            // Strict whitelist (no svg/html/php)
            'file' => 'required|file|max:10240|mimetypes:'
                . implode(',', [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain',
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'
                ]),
            'attachable_type' => 'required|in:App\\Models\\Project,App\\Models\\Task',
            'attachable_id' => 'required|integer',
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = Attachment::create([
            'name' => $validated['name'] ?? $file->getClientOriginalName(),
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => $this->getFileType($file->getMimeType()),
            'uploaded_by' => Auth::id(),
            'attachable_type' => $validated['attachable_type'],
            'attachable_id' => $validated['attachable_id'],
        ]);

        return redirect()->back()
                        ->with('success', 'File uploaded successfully.');
    }

    public function show(Attachment $attachment)
    {
        $attachment->load(['uploader', 'attachable']);
        return view('attachments.show', compact('attachment'));
    }

    public function download(Attachment $attachment)
    {
        return Storage::download($attachment->path, $attachment->original_name);
    }

    public function destroy(Attachment $attachment)
    {
        $attachment->deleteFile();
        $attachment->delete();

        return redirect()->back()
                        ->with('success', 'Attachment deleted successfully.');
    }

    private function getFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (in_array($mimeType, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'document';
        } elseif (str_starts_with($mimeType, 'text/')) {
            return 'text';
        } else {
            return 'other';
        }
    }
}
