<head>
    <meta charset="UTF-8">
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
</head>
<x-header />
<div class="bigWrap">
    <h1>Hello {{ Auth::user()->name }}!</h1>

    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ url('/admin/audit-logs/download') }}" class="btn btn-primary">Download CSV</a>
        @endif
    @endauth
    <div id="userPathFeed" class="pathFeed">
        <h2 class="pathFeedTitle">Your Paths</h2>
        @forelse ($paths as $path)
            <a href="{{ route('actions.path.show', $path->id) }}" class="pathCard">
                <h3>{{ $path->name }}</h3>
                <p>{{ $path->steps_count }} steps</p>
            </a>
        @empty
            <p>You haven't created any paths yet.</p>
        @endforelse
    </div>
</div>
