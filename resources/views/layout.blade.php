<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand">Expense Tracker</a>
            <div>
                <button id="navigation-button" class="btn btn-primary me-2"></button>
                <button id="logout" class="btn btn-danger">Logout</button>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        @yield('content')
    </div>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const currentPath = window.location.pathname;
            if (currentPath === '/categories') {
                $('#navigation-button').text('Dashboard').click(function () {
                    window.location.href = '/dashboard';
                });
            } else {
                $('#navigation-button').text('Add Category').click(function () {
                    window.location.href = '/categories';
                });
            }

            $('#logout').click(function () {
                $.post('/api/logout', {}, function () {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                });
            });
        });
    </script>
</body>
</html>
