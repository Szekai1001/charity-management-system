<x-guest-layout>
    <div class="px-4 py-2">

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="flex justify-center mb-6">
                <img src="{{ asset('image/logoBgRemove.png') }}" 
                     class="w-32 h-32 md:w-48 md:h-48 object-contain" 
                     alt="Logo">
            </div>

            <div class="text-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    Reset Password
                </h1>
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" 
                              class="block mt-1 w-full py-3 px-4" 
                              type="email" 
                              name="email" 
                              :value="old('email', $request->email)" 
                              required autofocus autocomplete="username" 
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
                              placeholder="New password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input id="password_confirmation" 
                              class="block mt-1 w-full py-3 px-4"
                              type="password"
                              name="password_confirmation" 
                              required autocomplete="new-password" 
                              placeholder="Confirm new password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center py-3 text-base">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>