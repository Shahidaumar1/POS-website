@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Welcome to the Admin Dashboard</h1>

    <div>
        <h2>Manage Products</h2>
        <a href="{{ route('products.index') }}">View Products</a>
    </div>

    <div>
        <h2>Manage Sales</h2>
        <a href="{{ route('sales.index') }}">View Sales</a>
    </div>

    <div>
        <h2>Manage Users</h2>
        <a href="{{ route('users.index') }}">View Users</a>
    </div>
</div>
@endsection
