<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('ご登録ありがとうございます！ご利用開始前に、お送りしたメール内のリンクをクリックしてメールアドレスの確認をお願いいたします。メールが届いていない場合は、再度お送りいたします。') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ __('新しい確認リンクがご登録いただいたメールアドレスに送信されました。') }}
        </div>
    @endif

    <div class="flex items-center justify-between mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('確認メールを再送信') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('ログアウト') }}
            </button>
        </form>
    </div>
</x-guest-layout>
