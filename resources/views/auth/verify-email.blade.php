<x-guest-layout>
    <div class="px-4 py-2">

        <div class="flex justify-center mb-6">
            <img src="{{ asset('image/logoBgRemove.png') }}" 
                 class="w-32 h-32 md:w-48 md:h-48 object-contain" 
                 alt="Logo">
        </div>

        <div class="text-center mb-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Verify Your Email
            </h1>
        </div>

        <div class="mb-4 text-sm md:text-base text-gray-600 text-center leading-relaxed">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 font-medium text-sm text-green-600 text-center bg-green-50 p-3 rounded-lg border border-green-200">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            
            <form method="POST" action="{{ route('verification.send') }}" class="w-full md:w-auto">
                @csrf
                <x-primary-button class="w-full justify-center py-3 md:w-auto">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full md:w-auto text-center md:text-right">
                @csrf
                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 py-2">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>

    </div>
</x-guest-layout>