@php
    // Generates a sorting link for a column, toggling between asc/desc/reset
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

         // Remove empty params from URL
        $url = route('actions.index', array_filter($params));

        return "<a href=\"$url\">$label $symbol</a>";
    }

    // Set basic request state
    $highlightFutureChecked = request()->has('highlight_future') ?? false;
    $showAll = $showAll ?? false;
    $currentLevel = request('current_level') ?? 0;

    // Determine which columns to show based on request or default
    $showImage = count(request()->all()) === 0 || request('show_image') === 'on';
    $showName = count(request()->all()) === 0 || request('show_name') === 'on';
    $showLevel = count(request()->all()) === 0 || request('show_level') === 'on';
    $showXp = count(request()->all()) === 0 || request('show_xp') === 'on';
    $showIngredients = count(request()->all()) === 0 || request('show_ingredients') === 'on';
    $showQuantity = count(request()->all()) === 0 || request('show_quantity') === 'on';
    $showBuy = count(request()->all()) === 0 || request('show_buy') === 'on';
    $showSell = count(request()->all()) === 0 || request('show_sell') === 'on';
    $showMargin = count(request()->all()) === 0 || request('show_margin') === 'on';
    $showMarginPercent = count(request()->all()) === 0 || request('show_margin_percent') === 'on';
    $showMembersOnly = count(request()->all()) === 0 || request('show_members_only') === 'on';

    // Generate HTML for action <select> dropdown
    $actionOptions = '';
    foreach ($actions as $action) {
        $actionOptions .= '<option value="' . $action->id . '">' . e($action->name) . '</option>';
    }

    // Check if XP-based filtering is being used
    $usingXp = request()->has('current_xp') || request()->has('goal_xp');
@endphp

<head>
    <title>OSRS Cooking Calculator</title>
    <meta charset="UTF-8">
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<x-header />
<body>
    <div class="bigWrap">
        <div id="pathForm" class="formWrapper pathFormWrapper">
            <form method="POST" action="{{ route('paths.store') }}" class="pathForm">
                    @csrf
                    <input type="hidden" name="return_to" id="returnToInput" value="">
                    <button class="iconBtn closeFormBtn" type="button"><i class="bi bi-x-lg"></i></button>
                    <h2>Path Creator</h2>
                    <div class="inputsWrap">
                        <div id="form-messages"></div>
                        <div class="inputCG marg">
                            <label>Path Name</label>
                            <input type="text" name="name" required>
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
                    </div>
                <button class="btn1" type="button" id="add-step">Add another step</button>
                <button class="btn1" type="submit">Save Path</button>
            </form>
        </div>
        <div id="filtersForm" class="formWrapper">
            <form id="filters" method="GET" action="{{ route('actions.index') }}">
                <button class="iconBtn closeFormBtn" type="button"><i class="bi bi-x-lg"></i></button>

                <h2>Filters</h2>
                <h3>Miscellaneous</h3>
                <div class="checkBoxen">
                    <label class="misc">
                        <input type="checkbox" name="f2p_only" {{ request('f2p_only') ? 'checked' : '' }}>
                        Free-to-play only
                    </label>
                    <label class="misc">
                        <input type="checkbox" name="only_profitable" {{ request('only_profitable') ? 'checked' : '' }}>
                        Show only profitable
                    </label>
                </div>
                <h4>Level Visibility</h4>
                <div class="checkBoxen">
                    <label class="misc">
                        <input type="radio" name="filter_mode" value="can_do_now" {{ request('filter_mode') === 'can_do_now' ? 'checked' : '' }}>
                        Show Only Recipes you can currently cook
                    </label>
                    <label class="misc">
                        <input type="radio" name="filter_mode" value="highlight_future" {{ request('filter_mode') === 'highlight_future' ? 'checked' : '' }}>
                        Highlight upcoming recipes (up to goal level)
                    </label>
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
                            <input type="hidden" name="show_image" value="off">
                            <input type="checkbox" name="show_image" value="on" {{ old('show_image', request('show_image') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Image
                        </label>
                        <label>
                            <input type="hidden" name="show_name" value="off">
                            <input type="checkbox" name="show_name" value="on" {{ old('show_name', request('show_name') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Name
                        </label>
                        <label>
                            <input type="hidden" name="show_level" value="off">
                            <input type="checkbox" name="show_level" value="on" {{ old('show_level', request('show_level') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Level
                        </label>
                        <label>
                            <input type="hidden" name="show_xp" value="off">
                            <input type="checkbox" name="show_xp" value="on" {{ old('show_xp', request('show_xp') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            XP
                        </label>
                        <label>
                            <input type="hidden" name="show_ingredients" value="off">
                            <input type="checkbox" name="show_ingredients" value="on" {{ old('show_ingredients', request('show_ingredients') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Ingredients
                        </label>
                        <label>
                            <input type="hidden" name="show_quantity" value="off">
                            <input type="checkbox" name="show_quantity" value="on" {{ old('show_quantity', request('show_quantity') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Quantity
                        </label>
                        <label>
                            <input type="hidden" name="show_buy" value="off">
                            <input type="checkbox" name="show_buy" value="on" {{ old('show_buy', request('show_buy') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Buy
                        </label>
                        <label>
                            <input type="hidden" name="show_sell" value="off">
                            <input type="checkbox" name="show_sell" value="on" {{ old('show_sell', request('show_sell') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Sell
                        </label>
                        <label>
                            <input type="hidden" name="show_margin" value="off">
                            <input type="checkbox" name="show_margin" value="on" {{ old('show_margin', request('show_margin') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Margin
                        </label>
                        <label>
                            <input type="hidden" name="show_margin_percent" value="off">
                            <input type="checkbox" name="show_margin_percent" value="on" {{ old('show_margin_percent', request('show_margin_percent') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Margin %
                        </label>
                        <label>
                            <input type="hidden" name="show_members_only" value="off">
                            <input type="checkbox" name="show_members_only" value="on" {{ old('show_members_only', request('show_members_only') === 'on' || count(request()->all()) === 0) ? 'checked' : '' }}>
                            Members Only
                        </label>
                    </div>
                </div>
                <div class="impButtons">
                    <button class="filterSubmit btn1" type="submit" name="apply_filters" value="1">Apply</button>
                    <button class="filterSubmit btn1" type="submit" name="reset_filters" value="1">Reset</button>
                </div>
            </form>
        </div>
        <div class="contentWrap">
            <div class="blur-overlay">
            <h1>Cooking Calculator</h1>
            <p class="introText">A handy cooking calculator to plan your path to a specific level. Be sure to check out the user-submitted paths as well.</p>
                <div class="helpButtons">
                    <div class="outsideFormWrap">
                        <form id="lvlxp">
                            <input type="hidden" name="mode" id="mode" value="{{ request('mode', 'level') }}">
                            <div >
                                <button type="button" id="btn-level" class="mode-btn active btn1">Use Levels</button>
                                <button type="button" id="btn-xp" class="mode-btn btn1">Use XP</button>
                            </div>
                            <div class="inpbtnWrap">
                                <div id="level-inputs" class="specDual">
                                    <input type="number" name="current_level" id="current_level" placeholder="From Level" value="{{ request('current_level') }}">
                                    <input type="number" name="goal_level" id="goal_level" placeholder="Goal Level" value="{{ request('goal_level') }}">
                                </div>
                                <div id="xp-inputs" class="specDual" style="display: none;">
                                    <input type="number" name="current_xp" id="current_xp" placeholder="From XP" value="{{ request('current_xp') }}">
                                    <input type="number" name="goal_xp" id="goal_xp" placeholder="Goal XP" value="{{ request('goal_xp') }}">
                                </div>
                                <button class="specDual btn1" type="submit" class="btn1">Apply</button>
                            </div>
                        </form>
                    </div>
                    <div class="buttonWrap">
                        @if(auth()->check())
                            <button class="btnClear" id="saveFilterBtn"><i class="bi bi-suit-heart-fill"></i></button>
                        @endif
                        <button id="createUserPaths" class="btn1 toggleFormBtn" data-target="pathForm" style="display: inline-block;">Create a Path</button>
                        <a href="#userPathFeed" class="btn1" style="display: inline-block;">Browse Paths</a>
                        <button id="show-filters-btn" class="btn1 toggleFormBtn" data-target="filtersForm" style="display: inline-block;">Filters</button>
                    </div>
                </div>
                <table id="actions-table" style="display: table;">
                    <thead>
                        <tr>
                            <th class="{{ !$showImage ? 'hidden-column' : '' }}">Image</th>
                            <th class="{{ !$showName ? 'hidden-column' : '' }}">{!! sort_link('name', 'Name', $sort, $direction) !!}</th>
                            <th class="{{ !$showLevel ? 'hidden-column' : '' }}">{!! sort_link('level', 'Level', $sort, $direction) !!}</th>
                            <th class="{{ !$showXp ? 'hidden-column' : '' }}">{!! sort_link('xp', 'XP', $sort, $direction) !!}</th>
                            <th class="{{ !$showIngredients ? 'hidden-column' : '' }}">{!! sort_link('first_ingredient', 'Ingredients', $sort, $direction) !!}</th>
                            @if (request()->has('apply_filters'))
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
                            $filterMode = request('filter_mode');

                            $filterCanDo = $filterMode === 'can_do_now';
                            $filterHighlight = $filterMode === 'highlight_future';
                        @endphp

                        @foreach($actions as $action)
                            @php
                                $canDoNow = $action->level <= $currentLevel;
                                $withinGoal = $action->level <= $goalLevel;
                            @endphp

                            @if($filterCanDo && !$canDoNow)
                                @continue
                            @elseif($filterHighlight && !$withinGoal)
                                @continue
                            @endif

                            <tr>
                                <td class="{{ !$showImage ? 'hidden-column' : '' }}"><img src="{{ $action->image }}" alt="{{ $action->name }}"></td>
                                <td class="{{ !$showName ? 'hidden-column' : '' }}">{{ $action->name }}</td>
                                <td class="{{ !$showLevel ? 'hidden-column' : '' }}"
                                @if (request()->has('apply_filters'))
                                    @if (!$action->can_do_now && !$action->filter_highlight)
                                        style="background-color: #F94144;"
                                    @elseif ($action->filter_highlight && !$action->can_do_now && $action->within_goal)
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
                                @isset($action)
                                    @if (request()->has('apply_filters'))
                                        <td class="{{ !$showQuantity ? 'hidden-column' : '' }}">{{ $action->quantity_needed }}</td>
                                    @endif
                                @endisset
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
    </div>
    <script src="{{ asset('js/tablePages.js') }}" defer></script>
    <script src="{{ asset('js/pathFuncs.js') }}" defer></script>
</body>