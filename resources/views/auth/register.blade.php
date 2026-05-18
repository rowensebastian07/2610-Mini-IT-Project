<x-top-nav>
  
</x-top-nav>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <title>Sign Up</title>
</head>

<body>
    
    <form method="POST" action="{{ route('register') }}" class="register-form">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="auth-lbl" />
            <x-text-input class="auth-input" id="name"  type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-register" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="auth-lbl" />
            <x-text-input class="auth-input" id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-register" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="auth-lbl" />

            <x-text-input id="password" class="block mt-1 w-full" class="auth-input"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-register" />
            
        </div>

        <!-- Confirm Password -->
        <div class="mt-4 confirm-pass">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="auth-lbl"/>

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password" class="auth-input"
                            name="password_confirmation" required autocomplete="new-password" />

        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-register" id="confirm-pass-error" />
        </div>

        
        
        
        

        <div class="flex items-center justify-end mt-4 already-register">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            
        </div>
        <x-primary-button class="ms-4 button-auth">
                {{ __('Register') }}
            </x-primary-button>
    </form>
</body>
</html>
