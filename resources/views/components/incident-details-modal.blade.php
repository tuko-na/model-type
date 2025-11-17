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
                <h2 class="text-2xl font-bold text-gray-900" x-text="incident.title"></h2>

                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
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
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('閉じる') }}
                    </x-secondary-button>
                </div>
            </div>
        </template>
    </div>
</x-modal>
