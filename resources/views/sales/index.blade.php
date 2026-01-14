<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales List</title>
</head>
<body>
    <h1>Sales List</h1>

    <table>
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>User</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->user->name }}</td>
                    <td>{{ $sale->total_amount }}</td>
                    <td>{{ $sale->payment_method }}</td>
                    <td>
                        <a href="#">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @foreach($sales as $sale)
    <p>Sale ID: {{ $sale->id }} | Total: {{ $sale->total_amount }} | Payment Method: {{ $sale->payment_method }}</p>
@endforeach

</body>
</html>
