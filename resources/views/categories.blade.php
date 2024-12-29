@extends('layout')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h2 id="categoryFormTitle">Add Category</h2>
        <form id="addCategoryForm">
            <div class="mb-3">
                <label for="categoryName" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="categoryName" name="name" required>
            </div>
            <input type="hidden" id="categoryId">
            <button type="submit" class="btn btn-primary" id="categorySubmitButton">Add Category</button>
        </form>
    </div>
    <div class="col-md-6">
        <h2>Categories</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="categoryTableBody"></tbody>
        </table>
    </div>
</div>
<script>
    function loadCategories() {
        $.get('/api/categories', function (categories) {
            $('#categoryTableBody').html(categories.map(category => `
                <tr>
                    <td>${category.name}</td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-category" data-id="${category.id}" data-name="${category.name}">Edit</button>
                        <button class="btn btn-danger btn-sm delete-category" data-id="${category.id}">Delete</button>
                    </td>
                </tr>
            `));
        });
    }

    $('#addCategoryForm').submit(function (e) {
        e.preventDefault();
        const categoryId = $('#categoryId').val();
        const data = {
            name: $('#categoryName').val()
        };
        const url = categoryId ? `/api/categories/${categoryId}` : '/api/categories';
        const method = categoryId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            },
            data: data,
            success: function () {
                loadCategories();
                $('#addCategoryForm')[0].reset();
                $('#categoryId').val('');
                $('#categorySubmitButton').text('Add Category');
                $('#categoryFormTitle').text('Add Category');
                alert(categoryId ? 'Category updated successfully' : 'Category added successfully');
            },
            error: function () {
                alert('Failed to save category');
            }
        });
    });

    $(document).on('click', '.edit-category', function () {
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');
        $('#categoryName').val(categoryName);
        $('#categoryId').val(categoryId);
        $('#categorySubmitButton').text('Update Category');
        $('#categoryFormTitle').text('Edit Category');
    });

    $(document).on('click', '.delete-category', function () {
        const categoryId = $(this).data('id');

        $.ajax({
            url: `/api/categories/${categoryId}`,
            method: 'DELETE',
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            },
            success: function () {
                loadCategories();
                alert('Category deleted successfully');
            },
            error: function () {
                alert('Failed to delete category');
            }
        });
    });

    $(document).ready(function () {
        loadCategories();
    });
</script>
@endsection
