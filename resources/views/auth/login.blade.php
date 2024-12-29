@extends('layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Login</h3>
                </div>
                <div class="card-body p-4">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <button onclick="window.location.href='/register'" class="btn btn-secondary w-100 mt-3">Register</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        $.post('/api/login', {
            email: $('#email').val(),
            password: $('#password').val()
        }, function(response) {
            localStorage.setItem('token', response.access_token);
            window.location.href = '/dashboard';
        }).fail(function() {
            alert('Invalid credentials. Please try again.');
        });
    });

    $(document).ready(function() {
        if (localStorage.getItem('token')) {
            $.ajax({
                url: '/api/user',
                method: 'GET',
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                },
                success: function() {
                    window.location.href = '/dashboard';
                },
                error: function() {
                    localStorage.removeItem('token');
                }
            });
        }
    });
</script>
@endsection