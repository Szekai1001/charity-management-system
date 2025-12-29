<x-guest-layout>
    <div class="px-4 py-2">

        <div class="text-center mb-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Confirm Password
            </h1>
        </div>

        <div class="mb-6 text-sm md:text-base text-gray-600 text-center leading-relaxed">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" 
                              class="block mt-1 w-full py-3 px-4"
                              type="password"
                              name="password"
                              required autocomplete="current-password"
                              placeholder="Enter your password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center py-3 text-base">
                    {{ __('Confirm') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>