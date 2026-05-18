<x-top-nav>
  
</x-top-nav>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <title>Log In</title>
</head>

<body>
    <div class="auth-decor">
        <h1>MMU Clubs & Societies</h1><br>
        <img src="images/csrw-placeholder-1.jpeg" style="z-index: 999">
        <img src="images/csrw-placeholder-2.jpeg" style="left:300; top:250;">
    </div>
    

    <form method="POST" action="{{ route('login') }}" class="log-in-form">
        @csrf

        <!-- Email Address -->
        <div class="form-box-category">
            <x-input-label for="email" :value="__('Email')" class="auth-lbl"/><br>
            <x-text-input id="email" class="block mt-1 w-full first-box" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" class="auth-input" />
            
        </div>

        <!-- Password -->
        <div class="mt-4 form-box-category">
            <x-input-label for="password" :value="__('Password')" class="auth-lbl" /><br>

            <x-text-input id="password" class="block mt-1 w-full second-box auth-input"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            />
                            
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="remember-forgot-pass">
            <div class="block mt-4 remember-me-box">
                <label for="remember_me" class="inline-flex items-center">
                    <input class="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember" class="auth-input">
                    
                </label>
                <span class="remember-text">{{ __('Remember me') }}</span>
            </div>

            <div class="flex items-center justify-end mt-4 forgot-pass">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}<br>
                    </a>
                @endif
                </div>

        </div>
            <x-primary-button class="ms-3 button-auth">
                {{ __('Log in') }}
            </x-primary-button>
        
    </form>
</body>
</html>