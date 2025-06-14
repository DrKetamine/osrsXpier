<div id="userPathFeed" class="pathFeed">
    <h2 class="pathFeedTitle">User Paths</h2>
    @forelse ($paths as $path)
        <a href="{{ route('actions.path.show', $path->id) }}" class="pathCard">
            <h3>{{ $path->name }}</h3>
            <p>{{ $path->steps_count }} steps</p>
        </a>
    @empty
        <p>No paths found.</p>
    @endforelse
</div>