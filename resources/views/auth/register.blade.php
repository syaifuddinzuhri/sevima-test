@extends('layouts.auth')

@section('content')
    <div class="row auth-container vh-100 offset-lg-2">
        <div class="col-md-6 col-lg-5 p-0 auth-hero-container d-flex align-items-center justify-content-center flex-column">
            <img src="{{ asset('assets/img/auth-image.png') }}" class="hero-image" alt="">
        </div>

        <div class="col-md-6 col-lg-4 auth-form-container p-4 d-flex align-items-center justify-content-center">
            <div class="w-100 auth-form-box px-4">
                <h1 class="font-pacifico text-center mb-4">InstaApp</h1>
                <p class="mb-4 text-secondary text-center">Sign up to see photos and videos from your friends.</p>
                <form id="registerForm">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control rounded-pill" id="name" name="name"
                            placeholder="Enter your name" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control rounded-pill" id="username" name="username"
                            placeholder="Enter username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control rounded-pill" id="email" name="email"
                            placeholder="Enter email" required>
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control rounded-pill pr-5" id="password" name="password"
                            placeholder="Enter password" required>
                        <span class="toggle-password" data-target="#password">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control rounded-pill pr-5" id="password_confirmation"
                            name="password_confirmation" placeholder="Confirm password" required>
                        <span class="toggle-password" data-target="#password_confirmation">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill btn-glass">Sign up</button>
                    </div>

                    <div class="mt-3 text-center">
                        <p>Have an account? <a href="{{ route('login') }}">Log in</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.toggle-password').on('click', function() {
                const target = $($(this).data('target'));
                const icon = $(this).find('i');

                if (target.attr('type') === 'password') {
                    target.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    target.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                const email = $('#email').val();
                const name = $('#name').val();
                const password = $('#password').val();
                const username = $('#username').val();
                const password_confirmation = $('#password_confirmation').val();

                axios.post('{{ url('/api/auth/register') }}', {
                        name,
                        username,
                        email,
                        password,
                        password_confirmation
                    })
                    .then(function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Register succesfully',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        setTimeout(() => {
                            window.location.href = '/auth/login';
                        }, 1000);
                    })
                    .catch(function(error) {
                        const message = error.response?.data?.message || 'Login failed';
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
            });
        });
    </script>
@endsection
