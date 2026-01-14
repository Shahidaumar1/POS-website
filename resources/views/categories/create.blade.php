<h1>Create New Category</h1>
<form action="{{ route('categories.store') }}" method="POST">
    @csrf
    <label for="name">Category Name:</label>
    <input type="text" name="name" id="name" required>
    <button type="submit">Save Category</button>
</form>
