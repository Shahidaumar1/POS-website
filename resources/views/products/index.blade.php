<!-- <a href="{{ route('products.create') }}">Create New Product</a> -->
<table>
    <thead>
        <a href="{{ route('products.create') }}">Create New Product</a>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->stock_quantity }}</td>
                <td>{{ $product->category ? $product->category->name : 'No Category' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
