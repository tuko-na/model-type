<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('製品詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- 型番 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('型番') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->model_number }}</p>
                        </div>

                        <!-- 製品名 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('製品名') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->name }}</p>
                        </div>

                        <!-- メーカー -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('メーカー') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->manufacturer }}</p>
                        </div>

                        <!-- カテゴリ -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('カテゴリ') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->category }}</p>
                        </div>

                        <!-- 購入日 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('購入日') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->purchase_date }}</p>
                        </div>

                        <!-- 購入状態 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('購入状態') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->purchase_condition }}</p>
                        </div>

                        <!-- ステータス -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('ステータス') }}</h3>
                            <p class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span @class([
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    'bg-green-100 text-green-800' => $product->status === 'active',
                                    'bg-gray-100 text-gray-800' => $product->status === 'in_storage',
                                    'bg-yellow-100 text-yellow-800' => $product->status === 'in_repair',
                                    'bg-red-100 text-red-800' => $product->status === 'disposed',
                                ])>
                                    {{ match ($product->status) {
                                        'active' => '稼働中',
                                        'in_storage' => '保管中',
                                        'in_repair' => '修理中',
                                        'disposed' => '廃棄済み',
                                        default => $product->status,
                                    } }}
                                </span>
                            </p>
                        </div>

                        <!-- 備考 -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('備考') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->notes ?? '---' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('products.index') }}" class="mr-4">
                            <x-secondary-button>
                                {{ __('一覧に戻る') }}
                            </x-secondary-button>
                        </a>
                        {{-- 将来の編集・削除ボタン --}}
                        {{--
                        <a href="{{ route('products.edit', $product) }}" class="mr-4">
                            <x-primary-button>
                                {{ __('編集') }}
                            </x-primary-button>
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">
                                {{ __('削除') }}
                            </x-danger-button>
                        </form>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
