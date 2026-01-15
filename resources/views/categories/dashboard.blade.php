<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Categories Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 28px;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .category-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .category-header h2 {
            font-size: 22px;
            font-weight: bold;
        }
        
        .category-stats {
            display: flex;
            gap: 20px;
            font-size: 14px;
        }
        
        .category-stats span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .products-table thead {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        
        .products-table th {
            padding: 15px;
            text-align: left;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .products-table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .products-table tr:hover {
            background: #f8f9fa;
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
        }
        
        .stock-low {
            color: #dc3545;
            font-weight: bold;
        }
        
        .stock-ok {
            color: #28a745;
        }
        
        .action-links {
            display: flex;
            gap: 8px;
        }
        
        .action-links button,
        .action-links a {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .edit-btn {
            background: #17a2b8;
            color: white;
        }
        
        .edit-btn:hover {
            background: #138496;
        }
        
        .sell-btn {
            background: #28a745;
            color: white;
        }
        
        .sell-btn:hover {
            background: #218838;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
        
        .no-products {
            text-align: center;
            padding: 30px;
            color: #999;
        }
        
        .admin-only {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .permission-badge {
            display: inline-block;
            background: #ffc107;
            color: #333;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>📂 Categories Overview</h1>
                <p style="color: #666; font-size: 14px;">Welcome, {{ auth()->user()->name }} 
                    @if(auth()->user()->isAdmin())
                        <span class="permission-badge">👑 Admin</span>
                    @endif
                </p>
            </div>
            <div class="header-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Back to Products</a>
                <a href="{{ route('pos.create') }}" class="btn btn-primary">🛒 New Sale</a>
                @if(auth()->user()->isAdmin())
                    <button onclick="openAddCategoryModal()" class="btn btn-primary">➕ Add Category</button>
                @endif
            </div>
        </div>
        
        <!-- Categories with Products -->
        @if($categories->isEmpty())
            <div class="category-section">
                <div class="no-products">
                    <p>No categories found. 
                        @if(auth()->user()->isAdmin())
                            <button onclick="openAddCategoryModal()" style="background: #667eea; color: white; border: none; padding: 5px 15px; border-radius: 3px; cursor: pointer;">Create a category</button>
                        @endif
                    </p>
                </div>
            </div>
        @else
            @foreach($categories as $category)
                <div class="category-section">
                    <div class="category-header">
                        <div>
                            <h2>{{ $category->name }}</h2>
                        </div>
                        <div class="category-stats">
                            <span>📦 Products: <strong>{{ $category->products->count() }}</strong></span>
                            <span>📊 Total Stock: <strong>{{ $category->products->sum('stock_quantity') }}</strong></span>
                            <span>🏆 Total Sold: <strong>{{ $category->products->sum('total_sold') }}</strong></span>
                            @if(auth()->user()->isAdmin())
                                <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}')" class="edit-btn">✏️ Edit</button>
                            @endif
                        </div>
                    </div>
                    
                    @if($category->products->isEmpty())
                        <div class="no-products">
                            <p>No products in this category</p>
                        </div>
                    @else
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Current Stock</th>
                                    <th>Total Sold</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->products as $product)
                                    <tr>
                                        <td class="product-name">{{ $product->name }}</td>
                                        <td>
                                            <span class="@if($product->stock_quantity < 5) stock-low @else stock-ok @endif">
                                                {{ $product->stock_quantity }} units
                                                @if($product->stock_quantity < 5)
                                                    <span style="color: #dc3545;">⚠️ Low</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $product->total_sold }} units</td>
                                        <td>£{{ number_format($product->price, 2) }}</td>
                                        <td>
                                            <div class="action-links">
                                                @if(auth()->user()->isAdmin())
                                                    <button onclick="editProduct({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock_quantity }})" class="edit-btn">✏️ Edit</button>
                                                @else
                                                    <button class="edit-btn admin-only" title="Only admins can edit">✏️ Edit</button>
                                                @endif
                                                <a href="{{ route('pos.create') }}" class="sell-btn">🛍️ Sell</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
    
    <!-- Edit Category Modal -->
    <div id="editCategoryModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.6);">
        <div style="background-color:white; margin:auto; padding:30px; border-radius:10px; width:90%; max-width:500px; position:relative; top:50%; transform:translateY(-50%);">
            <h2>Edit Category</h2>
            <input type="text" id="categoryName" placeholder="Category Name" style="width:100%; padding:10px; margin:15px 0; border:1px solid #ddd; border-radius:5px;">
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button onclick="closeEditCategoryModal()" class="btn btn-secondary">Cancel</button>
                <button onclick="saveCategory()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
    
    <!-- Add Category Modal -->
    <div id="addCategoryModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.6);">
        <div style="background-color:white; margin:auto; padding:30px; border-radius:10px; width:90%; max-width:500px; position:relative; top:50%; transform:translateY(-50%);">
            <h2>Add New Category</h2>
            <input type="text" id="newCategoryName" placeholder="Category Name" style="width:100%; padding:10px; margin:15px 0; border:1px solid #ddd; border-radius:5px;">
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button onclick="closeAddCategoryModal()" class="btn btn-secondary">Cancel</button>
                <button onclick="addNewCategory()" class="btn btn-primary">Add</button>
            </div>
        </div>
    </div>
    
    <script>
        let currentCategoryId = null;
        
        function openAddCategoryModal() {
            document.getElementById('addCategoryModal').style.display = 'flex';
        }
        
        function closeAddCategoryModal() {
            document.getElementById('addCategoryModal').style.display = 'none';
        }
        
        function editCategory(id, name) {
            currentCategoryId = id;
            document.getElementById('categoryName').value = name;
            document.getElementById('editCategoryModal').style.display = 'flex';
        }
        
        function closeEditCategoryModal() {
            document.getElementById('editCategoryModal').style.display = 'none';
            currentCategoryId = null;
        }
        
        function editProduct(id, name, price, stock) {
            alert('Edit product functionality - Coming soon!');
        }
        
        function saveCategory() {
            const name = document.getElementById('categoryName').value;
            if (!name) {
                alert('Please enter category name');
                return;
            }
            
            fetch(`/categories/${currentCategoryId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name: name })
            })
            .then(() => {
                location.reload();
            });
        }
        
        function addNewCategory() {
            const name = document.getElementById('newCategoryName').value;
            if (!name) {
                alert('Please enter category name');
                return;
            }
            
            fetch('/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name: name })
            })
            .then(() => {
                location.reload();
            });
        }
    </script>
</body>
</html>
