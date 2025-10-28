<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'commentable_type' => 'required|string|in:project,task',
            'commentable_id' => 'required|integer',
            'body' => 'required|string|max:2000',
        ]);
        $this->authorize('create', Comment::class);

        $map = [
            'project' => \App\Models\Project::class,
            'task' => \App\Models\Task::class,
        ];
        $type = $map[$data['commentable_type']];
        $model = $type::findOrFail($data['commentable_id']);

        Comment::create([
            'user_id' => auth()->id(),
            'commentable_type' => $type,
            'commentable_id' => $model->id,
            'body' => $data['body'],
        ]);

        // Simple in-app notification to project owner/assignee if available
        try {
            if ($data['commentable_type'] === 'task') {
                $task = $model; $userId = $task->assigned_to ?: ($task->project->created_by ?? null);
                if ($userId) { \App\Models\Notification::create(['title'=>'New Comment','message'=>substr($data['body'],0,120),'type'=>'comment','user_id'=>$userId,'data'=>['task_id'=>$task->id]]); }
            } else {
                $project = $model; $userId = $project->created_by;
                if ($userId) { \App\Models\Notification::create(['title'=>'New Comment','message'=>substr($data['body'],0,120),'type'=>'comment','user_id'=>$userId,'data'=>['project_id'=>$project->id]]); }
            }
        } catch (\Throwable $e) {}

        return back()->with('success','Comment added');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return back()->with('success','Comment deleted');
    }
}

