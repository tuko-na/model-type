<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('製品の新規登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <!-- 型番 -->
                        <div>
                            <x-input-label for="model_number" :value="__('型番')" />
                            <x-text-input id="model_number" class="block mt-1 w-full" type="text" name="model_number" :value="old('model_number')" required autofocus />
                            <x-input-error :messages="$errors->get('model_number')" class="mt-2" />
                        </div>

                        <!-- 製品名 -->
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('製品名')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- メーカー -->
                        <div class="mt-4">
                            <x-input-label for="manufacturer" :value="__('メーカー')" />
                            <x-text-input id="manufacturer" class="block mt-1 w-full" type="text" name="manufacturer" :value="old('manufacturer')" required />
                            <x-input-error :messages="$errors->get('manufacturer')" class="mt-2" />
                        </div>

                        <!-- カテゴリ -->
                        <div class="mt-4">
                            <x-input-label for="category" :value="__('カテゴリ')" />
                            <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category')" required />
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- 購入日 -->
                        <div class="mt-4">
                            <x-input-label for="purchase_date" :value="__('購入日')" />
                            <x-text-input id="purchase_date" class="block mt-1 w-full" type="date" name="purchase_date" :value="old('purchase_date')" required />
                            <x-input-error :messages="$errors->get('purchase_date')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('登録する') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
