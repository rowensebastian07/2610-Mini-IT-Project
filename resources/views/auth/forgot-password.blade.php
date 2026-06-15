<x-top-nav>
    
</x-top-nav>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/forgot.css') }}">
    <title>Forgot Password</title>
</head>

<body>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="center-container">
        
        <form method="POST" action="{{ route('password.email') }}" class="log-in-form centered">
            @csrf

            <h2 class="form-title">Forgot Password</h2>

            @if (session('status'))
                <div class="status-alert">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('email'))
                <div class="status-alert">
                    We have emailed your password reset link..
                </div>
            @endif

            <div class="form-box-category">
                <x-input-label for="email" :value="__('Email')" class="auth-lbl" />
                <x-text-input id="email" class="auth-input" type="email" name="email" :value="old('email')" required autofocus />
                
                </div>

            <button type="submit" class="button-auth">
                {{ __('Email Password Reset Link') }}
            </button>
        </form>

    </div>
</body>
</html>