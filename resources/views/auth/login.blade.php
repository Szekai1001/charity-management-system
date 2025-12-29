<x-guest-layout>
    <div class="px-4 py-2">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class=" login-logo flex justify-center mb-6">
                <img src="{{ asset('image/logoBgRemove.png') }}" class="w-32 h-32 md:w-48 md:h-48 object-contain" alt="Logo">
            </div>

            <div class="text-center text-3xl font-bold">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Login to Your Account</h1>
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full py-3 px-4" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="e.g. john@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full py-3 px-4"
                    type="password"
                    name="password"
                    required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex flex-col md:flex-row justify-between items-center mt-4 gap-4 md:gap-0">

                <label for="remember_me" class="inline-flex items-center self-start md:self-auto">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
                @endif
            </div>
            <div class="mt-6">
                <x-primary-button class="w-full justify-center py-3 text-lg">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
    </div>
    </form>
    <div class="mt-6 text-center">
        <p class="text-gray-600">Don't have an account? <a href="{{ route('register') }}" class="text-primary">Register here</a></p>
    </div>
    </div>
</x-guest-layout>