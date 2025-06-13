<!DOCTYPE html>
<html>
<head>
    <title>Actions List</title>
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
</head>
<body>
    <h1>Actions</h1>
    <form method="GET" action="{{ route('actions.index') }}">
        <div>
            <button type="button" id="btn-level" class="mode-btn active">Use Levels</button>
            <button type="button" id="btn-xp" class="mode-btn">Use XP</button>
        </div>
        <div id="level-inputs">
            <div>
                <label for="current_level">Current Level</label>
                <input type="number" id="current_level" name="current_level" min="1" max="126" value="{{ request('current_level') }}">
            </div>

            <div>
                <label for="goal_level">Goal Level</label>
                <input type="number" id="goal_level" name="goal_level" min="1" max="126" value="{{ request('goal_level') }}">
            </div>
        </div>

        <div id="xp-inputs" style="display:none;">
            <div>
                <label for="current_xp">Current XP</label>
                <input type="number" id="current_xp" name="current_xp" min="0" value="{{ request('current_xp') }}">
            </div>

            <div>
                <label for="goal_xp">Goal XP</label>
                <input type="number" id="goal_xp" name="goal_xp" min="0" value="{{ request('goal_xp') }}">
            </div>
        </div>

        <button type="submit">Apply</button>
    </form>
    <table>
        <tr>
            <th>Image</th>
            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                Name
                @if($sort === 'name')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>

            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'level', 'direction' => ($sort === 'level' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                Level
                @if($sort === 'level')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>

            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'xp', 'direction' => ($sort === 'xp' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                XP
                @if($sort === 'xp')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>

            <th>Ingredients</th>

            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'quantity', 'direction' => ($sort === 'quantity' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                Quantity
                @if($sort === 'quantity')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>

            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'buy', 'direction' => ($sort === 'buy' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                Buy
                @if($sort === 'buy')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>

            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'sell', 'direction' => ($sort === 'sell' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                Sell
                @if($sort === 'sell')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>

            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'margin', 'direction' => ($sort === 'margin' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                Margin
                @if($sort === 'margin')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>

            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'margin_percent', 'direction' => ($sort === 'margin_percent' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                Margin %
                @if($sort === 'margin_percent')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>

            <th>
            <a href="{{ route('actions.index', array_merge(request()->all(), ['sort' => 'members_only', 'direction' => ($sort === 'members_only' && $direction === 'asc') ? 'desc' : 'asc'])) }}">
                Members Only
                @if($sort === 'members_only')
                {{ $direction === 'asc' ? '▲' : '▼' }}
                @endif
            </a>
            </th>
        </tr>
        @foreach($actions as $action)
            <tr>
                <td><img src="{{ $action->image }}" alt="{{ $action->name }}"></td>
                <td>{{ $action->name }}</td>
                <td>{{ $action->level }}</td>
                <td>{{ $action->xp }}</td>
                <td class="ingreds">
                    @foreach($action->ingredients as $ingredient)
                        <div>
                            <img src="{{ $ingredient->image }}" alt="{{ $ingredient->name }}" style="height:20px; vertical-align:middle;">
                            {{ $ingredient->name }} x{{ $ingredient->quantity }}
                        </div>
                    @endforeach
                </td>
                <td>{{ $action->times_needed ?? '-' }}</td>
                <td data-buy="{{ $action->buy }}">{{ number_format($action->buy, 2) }}</td>
                <td data-sell="{{ $action->sell }}">{{ number_format($action->sell, 2) }}</td>
                <td class="margin"></td>
                <td class="margin-percent"></td>
                <td>{{ $action->members_only ? 'Yes' : 'No' }}</td>
            </tr>
        @endforeach
    </table>
    <script src="{{ asset('js/tablePages.js') }}" defer></script>
</body>
</html>
