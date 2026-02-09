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
                    @if (session('success'))
                        <div class="mb-4 text-sm font-medium text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif
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

                        <!-- ジャンル -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('ジャンル') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->genre_name ?? '---' }}</p>
                        </div>

                        <!-- 楽天リンク -->
                        @if($product->rakuten_url)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('楽天リンク') }}</h3>
                            <a 
                                href="{{ $product->rakuten_url }}" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 mt-1 px-3 py-2 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                楽天で見る
                            </a>
                        </div>
                        @endif

                        <!-- 購入日 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('購入日') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->purchase_date }}</p>
                        </div>

                        <!-- 保証終了日 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('保証終了日') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $product->warranty_expires_on ?? '---' }}</p>
                        </div>

                        <!-- 購入金額 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('購入金額') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ isset($product->price) ? number_format($product->price) . ' 円' : '---' }}</p>
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

                        <a href="{{ route('incidents.create', ['product_id' => $product->id]) }}" class="mr-4">
                            <x-primary-button>
                                {{ __('インシデントを登録') }}
                            </x-primary-button>
                        </a>

                        <a href="{{ route('products.edit', $product) }}" class="mr-4">
                            <x-primary-button>
                                {{ __('編集') }}
                            </x-primary-button>
                        </a>
                        <x-danger-button
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', { name: 'confirm-product-deletion', action: '{{ route('products.destroy', $product) }}' })"
                        >{{ __('削除') }}</x-danger-button>
                    </div>
                </div>
            </div>

            <!-- インシデント一覧 -->
            <div class="mt-8">
                <h3 class="text-2xl font-semibold leading-tight text-gray-800">インシデント履歴</h3>
                <div class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        @if($incidents->isEmpty())
                            <p class="text-gray-500">この製品に関するインシデデントはまだ登録されていません。</p>
                        @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">タイトル</th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">発生日</th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">種別</th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">対応</th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">費用</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($incidents->sortByDesc('occurred_at') as $incident)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                                <button
                                                    type="button"
                                                    x-data=""
                                                    x-on:click.prevent='$dispatch("open-modal", { name: "incident-details", incident: @json($incident) })'
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    {{ $incident->title }}
                                                </button>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $incident->occurred_at }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $incident->incident_type_label }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $incident->resolution_type_label }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ isset($incident->cost) ? number_format($incident->cost) . ' 円' : '---' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

        </div>
        <x-confirm-delete-modal />
        <x-incident-details-modal />
    </div>
</x-app-layout>
