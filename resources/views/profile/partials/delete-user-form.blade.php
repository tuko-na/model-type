<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('アカウントを削除') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('アカウントを削除すると、そのリソースとデータはすべて永久に削除されます。アカウントを削除する前に、保持したいデータや情報をダウンロードしてください。') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('アカウントを削除') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('本当にアカウントを削除してもよろしいですか？') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('アカウントを削除すると、そのリソースとデータはすべて永久に削除されます。アカウントを削除することを確認するために、パスワードを入力してください。') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('パスワード') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-3/4 mt-1"
                    placeholder="{{ __('パスワード') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end mt-6">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('キャンセル') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('アカウントを削除') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
