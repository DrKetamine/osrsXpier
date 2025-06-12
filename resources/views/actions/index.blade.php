<!DOCTYPE html>
<html>
<head>
    <title>Actions List</title>
    <style>
        img { width: 50px; height: auto; }
        table, th, td { border: 1px solid #ccc; border-collapse: collapse; padding: 5px; }
    </style>
</head>
<body>
    <h1>Actions</h1>
    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Level</th>
            <th>XP</th>
            <th>Quantity</th>
            <th>Buy</th>
            <th>Sell</th>
            <th>Margin</th>
            <th>Margin %</th>
            <th>Members Only</th>
        </tr>
        @foreach($actions as $action)
            <tr>
                <td><img src="{{ $action->image }}" alt="{{ $action->name }}"></td>
                <td>{{ $action->name }}</td>
                <td>{{ $action->level }}</td>
                <td>{{ $action->xp }}</td>
                <td>{{ $action->quantity }}</td>
                <td>{{ $action->buy }}</td>
                <td>{{ $action->sell }}</td>
                <td>{{ $action->margin }}</td>
                <td>{{ $action->margin_percent }}</td>
                <td>{{ $action->members_only ? 'Yes' : 'No' }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
