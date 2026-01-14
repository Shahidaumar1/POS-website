<form method="POST" action="{{ route('sales.store') }}">
    @csrf
    <label for="product_id">Select Product:</label>
    <select name="products[0][id]" id="product_id">
        <option value="">Select Product</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    </select>
    
    <label for="quantity">Quantity:</label>
    <input type="number" name="products[0][quantity]" id="quantity" required>

    <label for="payment_method">Payment Method:</label>
    <select name="payment_method" id="payment_method">
        <option value="cash">Cash</option>
        <option value="card">Card</option>
    </select>
    
    <button type="submit">Checkout</button>
</form>
