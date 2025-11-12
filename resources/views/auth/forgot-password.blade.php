<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('パスワードをお忘れですか？ご安心ください。メールアドレスをお知らせいただければ、新しいパスワードを設定できるリセットリンクをメールでお送りします。') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('メールアドレス')" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('パスワードリセットリンクを送信') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
