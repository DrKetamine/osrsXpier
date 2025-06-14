@php
function sort_link($column, $title, $sort, $direction) {
    $dir = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
    $icon = '';
    if ($sort === $column) {
        $icon = $direction === 'asc' ? ' ↑' : ' ↓';
    }
    $url = request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
    return '<a href="' . e($url) . '">' . e($title) . $icon . '</a>';
}
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $path->name }}</title>
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
</head>
<body>
    <h1>{{ $path->name }}</h1>

    @if ($path->steps->count())
        <table id="actions-table" style="display: table;">
            <thead>
                <tr>
                    <th class="{{ !$showImage ? 'hidden-column' : '' }}">Image</th>
                    <th class="{{ !$showName ? 'hidden-column' : '' }}">{!! sort_link('name', 'Name', $sort, $direction) !!}</th>
                    <th class="{{ !$showLevel ? 'hidden-column' : '' }}">{!! sort_link('level', 'Level', $sort, $direction) !!}</th>
                    <th class="{{ !$showXp ? 'hidden-column' : '' }}">{!! sort_link('xp', 'XP', $sort, $direction) !!}</th>
                    <th class="{{ !$showIngredients ? 'hidden-column' : '' }}">{!! sort_link('first_ingredient', 'Ingredients', $sort, $direction) !!}</th>
                    <th class="{{ !$showQuantity ? 'hidden-column' : '' }}">{!! sort_link('quantity_needed', 'Quantity', $sort, $direction) !!}</th>
                    <th class="{{ !$showBuy ? 'hidden-column' : '' }}">{!! sort_link('buy', 'Buy', $sort, $direction) !!}</th>
                    <th class="{{ !$showSell ? 'hidden-column' : '' }}">{!! sort_link('sell', 'Sell', $sort, $direction) !!}</th>
                    <th class="{{ !$showMargin ? 'hidden-column' : '' }}">{!! sort_link('margin', 'Margin', $sort, $direction) !!}</th>
                    <th class="{{ !$showMarginPercent ? 'hidden-column' : '' }}">{!! sort_link('margin_percent', 'Margin %', $sort, $direction) !!}</th>
                    <th class="{{ !$showMembersOnly ? 'hidden-column' : '' }}">{!! sort_link('members_only', 'Members Only', $sort, $direction) !!}</th>
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
</body>
</html>
