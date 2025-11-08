@props([
    'name' => 'confirm-product-deletion',
    'title' => '製品の削除',
    'message' => '本当にこの製品を削除しますか？この操作は取り消せません。',
])

<x-modal :name="$name" focusable>
    <div x-data="{ action: '' }" x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') { action = $event.detail.action; }">
        <form method="post" :action="action" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ $title }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ $message }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('キャンセル') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('削除する') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</x-modal>
