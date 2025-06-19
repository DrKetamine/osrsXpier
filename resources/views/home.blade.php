<!DOCTYPE html>
<head>
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
    <meta charset="UTF-8">
</head>
<x-header />
<head>
    <meta charset="UTF-8" />
    <title>My Project Home</title>
    <link rel="stylesheet" href="{{ asset('css/tablePages.css') }}">
</head>
<body>
    <div class="bigWrap">
        <h1>Welcome back OSRSxpier</h1>
        @auth
            @if (auth()->user()->savedFilters->count() > 0)
                <div id="userSavedFilters" class="pathFeed">
                    <h2 class="pathFeedTitle">Saved Filters</h2>
                    @forelse (auth()->user()->savedFilters as $filter)
                        <a href="{{ route('actions.index') }}?{{ $filter->params }}" class="pathCard">
                            <h3>{{ $filter->name }}</h3>
                            <p>Filter details</p>
                        </a>
                    @empty
                        <p>No saved filters found.</p>
                    @endforelse
                </div>
            @endif
        @endauth
    </div>
</body>
</html>
