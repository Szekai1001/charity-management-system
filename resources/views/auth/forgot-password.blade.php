<x-guest-layout>
    <div class="px-4 py-2">
        
        <div class="text-center mb-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Forgot Your Password?
            </h1>
        </div>

        <div class="mb-6 text-sm md:text-base text-gray-600 text-center leading-relaxed">
            {{ __('Enter your email address below and we\'ll send you a secure link to reset your password.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" 
                              class="block mt-1 w-full py-3 px-4" 
                              type="email" 
                              name="email" 
                              :value="old('email')" 
                              required autofocus 
                              placeholder="e.g. john@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center py-3 text-base">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-900 hover:underline">
                    <i class="bi bi-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>