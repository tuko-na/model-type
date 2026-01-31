@props([
    'name' => 'incident-details',
])

<x-modal :name="$name" maxWidth="2xl">
    <div
        x-data="{ incident: null }"
        x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') { incident = $event.detail.incident; }"
        class="p-6"
    >
        <template x-if="incident">
            <div>
                <div class="flex items-start justify-between">
                    <h2 class="text-2xl font-bold text-gray-900" x-text="incident.title"></h2>
                    <template x-if="incident.severity">
                        <span
                            class="px-3 py-1 text-sm font-medium rounded-full"
                            :class="{
                                'bg-green-100 text-green-800': incident.severity_color === 'green',
                                'bg-yellow-100 text-yellow-800': incident.severity_color === 'yellow',
                                'bg-orange-100 text-orange-800': incident.severity_color === 'orange',
                                'bg-red-100 text-red-800': incident.severity_color === 'red',
                                'bg-gray-100 text-gray-800': !incident.severity_color
                            }"
                            x-text="incident.severity_label"
                        ></span>
                    </template>
                </div>

                <div class="grid grid-cols-1 mt-6 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">発生日</h3>
                        <p class="mt-1 text-sm text-gray-900" x-text="incident.occurred_at"></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">費用</h3>
                        <p class="mt-1 text-sm text-gray-900" x-text="incident.cost ? `${Number(incident.cost).toLocaleString()} 円` : '---'"></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">インシデント種別</h3>
                        <p class="mt-1 text-sm text-gray-900" x-text="incident.incident_type_label"></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">対応種別</h3>
                        <p class="mt-1 text-sm text-gray-900" x-text="incident.resolution_type_label"></p>
                    </div>
                    <div class="sm:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500">症状タグ</h3>
                        <p class="mt-1 text-sm text-gray-900" x-text="incident.symptom_tags || '---'"></p>
                    </div>
                    <div class="sm:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500">詳細</h3>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap" x-text="incident.description || '---'"></p>
                    </div>

                    {{-- カテゴリ固有の詳細情報 --}}
                    <template x-if="incident.details && Object.keys(incident.details).length > 0">
                        <div class="pt-4 mt-4 border-t border-gray-200 sm:col-span-2">
                            <h3 class="mb-3 text-sm font-medium text-gray-500">カテゴリ固有の情報</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <template x-for="[key, value] in Object.entries(incident.details)" :key="key">
                                    <div>
                                        <span class="text-xs font-medium text-gray-400" x-text="key"></span>
                                        <p class="text-sm text-gray-900" x-text="value === true ? 'はい' : (value === false ? 'いいえ' : (value || '---'))"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex justify-between mt-6">
                    <x-danger-button
                        x-on:click.prevent="$dispatch('open-modal', { name: 'confirm-product-deletion', action: `/products/${incident.product_id}/incidents/${incident.id}` }); $dispatch('close')"
                    >
                        {{ __('削除') }}
                    </x-danger-button>
                    <div class="flex justify-end space-x-4">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            {{ __('閉じる') }}
                        </x-secondary-button>
                        <a :href="`/incidents/${incident.id}/edit`">
                            <x-primary-button>
                                {{ __('編集') }}
                            </x-primary-button>
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-modal>
