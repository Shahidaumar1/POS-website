@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Point of Sale (POS)</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Products Grid -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($products as $product)
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer p-4 border-2 border-transparent hover:border-blue-500" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                            <div class="text-2xl font-bold text-blue-600 mb-2">£{{ number_format($product->price, 2) }}</div>
                            <h3 class="font-bold text-gray-800 mb-1">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">Stock: {{ $product->stock_quantity }}</p>
                            @if($product->category)
                                <p class="text-xs text-gray-500">{{ $product->category->name }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="col-span-3 text-gray-500 text-center py-8">No products available</p>
                    @endforelse
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Shopping Cart</h2>
                    
                    <form method="POST" action="{{ route('sales.store') }}" id="checkoutForm">
                        @csrf
                        
                        <div id="cart-items" class="space-y-3 mb-4 max-h-80 overflow-y-auto">
                            <p class="text-gray-500 text-center text-sm">No items added yet</p>
                        </div>

                        <div class="border-t pt-4 mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-semibold">£<span id="subtotal">0.00</span></span>
                            </div>
                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-blue-600">£<span id="total">0.00</span></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method:</label>
                            <select name="payment_method" id="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="cash">💵 Cash</option>
                                <option value="card">💳 Card</option>
                                <option value="paypal">🅿️ PayPal</option>
                                <option value="stripe">Stripe</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition" id="checkoutBtn" disabled>
                            Checkout (0 items)
                        </button>
                        
                        <button type="button" onclick="clearCart()" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition mt-2">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = {};
    let products = {!! json_encode($products->keyBy('id')) !!};

    function addToCart(productId, productName, price) {
        if (cart[productId]) {
            cart[productId].quantity += 1;
        } else {
            cart[productId] = {
                id: productId,
                name: productName,
                price: price,
                quantity: 1
            };
        }
        updateCart();
    }

    function removeFromCart(productId) {
        delete cart[productId];
        updateCart();
    }

    function updateQuantity(productId, quantity) {
        if (quantity <= 0) {
            removeFromCart(productId);
        } else {
            cart[productId].quantity = parseInt(quantity);
            updateCart();
        }
    }

    function updateCart() {
        const cartItemsDiv = document.getElementById('cart-items');
        const checkoutBtn = document.getElementById('checkoutBtn');
        
        if (Object.keys(cart).length === 0) {
            cartItemsDiv.innerHTML = '<p class="text-gray-500 text-center text-sm">No items added yet</p>';
            checkoutBtn.disabled = true;
            checkoutBtn.textContent = 'Checkout (0 items)';
            document.getElementById('subtotal').textContent = '0.00';
            document.getElementById('total').textContent = '0.00';
            return;
        }

        let html = '';
        let subtotal = 0;
        let itemCount = 0;

        for (let productId in cart) {
            const item = cart[productId];
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            itemCount += item.quantity;

            html += `
                <div class="bg-gray-50 p-3 rounded flex justify-between items-center">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-900">${item.name}</p>
                        <p class="text-xs text-gray-600">£${item.price.toFixed(2)} × ${item.quantity}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">£${itemTotal.toFixed(2)}</p>
                        <input type="number" value="${item.quantity}" min="1" onchange="updateQuantity(${productId}, this.value)" class="w-12 mt-1 px-2 py-1 border border-gray-300 rounded text-xs">
                        <button type="button" onclick="removeFromCart(${productId})" class="block text-red-500 text-xs mt-1 hover:text-red-700">Remove</button>
                    </div>
                </div>
            `;
        }

        cartItemsDiv.innerHTML = html;
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('total').textContent = subtotal.toFixed(2);
        checkoutBtn.disabled = false;
        checkoutBtn.textContent = `Checkout (${itemCount} items)`;

        // Update hidden form fields
        updateFormData();
    }

    function updateFormData() {
        const checkoutForm = document.getElementById('checkoutForm');
        
        // Remove existing product inputs
        checkoutForm.querySelectorAll('input[name^="products"]').forEach(el => el.remove());

        // Add new product inputs
        let index = 0;
        for (let productId in cart) {
            const item = cart[productId];
            checkoutForm.innerHTML += `
                <input type="hidden" name="products[${index}][id]" value="${item.id}">
                <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
            `;
            index++;
        }
    }

    function clearCart() {
        if (confirm('Are you sure you want to clear the cart?')) {
            cart = {};
            updateCart();
        }
    }
</script>
@endsection
