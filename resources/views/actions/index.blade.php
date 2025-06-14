@php
    function sort_link($column, $label, $sort, $direction) {
        if ($sort === $column && $direction === 'desc') {
            $nextDirection = 'asc';
            $symbol = '▼';
        } elseif ($sort === $column && $direction === 'asc') {
            $nextDirection = null; // Reset sorting
            $symbol = '▲';
        } else {
            $nextDirection = 'desc';
            $symbol = '';
        }

        $params = request()->all();
        $params['sort'] = $nextDirection ? $column : null;
        $params['direction'] = $nextDirection;

        // Remove empty params to avoid `?sort=&direction=` in URL
        $url = route('actions.index', array_filter($params));

        return "<a href=\"$url\">$label $symbol</a>";
    }
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>OSRS Cooking Calculator</title>
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <h1>Cooking Calculator</h1>
    <p class="introText">A handy cooking calculator to plan your path to a specific level. Be sure to check out the user-submitted paths as well.</p>
    <div class="formWrapper" 
        @if(count(request()->all()))
            style="display: none;"
        @else
            style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);"
        @endif
    >
        <form method="GET" action="{{ route('actions.index') }}">
            @if(count(request()->all()))
                <button class="iconBtn" type="button" id="close-filters-btn"><i class="bi bi-x-lg"></i></button>
            @endif

            <h2>Filters</h2>

            @if(!count(request()->all()))
                <p style="color: white; margin-top: 0.5em; margin-bottom: 1em;">
                    To get started, choose your current Level <i><span style="color: #778DA9;">or</span></i> XP <i><span style="color: #778DA9;">and</span></i> Goal. Or press "Show Now"
                </p>
            @endif

            <div class="levelXP">
                <button type="button" id="btn-level" class="mode-btn active btn1">Use Levels</button>
                <button type="button" id="btn-xp" class="mode-btn btn1">Use XP</button>
            </div>

            <div id="level-inputs">
                <div class="inputCG marg">
                    <label for="current_level">Current Level</label>
                    <input type="number" id="current_level" name="current_level" min="1" max="126" value="{{ request('current_level') }}" required>
                </div>
                <div class="inputCG marg">
                    <label for="goal_level">Goal Level</label>
                    <input type="number" id="goal_level" name="goal_level" min="1" max="126" value="{{ request('goal_level') }}"required>
                </div>
            </div>

            <div id="xp-inputs" style="display:none;">
                <div class="inputCG">
                    <label for="current_xp">Current XP</label>
                    <input type="number" id="current_xp" name="current_xp" min="0" value="{{ request('current_xp') }}"required>
                </div>
                <div class="inputCG">
                    <label for="goal_xp">Goal XP</label>
                    <input type="number" id="goal_xp" name="goal_xp" min="0" value="{{ request('goal_xp') }}"required>
                </div>
            </div>

            <hr style="margin: 1em 0;">

            <div class="inputCG">
                <h3>Miscellaneous</h3>
                <label class="misc">
                    <input type="checkbox" name="f2p_only" {{ request('f2p_only') ? 'checked' : '' }}>
                    Free-to-play only
                </label>
            </div>

            <div class="inputCG">
                <label class="misc">
                    <input type="checkbox" name="only_profitable" {{ request('only_profitable') ? 'checked' : '' }}>
                    Show only profitable
                </label>
            </div>

            <div class="inputCG">
                <h4>Category</h4>
                <select name="category" id="category">
                    <option value="">All</option>
                    <option value="fish" {{ request('category') == 'fish' ? 'selected' : '' }}>Fish</option>
                    <option value="baking" {{ request('category') == 'baking' ? 'selected' : '' }}>Baking</option>
                    <option value="gnome" {{ request('category') == 'gnome' ? 'selected' : '' }}>Gnome Food</option>
                    <option value="brewing" {{ request('category') == 'brewing' ? 'selected' : '' }}>Brewing</option>
                </select>
            </div>

            <hr style="margin: 1em 0;">

            <div class="inputCG columns">
                <h3>Columns to show</h3>
                <div class="column-checkboxes">
                    <label><input type="checkbox" name="show_name" checked> Name</label>
                    <label><input type="checkbox" name="show_level" checked> Level</label>
                    <label><input type="checkbox" name="show_xp" checked> XP</label>
                    <label><input type="checkbox" name="show_ingredients" checked> Ingredients</label>
                    <label><input type="checkbox" name="show_quantity" checked> Quantity</label>
                    <label><input type="checkbox" name="show_buy" checked> Buy</label>
                    <label><input type="checkbox" name="show_sell" checked> Sell</label>
                    <label><input type="checkbox" name="show_margin" checked> Margin</label>
                    <label><input type="checkbox" name="show_margin_percent" checked> Margin %</label>
                    <label><input type="checkbox" name="show_members_only" checked> Members Only</label>
                </div>
            </div>
            <div class="impButtons">
                <button class="filterSubmit btn1" type="submit" name="apply_filters" value="1">Apply</button>
                <button class="filterSubmit btn1" type="submit" name="show_all" value="1">Show Now</button>
            </div>
        </form>
    </div>
    <div class="contentWrap">
        <div class="blur-overlay" @if(!count(request()->all())) style="display: block;" @else style="filter: blur(0);" @endif>
            @if(count(request()->all()))
                <div class="helpButtons">
                    <button id="userPaths" class="btn1" style="display: inline-block;">User Paths</button>
                    <button id="show-filters-btn" class="btn1" style="display: inline-block;">Filters</button>
                </div>
            @endif
            <table id="actions-table" style="display: table;">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>{!! sort_link('name', 'Name', $sort, $direction) !!}</th>
                        <th>{!! sort_link('level', 'Level', $sort, $direction) !!}</th>
                        <th>{!! sort_link('xp', 'XP', $sort, $direction) !!}</th>
                        <th>{!! sort_link('first_ingredient', 'Ingredients', $sort, $direction) !!}</th>
                        @if (!($showAll ?? false))
                            <th>{!! sort_link('quantity_needed', 'Quantity', $sort, $direction) !!}</th>
                        @endif
                        <th>{!! sort_link('buy', 'Buy', $sort, $direction) !!}</th>
                        <th>{!! sort_link('sell', 'Sell', $sort, $direction) !!}</th>
                        <th>{!! sort_link('margin', 'Margin', $sort, $direction) !!}</th>
                        <th>{!! sort_link('margin_percent', 'Margin %', $sort, $direction) !!}</th>
                        <th>{!! sort_link('members_only', 'Members Only', $sort, $direction) !!}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($actions as $action)
                    <tr>
                        <td><img src="{{ $action->image }}" alt="{{ $action->name }}"></td>
                        <td>{{ $action->name }}</td>
                        <td>{{ $action->level }}</td>
                        <td>{{ rtrim(rtrim(number_format($action->xp, 2), '0'), '.') }}</td>
                        <td class="ingreds">
                            @foreach($action->ingredients as $ingredient)
                                <div>
                                    <img src="{{ $ingredient->image }}" alt="{{ $ingredient->name }}" style="height:20px; vertical-align:middle;">
                                    {{ $ingredient->name }} x{{ $ingredient->quantity }}
                                </div>
                            @endforeach
                        </td>
                        @if (!($showAll ?? false))
                            <td>{{ $action->quantity_needed }}</td>
                        @endif
                        <td data-buy="{{ $action->buy }}">{{ number_format($action->buy, 2) }}</td>
                        <td data-sell="{{ $action->sell }}">{{ number_format($action->sell, 2) }}</td>
                        <td class="margin"></td>
                        <td class="margin-percent"></td>
                        <td>{{ $action->members_only ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <button id="toggle-rows-btn" type="button" 
            @if(count(request()->all())) 
                style="display: inline-block;" 
            @else 
                style="display: none;" 
            @endif
        >
            Show more
        </button>
    </div>
    <script src="{{ asset('js/tablePages.js') }}" defer></script>
</body>
</html>
