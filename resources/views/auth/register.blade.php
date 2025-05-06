<x-layout>
    <x-slot:heading>
        Register
    </x-slot:heading>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="space-y-12 max-w-xl mx-auto">
            <div class="border-b border-gray-900/10 pb-12">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-4">
                    <!-- Name -->
                    <x-form-field>
                        <x-form-label for="name" :value="__('Name')" />
                        <x-form-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-form-error name="name" />
                    </x-form-field>
                    <!-- Email Address -->
                    <x-form-field>
                        <x-form-label for="email" :value="__('Email')" />
                        <x-form-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-form-error name="email" />
                    </x-form-field>
                    <!-- Password -->
                    <x-form-field>
                        <x-form-label for="password" :value="__('Password')" />
                        <x-form-input id="password" type="password" name="password" required autocomplete="new-password" />
                        <x-form-error name="password" />
                    </x-form-field>
                    <!-- Confirm Password -->
                    <x-form-field>
                        <x-form-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-form-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-form-error name="password_confirmation" />
                    </x-form-field>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-6 gap-x-6 max-w-xl mx-auto">
            <a class="underline text-sm text-gray-50 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-form-button>{{ __('Register') }}</x-form-button>
        </div>
    </form>
</x-layout>
