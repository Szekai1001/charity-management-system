<x-guest-layout>
    <div class="px-4 py-2">

        <form method="POST" action="{{ route('register') }}" class="login-form">
            @csrf
            
            <div class="login-logo flex justify-center mb-6">
                <img src="{{ asset('image/logoBgRemove.png') }}" 
                     class="w-32 h-32 md:w-48 md:h-48 object-contain" 
                     alt="Logo">
            </div>

            <div class="text-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    Register Your Account
                </h1>
            </div>

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" 
                              class="block mt-1 w-full py-3 px-4" 
                              type="text" 
                              name="name" 
                              :value="old('name')" 
                              required autofocus autocomplete="name" 
                              placeholder="e.g. John Doe" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" 
                              class="block mt-1 w-full py-3 px-4" 
                              type="email" 
                              name="email" 
                              :value="old('email')" 
                              required autocomplete="username" 
                              placeholder="e.g. john@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" 
                              class="block mt-1 w-full py-3 px-4"
                              type="password"
                              name="password"
                              required autocomplete="new-password"
                              placeholder="At least 8 characters" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input id="password_confirmation" 
                              class="block mt-1 w-full py-3 px-4"
                              type="password"
                              name="password_confirmation" 
                              required autocomplete="new-password"
                              placeholder="Same as above" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="mt-8 flex flex-col-reverse gap-4 md:flex-row md:items-center md:justify-end">
                
                <a class="text-sm text-gray-600 hover:text-gray-900 text-center md:text-left underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                   href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="w-full md:w-auto justify-center py-3 text-lg md:text-base ms-0 md:ms-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>