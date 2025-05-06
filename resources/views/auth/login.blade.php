<x-layout>
    <x-slot:heading>
        Log In
    </x-slot:heading>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-12 max-w-xl mx-auto">
            <div class="border-b border-gray-900/10 pb-12">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-4">

                    <!-- Email Address -->
                    <x-form-field>
                        <x-form-label for="email" :value="__('Email')" />

                        <div class="mt-2">
                            <x-form-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            <x-form-error name="email" />
                        </div>
                    </x-form-field>

                    <!-- Password -->
                    <x-form-field>
                        <x-form-label for="password" :value="__('Password')" />

                        <div class="mt-2">
                            <x-form-input id="password" type="password" name="password" required autocomplete="current-password" />

                            <x-form-error name="password" />
                        </div>
                    </x-form-field>

                    <!-- Remember Me -->
                    <x-form-field>
                        <x-form-label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-gray-50">{{ __('Remember me') }}</span>
                        </x-form-label>
                    </x-form-field>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-6 gap-x-6 max-w-xl mx-auto">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-50 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-form-button>{{ __('Log in') }}</x-form-button>
        </div>
    </form>
</x-layout>
