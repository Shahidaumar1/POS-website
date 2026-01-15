<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sales List</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="min-h-screen bg-gradient-to-br from-blue-600 to-purple-700 p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">📈 Sales List</h1>
                    <p class="text-gray-600 mt-1">All transactions and sales history</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard') }}" class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-bold py-2 px-4 rounded-lg transition whitespace-nowrap text-sm">← Back to Dashboard</a>
                    <a href="{{ route('pos.create') }}" class="border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white font-bold py-2 px-4 rounded-lg transition whitespace-nowrap text-sm">🛒 New Sale</a>
                </div>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            @if($sales->isEmpty())
                <p class="text-gray-500 text-center py-8">No sales found. <a href="{{ route('pos.create') }}" class="text-blue-600 hover:underline">Create a new sale</a></p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Sale ID</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">User</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Total Amount</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Payment Method</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Date</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-800 font-bold">#{{ $sale->id }}</td>
                                    <td class="py-3 px-4 text-gray-800">{{ $sale->user->name }}</td>
                                    <td class="py-3 px-4 text-gray-800 font-bold">£{{ number_format($sale->total_amount, 2) }}</td>
                                    <td class="py-3 px-4">
                                        @if($sale->payment_method === 'cash')
                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">💵 Cash</span>
                                        @elseif($sale->payment_method === 'card')
                                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">💳 Card</span>
                                        @elseif($sale->payment_method === 'paypal')
                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">🅿️ PayPal</span>
                                        @elseif($sale->payment_method === 'stripe')
                                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">Stripe</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('sales.show', $sale->id) }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-3 py-2 rounded text-sm inline-block font-semibold transition">📋 Receipt</a>
                                            <button onclick="deleteSale({{ $sale->id }})" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-3 py-2 rounded text-sm font-semibold transition">🗑️ Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function deleteSale(saleId) {
        if (confirm('Are you sure you want to delete this sale? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/sales/${saleId}`;
            form.innerHTML = `@csrf
            @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
</body>
</html>
