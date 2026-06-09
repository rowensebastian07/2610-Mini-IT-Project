<x-top-nav>
    
</x-top-nav>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <title>Reset Password</title>
</head>

<body>
    <div class="auth-decor">
        <h1>Reset Password</h1><br>
        <img src="/images/csrw-placeholder-1.jpeg" style="z-index: 999">
        <img src="/images/csrw-placeholder-2.jpeg" style="left:300; top:250;">
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="log-in-form">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-box-category">
            <x-input-label for="email" :value="__('Email')" class="auth-lbl" />
            <x-text-input id="email" class="auth-input" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="form-box-category">
            <x-input-label for="password" :value="__('New Password')" class="auth-lbl" />
            <x-text-input id="password" class="auth-input" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="form-box-category">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="auth-lbl" />
            <x-text-input id="password_confirmation" class="auth-input" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="button-auth">
            {{ __('Reset Password') }}
        </button>
    </form>
</body>
</html>