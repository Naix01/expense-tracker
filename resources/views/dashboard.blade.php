@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12 text-center">
            <h2>Expense Tracker Dashboard</h2>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-6 col-md-12 mb-4">
            <h3 class="mb-0">Add/Edit Expense</h3>
            <form id="expenseForm" class="bg-light p-4 rounded shadow-sm">
                <div id="expenseFormErrors" class="alert alert-danger d-none"></div>
                <input type="hidden" id="expenseId">
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select id="category" class="form-select" name="category_id" required>
                        <option value="">Select a category</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Enter description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="expenseSubmitButton">Add Expense</button>
            </form>
        </div>

        <div class="col-lg-6 col-md-12">
            <h3 class="mb-3">Expenses</h3>
            <form id="filterExpensesForm" class="d-flex flex-wrap bg-light p-3 rounded shadow-sm mb-3">
                <div class="me-2 mb-2">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="start_date">
                </div>
                <div class="me-2 mb-2">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate" name="end_date">
                </div>
                <button type="submit" class="btn btn-success mb-2">Filter</button>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="expenseTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-3">Expense Analytics</h3>
            <div class="bg-light p-4 rounded shadow-sm d-flex justify-content-center">
                <canvas id="expenseChart" style="max-width: 600px; max-height: 400px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function loadCategories() {
        $.get('/api/categories', function(categories) {
            $('#category').html(
                `<option value="">Select a category</option>` +
                categories.map(category => `<option value="${category.id}">${category.name}</option>`)
            );
        });
    }

    function loadExpenses(startDate = '', endDate = '') {
        let url = '/api/expenses';
        if (startDate && endDate) {
            url += `?start_date=${startDate}&end_date=${endDate}`;
        }

        $.get(url, function(expenses) {
            $('#expenseTableBody').html(
                expenses.map(expense => `
                    <tr>
                        <td>${expense.date}</td>
                        <td>${expense.category.name}</td>
                        <td>${expense.description}</td>
                        <td>${expense.amount}</td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-expense" data-id="${expense.id}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-expense" data-id="${expense.id}">Delete</button>
                        </td>
                    </tr>
                `)
            );
        }).fail(function() {
            $('#expenseTableBody').html('<tr><td colspan="5" class="text-center">No expenses found</td></tr>');
        });
    }

    function loadExpenseAnalytics() {
        $.get('/api/summary', function(data) {
            const labels = data.map(item => item.category.name);
            const amounts = data.map(item => item.total);

            const ctx = document.getElementById('expenseChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Expenses by Category',
                        data: amounts,
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d'],
                        borderColor: '#ffffff',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        tooltip: {
                            enabled: true,
                        }
                    }
                }
            });
        });
    }

    $(document).ready(function() {
        loadCategories();
        loadExpenses();
        loadExpenseAnalytics();

        $(document).on('click', '.edit-expense', function() {
            const id = $(this).data('id');
            $.get(`/api/expenses/${id}`, function(expense) {
                $('#expenseId').val(expense.id);
                $('#category').val(expense.category_id);
                $('#amount').val(expense.amount);
                $('#description').val(expense.description);
                $('#date').val(expense.date);
                $('#expenseSubmitButton').text('Update Expense');
            });
        });

        $(document).on('click', '.delete-expense', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this expense?')) {
                $.ajax({
                    url: `/api/expenses/${id}`,
                    type: 'DELETE',
                    success: function() {
                        loadExpenses();
                        loadExpenseAnalytics();
                    }
                });
            }
        });

        $('#filterExpensesForm').submit(function(e) {
            e.preventDefault();
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            loadExpenses(startDate, endDate);
            loadExpenseAnalytics();
        });

        $('#expenseForm').submit(function(e) {
            e.preventDefault();
            const id = $('#expenseId').val();
            const data = $(this).serialize();
            const url = id ? `/api/expenses/${id}` : '/api/expenses';
            const type = id ? 'PUT' : 'POST';
            $.ajax({
                url: url,
                type: type,
                data: data,
                success: function() {
                    loadExpenses();
                    loadExpenseAnalytics();
                    $('#expenseForm')[0].reset();
                    $('#expenseSubmitButton').text('Add Expense');
                },
                error: function(xhr) {
                    displayErrors('#expenseFormErrors', xhr.responseJSON.errors);
                }
            });
        });
    });
</script>
@endsection
