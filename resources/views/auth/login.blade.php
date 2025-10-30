@extends('layouts.auth')

@section('content')
    <div class="row auth-container vh-100 offset-lg-2">
        <div class="col-md-6 col-lg-5 p-0 auth-hero-container d-flex align-items-center justify-content-center flex-column">
            <img src="{{ asset('assets/img/auth-image.png') }}" class="hero-image" alt="">
        </div>

        <div class="col-md-6 col-lg-4 auth-form-container p-4 d-flex align-items-center justify-content-center">
            <div class="w-100 auth-form-box px-4">
                <h1 class="font-pacifico text-center mb-4">InstaApp</h1>

                <form id="loginForm">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control rounded-pill" id="email" name="email"
                            placeholder="Enter email">
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control rounded-pill pr-5" id="password" name="password"
                            placeholder="Enter password">
                        <span id="togglePassword" class="toggle-password">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>


                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill btn-glass">Log in</button>
                    </div>

                    <div class="mt-3 text-center">
                        <p>Don't have an account? <a href="{{ route('register') }}">Sign Up</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        });

        $('#loginForm').on('submit', function(e) {
            e.preventDefault();

            const email = $('#email').val();
            const password = $('#password').val();

            axios.post('{{ url('/api/auth/login') }}', {
                    email,
                    password
                })
                .then(function(response) {
                    localStorage.setItem('auth_token', response.data.data.token);
                    window.location.href = '/';
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
    </script>
@endsection
