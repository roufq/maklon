<div class="card border-0 shadow-sm mt-4">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="mb-0">Comments</h6>
  </div>
  <div class="card-body">
    <form method="post" action="{{ route('comments.store') }}" class="mb-3">
      @csrf
      <input type="hidden" name="commentable_type" value="{{ $type }}">
      <input type="hidden" name="commentable_id" value="{{ $id }}">
      <div class="input-group">
        <input class="form-control" name="body" placeholder="Write a comment..." required>
        <button class="btn btn-primary">Post</button>
      </div>
    </form>
    <ul class="list-group list-group-flush">
      @forelse($comments as $c)
        <li class="list-group-item">
          <div class="d-flex justify-content-between">
            <div><strong>{{ $c->user->name }}</strong> <small class="text-muted">{{ $c->created_at->diffForHumans() }}</small></div>
            @if($c->user_id === auth()->id())
            <form method="post" action="{{ route('comments.destroy', $c) }}" onsubmit="return confirm('Delete comment?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
            @endif
          </div>
          <div class="mt-1">{{ $c->body }}</div>
        </li>
      @empty
        <li class="list-group-item text-muted">No comments yet</li>
      @endforelse
    </ul>
  </div>
  </div>

