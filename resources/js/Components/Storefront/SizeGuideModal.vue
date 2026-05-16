<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    sizeGuide: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4" @click.self="emit('close')">
        <div class="max-h-[90vh] w-full max-w-2xl overflow-hidden rounded-lg bg-white">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4">
                <h2 class="text-lg font-bold">{{ sizeGuide?.name || 'Size guide' }}</h2>
                <button type="button" class="rounded-md border border-zinc-300 px-3 py-1 text-sm font-semibold hover:bg-zinc-50" @click="emit('close')">
                    Close
                </button>
            </div>

            <div class="overflow-auto p-5">
                <table v-if="sizeGuide?.columns?.length && sizeGuide?.rows?.length" class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr>
                            <th v-for="column in sizeGuide.columns" :key="column" class="border-b border-zinc-200 bg-zinc-50 px-3 py-2 font-semibold">
                                {{ column }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, rowIndex) in sizeGuide.rows" :key="rowIndex" class="odd:bg-white even:bg-zinc-50">
                            <td v-for="(column, columnIndex) in sizeGuide.columns" :key="`${rowIndex}-${column}`" class="border-b border-zinc-100 px-3 py-2">
                                {{ row[columnIndex] || '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else class="text-sm text-zinc-600">No size guide is available for this product.</p>
                <p v-if="sizeGuide?.notes" class="mt-4 text-sm text-zinc-600">{{ sizeGuide.notes }}</p>
            </div>
        </div>
    </div>
</template>
