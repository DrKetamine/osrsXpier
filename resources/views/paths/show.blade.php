<head>
    <meta charset="UTF-8">
    <title>{{ $path->name }}</title>
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
</head>
<x-header />
<body>
    <div class="bigWrap">
        <h1>{{ $path->name }}</h1>
        <div class="dual section">
            <a class="btn1" href="{{ route('paths.edit', $path) }}">Edit</a>

            <form style="margin-bottom: 0;" method="POST" action="{{ route('paths.destroy', $path) }}" onsubmit="return confirm('Delete this path?')">
                @csrf
                @method('DELETE')
                <button class="btn1" type="submit">Delete</button>
            </form>
        </div>
        @if ($path->steps->count())
            <table id="actions-table" style="display: table;">
                <thead>
                    <tr>
                        <th class="{{ !$showImage ? 'hidden-column' : '' }}">Image</th>
                        <th class="{{ !$showName ? 'hidden-column' : '' }}">Name</th>
                        <th class="{{ !$showLevel ? 'hidden-column' : '' }}">Level</th>
                        <th class="{{ !$showXp ? 'hidden-column' : '' }}">XP</th>
                        <th class="{{ !$showIngredients ? 'hidden-column' : '' }}">Ingredients</th>
                        <th class="{{ !$showQuantity ? 'hidden-column' : '' }}">Quantity</th>
                        <th class="{{ !$showBuy ? 'hidden-column' : '' }}">Buy</th>
                        <th class="{{ !$showSell ? 'hidden-column' : '' }}">Sell</th>
                        <th class="{{ !$showMargin ? 'hidden-column' : '' }}">Margin</th>
                        <th class="{{ !$showMarginPercent ? 'hidden-column' : '' }}">Margin %</th>
                        <th class="{{ !$showMembersOnly ? 'hidden-column' : '' }}">Members Only</th>
                        <th>Step</th>
                        <th>From Level</th>
                        <th>To Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($path->steps as $step)
                    <tr>
                        <td class="{{ !$showImage ? 'hidden-column' : '' }}">
                            <img src="{{ $step->action->image }}" alt="{{ $step->action->name }}">
                        </td>
                        <td class="{{ !$showName ? 'hidden-column' : '' }}">{{ $step->action->name }}</td>
                        <td class="{{ !$showLevel ? 'hidden-column' : '' }}">{{ $step->action->level }}</td>
                        <td class="{{ !$showXp ? 'hidden-column' : '' }}">{{ rtrim(rtrim(number_format($step->action->xp, 2), '0'), '.') }}</td>
                        <td class="{{ !$showIngredients ? 'hidden-column' : '' }} ingreds">
                            @foreach($step->action->ingredients as $ingredient)
                                <div>
                                    <img src="{{ $ingredient->image }}" alt="{{ $ingredient->name }}" style="height:20px; vertical-align:middle;">
                                    {{ $ingredient->name }} x{{ $ingredient->quantity }}
                                </div>
                            @endforeach
                        </td>
                        <td class="{{ !$showQuantity ? 'hidden-column' : '' }}">{{ $step->action->quantity_needed }}</td>
                        <td class="{{ !$showBuy ? 'hidden-column' : '' }}" data-buy="{{ $step->action->buy }}">{{ number_format($step->action->buy, 2) }}</td>
                        <td class="{{ !$showSell ? 'hidden-column' : '' }}" data-sell="{{ $step->action->sell }}">{{ number_format($step->action->sell, 2) }}</td>
                        <td class="{{ !$showMargin ? 'hidden-column' : '' }} margin"></td>
                        <td class="{{ !$showMarginPercent ? 'hidden-column' : '' }} margin-percent"></td>
                        <td class="{{ !$showMembersOnly ? 'hidden-column' : '' }}">{{ $step->action->members_only ? 'Yes' : 'No' }}</td>
                        <td>{{ $step->step_order }}</td>
                        <td>{{ $step->level_from }}</td>
                        <td>{{ $step->level_to }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No steps in this path.</p>
        @endif
    </div>
</body>
</html>
