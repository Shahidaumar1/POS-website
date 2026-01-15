<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="min-h-screen bg-gradient-to-br from-blue-600 to-purple-700 p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">📊 POS Dashboard</h1>
                    <p class="text-gray-600 mt-1">Welcome, {{ auth()->user()->name }}</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('pos.create') }}" class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-bold py-2 px-4 rounded-lg transition whitespace-nowrap text-sm">🛒 New Sale</a>
                    <a href="{{ route('sales.index') }}" class="border-2 border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white font-bold py-2 px-4 rounded-lg transition whitespace-nowrap text-sm">📈 View Sales</a>
                    <a href="{{ route('categories.dashboard') }}" class="border-2 border-purple-600 text-purple-600 hover:bg-purple-600 hover:text-white font-bold py-2 px-4 rounded-lg transition whitespace-nowrap text-sm">📂 Categories</a>
                    <button onclick="openAddProductModal()" class="border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white font-bold py-2 px-4 rounded-lg transition whitespace-nowrap text-sm">➕ Add Product</button>
                    <button onclick="openAddCategoryModal()" class="border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white font-bold py-2 px-4 rounded-lg transition whitespace-nowrap text-sm">🏷️ Add Category</button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <h3 class="text-gray-600 text-sm font-semibold uppercase">Total Products</h3>
                <p class="text-4xl font-bold text-blue-600 mt-2">{{ $products->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <h3 class="text-gray-600 text-sm font-semibold uppercase">Total Stock</h3>
                <p class="text-4xl font-bold text-green-600 mt-2">{{ $products->sum('stock_quantity') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <h3 class="text-gray-600 text-sm font-semibold uppercase">Total Sold</h3>
                <p class="text-4xl font-bold text-purple-600 mt-2">{{ $products->sum('total_sold') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <h3 class="text-gray-600 text-sm font-semibold uppercase">Low Stock Items</h3>
                <p class="text-4xl font-bold text-red-600 mt-2">{{ $products->where('stock_quantity', '<', 5)->count() }}</p>
            </div>
        </div>

        <!-- Inventory Management -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">📦 Inventory Management</h2>
                <input type="text" id="searchBox" placeholder="Search products..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            @if($products->isEmpty())
                <p class="text-gray-500 text-center py-8">No products found. <button onclick="openAddProductModal()" class="text-blue-600 hover:underline">Add a new product</button></p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Product Name</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Category</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Current Stock</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Total Sold</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Price</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @foreach($products as $product)
                                <tr class="product-row border-b border-gray-200 hover:bg-gray-50">
                                    <td class="product-name py-3 px-4 text-gray-800">{{ $product->name }}</td>
                                    <td class="py-3 px-4 text-gray-800">{{ $product->category->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="@if($product->stock_quantity < 5) text-red-600 font-bold @else text-green-600 @endif">
                                            {{ $product->stock_quantity }} units
                                            @if($product->stock_quantity < 5) ⚠️ @endif
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-800">{{ $product->total_sold }} units</td>
                                    <td class="py-3 px-4">
                                        <span class="price-display" data-product-id="{{ $product->id }}">£{{ number_format($product->price, 2) }}</span>
                                        <input type="hidden" class="price-input" data-product-id="{{ $product->id }}" value="{{ $product->price }}">
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <button onclick="openEditProductModal({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock_quantity }}, {{ $product->category_id }})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">✏️ Edit</button>
                                            <button onclick="deleteProduct({{ $product->id }})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">🗑️ Delete</button>
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

<!-- Edit Product Modal -->
<div id="editProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 w-96">
        <h2 class="text-2xl font-bold mb-6">Edit Product</h2>
        <form id="editProductForm">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Product Name</label>
                <input type="text" id="productName" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Price (£)</label>
                <input type="number" id="productPrice" name="price" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Stock Quantity</label>
                <input type="number" id="productStock" name="stock_quantity" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">Save Changes</button>
                <button type="button" onclick="closeEditProductModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 w-96">
        <h2 class="text-2xl font-bold mb-6">Add New Product</h2>
        <form id="addProductForm" method="POST" action="{{ route('products.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Product Name</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Price (£)</label>
                <input type="number" name="price" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Stock Quantity</label>
                <input type="number" name="stock_quantity" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Category</option>
                    @php
                        $categories = \App\Models\Category::all();
                    @endphp
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Barcode (Optional)</label>
                <input type="text" name="barcode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter or scan barcode">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">Add Product</button>
                <button type="button" onclick="closeAddProductModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 w-96">
        <h2 class="text-2xl font-bold mb-6">Add New Category</h2>
        <form id="addCategoryForm" method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Category Name</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Description (Optional)</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">Add Category</button>
                <button type="button" onclick="closeAddCategoryModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('searchBox').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#productsTableBody tr').forEach(row => {
            const productName = row.querySelector('.product-name').textContent.toLowerCase();
            row.style.display = productName.includes(filter) ? '' : 'none';
        });
    });

    // Modal functions
    function openEditProductModal(productId, name, price, stock, categoryId) {
        document.getElementById('productName').value = name;
        document.getElementById('productPrice').value = price;
        document.getElementById('productStock').value = stock;
        const form = document.getElementById('editProductForm');
        form.action = `/products/${productId}`;
        document.getElementById('editProductModal').classList.remove('hidden');
    }

    function closeEditProductModal() {
        document.getElementById('editProductModal').classList.add('hidden');
    }

    function openAddProductModal() {
        document.getElementById('addProductModal').classList.remove('hidden');
    }

    function closeAddProductModal() {
        document.getElementById('addProductModal').classList.add('hidden');
    }

    function openAddCategoryModal() {
        document.getElementById('addCategoryModal').classList.remove('hidden');
    }

    function closeAddCategoryModal() {
        document.getElementById('addCategoryModal').classList.add('hidden');
    }

    // Delete product
    function deleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/products/${productId}`;
            form.innerHTML = `@csrf
            @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const editModal = document.getElementById('editProductModal');
        const addModal = document.getElementById('addProductModal');
        const catModal = document.getElementById('addCategoryModal');
        if (event.target == editModal) editModal.classList.add('hidden');
        if (event.target == addModal) addModal.classList.add('hidden');
        if (event.target == catModal) catModal.classList.add('hidden');
    }

    // Handle form submission for edit
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const productId = this.action.split('/').pop();
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-HTTP-Method-Override': 'PUT'
            },
            body: JSON.stringify({
                name: document.getElementById('productName').value,
                price: document.getElementById('productPrice').value,
                stock_quantity: document.getElementById('productStock').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product updated successfully!');
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>
</div>
</body>
</html>
