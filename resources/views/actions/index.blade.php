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
    $highlightFutureChecked = request()->has('highlight_future') ?? false;
    $showAll = $showAll ?? false;  // If passed from controller, or default false
    $currentLevel = request('current_level') ?? 0;

    $showImage = count(request()->all()) === 0 || request()->has('show_image');
    $showName = count(request()->all()) === 0 || request()->has('show_name');
    $showLevel = count(request()->all()) === 0 || request()->has('show_level');
    $showXp = count(request()->all()) === 0 || request()->has('show_xp');
    $showIngredients = count(request()->all()) === 0 || request()->has('show_ingredients');
    $showQuantity = count(request()->all()) === 0 || request()->has('show_quantity');
    $showBuy = count(request()->all()) === 0 || request()->has('show_buy');
    $showSell = count(request()->all()) === 0 || request()->has('show_sell');
    $showMargin = count(request()->all()) === 0 || request()->has('show_margin');
    $showMarginPercent = count(request()->all()) === 0 || request()->has('show_margin_percent');
    $showMembersOnly = count(request()->all()) === 0 || request()->has('show_members_only');

    $actionOptions = '';
    foreach ($actions as $action) {
        $actionOptions .= '<option value="' . $action->id . '">' . e($action->name) . '</option>';
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
    <script>
        const actionOptions = `{!! $actionOptions !!}`;
    </script>
    <h1>Cooking Calculator</h1>
    <p class="introText">A handy cooking calculator to plan your path to a specific level. Be sure to check out the user-submitted paths as well.</p>
    <div id="pathForm" class="formWrapper pathFormWrapper">
        <form method="POST" action="{{ route('paths.store') }}">
                @csrf
                <input type="hidden" name="return_to" id="returnToInput" value="">
                <button class="iconBtn closeFormBtn" type="button"><i class="bi bi-x-lg"></i></button>
                <h2>Path Creator</h2>
                <div class="pathName marg">
                    <label>Path Name</label>
                        <input class="inpt1" type="text" name="name" required>
                    </label>
                </div>
                <div id="path-steps">
                    <div class="step">
                        <label>What We Cooking?</label>
                        <select class="sel1" name="steps[0][action_id]" required>
                            @foreach ($actions as $action)
                                <option value="{{ $action->id }}">{{ $action->name }}</option>
                            @endforeach
                        </select>
                        <div class="levelWrap">
                            <label>Cooking Levels</label>
                            <div class="pathLevels marg">
                                <input class="inpt1 marg" type="number" placeholder="From" name="steps[0][level_from]" min="1" required>
                                <input class="inpt1 marg" type="number" placeholder="To" name="steps[0][level_to]" min="1" required>
                            </div>
                        </div>
                        <button type="button" class="remove-step iconBtn" aria-label="Remove step"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            <button class="btn1" type="button" id="add-step">Add another step</button>
            <button class="btn1" type="submit">Save Path</button>
        </form>
    </div>
    <div id="filtersForm" class="formWrapper {{ count(request()->all()) ? '' : 'visible' }}">
        <form method="GET" action="{{ route('actions.index') }}">
            @if(count(request()->all()))
                <button class="iconBtn closeFormBtn" type="button"><i class="bi bi-x-lg"></i></button>
            @endif

            <h2>Filters</h2>

            @if(!count(request()->all()))
                <p style="color: white; margin-top: 0.5em; margin-bottom: 1em;">
                    To get started, choose your current Level <i><span style="color: #778DA9;">or</span></i> XP <i><span style="color: #778DA9;">and</span></i> Goal. Or press "No Filters"
                </p>
            @endif

            <div class="levelXP">
                <button type="button" id="btn-level" class="mode-btn active btn1">Use Levels</button>
                <button type="button" id="btn-xp" class="mode-btn btn1">Use XP</button>
            </div>

            <div id="level-inputs">
                <div class="inputCG marg">
                    <label for="current_level">Current Level <span style="color: #F94144;">*</span></label>
                    <input class="inpt1" type="number" id="current_level" name="current_level" min="1" max="126" value="{{ request('current_level') }}" required>
                </div>
                <div class="inputCG marg">
                    <label for="goal_level">Goal Level <span style="color: #F94144;">*</span></label>
                    <input class="inpt1" type="number" id="goal_level" name="goal_level" min="1" max="126" value="{{ request('goal_level') }}"required>
                </div>
            </div>

            <div id="xp-inputs" style="display:none;">
                <div class="inputCG marg">
                    <label for="current_xp">Current XP <span style="color: #F94144;">*</span></label>
                    <input class="inpt1" type="number" id="current_xp" name="current_xp" min="0" value="{{ request('current_xp') }}"required>
                </div>
                <div class="inputCG marg">
                    <label for="goal_xp">Goal XP <span style="color: #F94144;">*</span></label>
                    <input class="inpt1" type="number" id="goal_xp" name="goal_xp" min="0" value="{{ request('goal_xp') }}"required>
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
                <h4>Level Visibility</h4>
                <label class="misc">
                    <input type="radio" name="filter_mode" value="can_do_now" {{ request('filter_mode') === 'can_do_now' ? 'checked' : '' }}>
                    Show Only Recipes you can currently cook
                </label>
            </div>
            <div class="inputCG">
                <label class="misc">
                    <input type="radio" name="filter_mode" value="highlight_future" {{ request('filter_mode') === 'highlight_future' ? 'checked' : '' }}>
                    Highlight upcoming recipes (up to goal level)
                </label>
            </div>
            <div class="inputCG">
                <label class="misc">
                    <input type="radio" name="filter_mode" value="" {{ request('filter_mode') === null ? 'checked' : '' }}>
                    Show all
                </label>
            </div>
            <div class="inputCG">
                <h4>Category</h4>
                <select class="sel1" name="category" id="category">
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
                    <label>
                        <input type="checkbox" name="show_image" {{ old('show_image', count(request()->all()) === 0 || request()->has('show_image')) ? 'checked' : '' }}>
                        Image
                    </label>
                    <label>
                        <input type="checkbox" name="show_name" {{ old('show_name', count(request()->all()) === 0 || request()->has('show_name')) ? 'checked' : '' }}>
                        Name
                    </label>
                    <label>
                        <input type="checkbox" name="show_level" {{ old('show_level', count(request()->all()) === 0 || request()->has('show_level')) ? 'checked' : '' }}>
                        Level
                    </label>
                    <label>
                        <input type="checkbox" name="show_xp" {{ old('show_xp', count(request()->all()) === 0 || request()->has('show_xp')) ? 'checked' : '' }}>
                        XP
                    </label>
                    <label>
                        <input type="checkbox" name="show_ingredients" {{ old('show_ingredients', count(request()->all()) === 0 || request()->has('show_ingredients')) ? 'checked' : '' }}>
                        Ingredients
                    </label>
                    <label>
                        <input type="checkbox" name="show_quantity" {{ old('show_quantity', count(request()->all()) === 0 || request()->has('show_quantity')) ? 'checked' : '' }}>
                        Quantity
                    </label>
                    <label>
                        <input type="checkbox" name="show_buy" {{ old('show_buy', count(request()->all()) === 0 || request()->has('show_buy')) ? 'checked' : '' }}>
                        Buy
                    </label>
                    <label>
                        <input type="checkbox" name="show_sell" {{ old('show_sell', count(request()->all()) === 0 || request()->has('show_sell')) ? 'checked' : '' }}>
                        Sell
                    </label>
                    <label>
                        <input type="checkbox" name="show_margin" {{ old('show_margin', count(request()->all()) === 0 || request()->has('show_margin')) ? 'checked' : '' }}>
                        Margin
                    </label>
                    <label>
                        <input type="checkbox" name="show_margin_percent" {{ old('show_margin_percent', count(request()->all()) === 0 || request()->has('show_margin_percent')) ? 'checked' : '' }}>
                        Margin %
                    </label>
                    <label>
                        <input type="checkbox" name="show_members_only" {{ old('show_members_only', count(request()->all()) === 0 || request()->has('show_members_only')) ? 'checked' : '' }}>
                        Members Only
                    </label>
                </div>
            </div>
            <div class="impButtons">
                <button class="filterSubmit btn1" type="submit" name="apply_filters" value="1">Apply</button>
                <button class="filterSubmit btn1" type="submit" name="show_all" value="1">No Filters</button>
            </div>
        </form>
    </div>
    <div class="contentWrap">
        <div class="blur-overlay" @if(!count(request()->all())) style="display: block;" @else style="filter: blur(0);" @endif>
            @if(count(request()->all()))
                <div class="helpButtons">
                    <button id="createUserPaths" class="btn1 toggleFormBtn" data-target="pathForm" style="display: inline-block;">Create a Path</button>
                    <a href="#userPathFeed" class="btn1" style="display: inline-block;">Browse Paths</a>
                    <button id="show-filters-btn" class="btn1 toggleFormBtn" data-target="filtersForm" style="display: inline-block;">Filters</button>
                </div>
            @endif
            <table id="actions-table" style="display: table;">
                <thead>
                    <tr>
                        <th class="{{ !$showImage ? 'hidden-column' : '' }}">Image</th>
                        <th class="{{ !$showName ? 'hidden-column' : '' }}">{!! sort_link('name', 'Name', $sort, $direction) !!}</th>
                        <th class="{{ !$showLevel ? 'hidden-column' : '' }}">{!! sort_link('level', 'Level', $sort, $direction) !!}</th>
                        <th class="{{ !$showXp ? 'hidden-column' : '' }}">{!! sort_link('xp', 'XP', $sort, $direction) !!}</th>
                        <th class="{{ !$showIngredients ? 'hidden-column' : '' }}">{!! sort_link('first_ingredient', 'Ingredients', $sort, $direction) !!}</th>
                        @if (!($showAll ?? false))
                            <th class="{{ !$showQuantity ? 'hidden-column' : '' }}">{!! sort_link('quantity_needed', 'Quantity', $sort, $direction) !!}</th>
                        @endif
                        <th class="{{ !$showBuy ? 'hidden-column' : '' }}">{!! sort_link('buy', 'Buy', $sort, $direction) !!}</th>
                        <th class="{{ !$showSell ? 'hidden-column' : '' }}">{!! sort_link('sell', 'Sell', $sort, $direction) !!}</th>
                        <th class="{{ !$showMargin ? 'hidden-column' : '' }}">{!! sort_link('margin', 'Margin', $sort, $direction) !!}</th>
                        <th class="{{ !$showMarginPercent ? 'hidden-column' : '' }}">{!! sort_link('margin_percent', 'Margin %', $sort, $direction) !!}</th>
                        <th class="{{ !$showMembersOnly ? 'hidden-column' : '' }}">{!! sort_link('members_only', 'Members Only', $sort, $direction) !!}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentLevel = request('current_level') ?? 0;
                        $goalLevel = request('goal_level') ?? 0;
                        $filterMode = request('filter_mode'); // values: 'can_do_now', 'highlight_future', or null

                        // Precompute for loop
                        $filterCanDo = $filterMode === 'can_do_now';
                        $filterHighlight = $filterMode === 'highlight_future';
                    @endphp

                    @foreach($actions as $action)
                        @php
                            $canDoNow = $action->level <= $currentLevel;
                            $withinGoal = $action->level <= $goalLevel;
                        @endphp

                        {{-- Filtering logic --}}
                        @if($filterCanDo && !$canDoNow)
                            @continue
                        @elseif($filterHighlight && !$withinGoal)
                            @continue
                        @endif

                        <tr>
                            <td class="{{ !$showImage ? 'hidden-column' : '' }}"><img src="{{ $action->image }}" alt="{{ $action->name }}"></td>
                            <td class="{{ !$showName ? 'hidden-column' : '' }}">{{ $action->name }}</td>
                            <td class="{{ !$showLevel ? 'hidden-column' : '' }}"
                                @if(!($showAll ?? false))
                                    @if(!$canDoNow && !$filterHighlight)
                                        style="background-color: #F94144;"
                                    @elseif($filterHighlight && !$canDoNow && $withinGoal)
                                        style="background-color: #F9C74F;"
                                    @endif
                                @endif
                            >
                                {{ $action->level }}
                            </td>
                            <td class="{{ !$showXp ? 'hidden-column' : '' }}">{{ rtrim(rtrim(number_format($action->xp, 2), '0'), '.') }}</td>
                            <td class="{{ !$showIngredients ? 'hidden-column' : '' }} ingreds">
                                @foreach($action->ingredients as $ingredient)
                                    <div>
                                        <img src="{{ $ingredient->image }}" alt="{{ $ingredient->name }}" style="height:20px; vertical-align:middle;">
                                        {{ $ingredient->name }} x{{ $ingredient->quantity }}
                                    </div>
                                @endforeach
                            </td>
                            @unless($showAll ?? false)
                                <td class="{{ !$showQuantity ? 'hidden-column' : '' }}">{{ $action->quantity_needed }}</td>
                            @endunless
                            <td class="{{ !$showBuy ? 'hidden-column' : '' }}" data-buy="{{ $action->buy }}">{{ number_format($action->buy, 2) }}</td>
                            <td class="{{ !$showSell ? 'hidden-column' : '' }}" data-sell="{{ $action->sell }}">{{ number_format($action->sell, 2) }}</td>
                            <td class="{{ !$showMargin ? 'hidden-column' : '' }} margin"></td>
                            <td class="{{ !$showMarginPercent ? 'hidden-column' : '' }} margin-percent"></td>
                            <td class="{{ !$showMembersOnly ? 'hidden-column' : '' }}">{{ $action->members_only ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button id="toggle-rows-btn" class="btn1" type="button" 
            @if(count(request()->all())) 
                style="display: flex;" 
            @else 
                style="display: none;" 
            @endif
        >
            Show more
        </button>
        <x-path-feed />
    </div>
    <script src="{{ asset('js/tablePages.js') }}" defer></script>
</body>
</html>
