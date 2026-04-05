{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - SERVEEEER</title>

    <!-- Vendor CSS -->
    <link href="{{ asset('lib/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/typicons.font/typicons.css') }}" rel="stylesheet">

    <!-- Azia CSS -->
    <link rel="stylesheet" href="{{ asset('css/azia.css') }}">
</head>
<body class="az-body">

<div class="az-signin-wrapper">
    <div class="az-card-signin">
        <h1 class="az-logo">SE<span>R</span>VEEER</h1>
        <div class="az-signin-header">
            <h2>Welcome back!</h2>
            <h4>Please sign in to continue</h4>

            {{-- Laravel Login Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email">{{ __('Email Address') }}</label>
                    <input id="email" type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email') }}" required autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" required>

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-az-primary btn-block">{{ __('Login') }}</button>

                <!-- Forgot Password -->
                @if (Route::has('password.request'))
                    <div class="text-center mt-2">
                       
                    </div>
                @endif
            </form>

        </div><!-- az-signin-header -->
    </div><!-- az-card-signin -->
</div><!-- az-signin-wrapper -->

<!-- Scripts -->
<script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('lib/ionicons/ionicons.js') }}"></script>
<script src="{{ asset('js/azia.js') }}"></script>

</body>
</html>