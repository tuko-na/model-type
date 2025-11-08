<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('製品一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="flex justify-end mb-4">
                        <a href="{{ route('products.create') }}">
                            <x-primary-button>
                                {{ __('新規登録') }}
                            </x-primary-button>
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 text-sm font-medium text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    ステータス
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    製品名
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    型番
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    カテゴリ
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    メーカー
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    購入日
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">編集</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-green-100 text-green-800' => $product->status === 'active',
                                            'bg-gray-100 text-gray-800' => $product->status === 'in_storage',
                                            'bg-yellow-100 text-yellow-800' => $product->status === 'in_repair',
                                            'bg-red-100 text-red-800' => $product->status === 'disposed',
                                        ])>
                                            {{ match ($product->status) {
                                                'active' => '使用中',
                                                'in_storage' => '保管中',
                                                'in_repair' => '修理中',
                                                'disposed' => '廃棄済み',
                                                default => '不明',
                                            } }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                        <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $product->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $product->model_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $product->category }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $product->manufacturer }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $product->purchase_date }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        <div class="flex flex-col items-end space-y-2">
                                            <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">編集</a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">削除</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">

                                    製品が登録されていません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
