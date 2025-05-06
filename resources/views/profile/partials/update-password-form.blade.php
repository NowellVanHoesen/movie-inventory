<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}"class="mt-6 space-y-6">
        @csrf
        @method('put')

        <x-form-field>
            <x-form-label for="update_password_current_password" :value="__('Current Password')" />
            <x-form-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-form-error name="current_password" />
        </x-form-field>

        <x-form-field>
            <x-form-label for="update_password_password" :value="__('New Password')" />
            <x-form-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-form-error name="password" />
        </x-form-field>

        <x-form-field>
            <x-form-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-form-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-form-error name="password_confirmation" />
        </x-form-field>

        <div class="flex items-center gap-4">
            <x-form-button>{{ __('Save') }}</x-form-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
