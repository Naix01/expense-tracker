@extends('layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Register</h3>
                </div>
                <div class="card-body p-4">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Already have an account? <a href="/login" class="text-primary">Login Here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#registerForm').submit(function (e) {
        e.preventDefault();
        $.post('/api/register', {
            name: $('#name').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            password_confirmation: $('#password_confirmation').val()
        }, function (response) {
            localStorage.setItem('token', response.access_token);
            // alert('Registration successful. Redirecting to dashboard...');
            window.location.href = '/dashboard';
        }).fail(function (error) {
            alert('Error: ' + (error.responseJSON.message || 'Registration failed'));
        });
    });
</script>
@endsection
