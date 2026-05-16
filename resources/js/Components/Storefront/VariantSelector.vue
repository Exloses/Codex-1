<script setup>
import { computed, reactive, watch } from 'vue';

const props = defineProps({
    attributes: {
        type: Array,
        default: () => [],
    },
    variants: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const selected = reactive({});

const normalizedAttributes = computed(() =>
    [...props.attributes]
        .sort((a, b) => Number(a.sort_order || 0) - Number(b.sort_order || 0))
        .map((attribute) => ({
            ...attribute,
            values: [...(attribute.values || [])].sort((a, b) => Number(a.sort_order || 0) - Number(b.sort_order || 0)),
        })),
);

const hasVariants = computed(() => props.variants.length > 0);
const requiredNames = computed(() => normalizedAttributes.value.map((attribute) => attribute.name));
const isComplete = computed(() => requiredNames.value.every((name) => selected[name]));

const combinationKey = (combination = {}) => JSON.stringify(
    Object.fromEntries(Object.entries(combination).sort(([a], [b]) => a.localeCompare(b))),
);

const selectedCombination = computed(() =>
    Object.fromEntries(requiredNames.value.filter((name) => selected[name]).map((name) => [name, selected[name]])),
);

const resolvedVariant = computed(() => {
    if (!hasVariants.value || !isComplete.value) {
        return null;
    }

    const selectedKey = combinationKey(selectedCombination.value);

    return props.variants.find((variant) => combinationKey(variant.combination || {}) === selectedKey) || null;
});

const isColorAttribute = (attribute) => attribute.name?.toLowerCase().includes('color');

const optionHasStock = (attribute, value) => props.variants.some((variant) => {
    if (Number(variant.stock || 0) <= 0) {
        return false;
    }

    const combination = variant.combination || {};

    if (combination[attribute.name] !== value.value) {
        return false;
    }

    return Object.entries(selectedCombination.value).every(([key, selectedValue]) => {
        if (key === attribute.name) {
            return true;
        }

        return combination[key] === selectedValue;
    });
});

const selectValue = (attribute, value) => {
    selected[attribute.name] = value.value;
};

watch(
    () => props.modelValue,
    (variant) => {
        if (!variant?.combination) {
            return;
        }

        Object.entries(variant.combination).forEach(([name, value]) => {
            selected[name] = value;
        });
    },
    { immediate: true },
);

watch(
    resolvedVariant,
    (variant) => {
        emit('update:modelValue', variant);
        emit('change', {
            variant,
            combination: selectedCombination.value,
            complete: isComplete.value,
        });
    },
    { immediate: true },
);

const missingText = computed(() => {
    if (!hasVariants.value || isComplete.value) {
        return '';
    }

    return `Select ${requiredNames.value.filter((name) => !selected[name]).join(', ')}.`;
});
</script>

<template>
    <div v-if="hasVariants && normalizedAttributes.length" class="space-y-5">
        <div v-for="attribute in normalizedAttributes" :key="attribute.id || attribute.name">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-semibold">{{ attribute.name }}</p>
                <p v-if="selected[attribute.name]" class="text-xs text-zinc-500">{{ selected[attribute.name] }}</p>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                <button
                    v-for="value in attribute.values"
                    :key="value.id || `${attribute.name}-${value.value}`"
                    type="button"
                    class="relative inline-flex h-10 min-w-10 items-center justify-center rounded-md border px-3 text-sm font-semibold transition"
                    :class="[
                        selected[attribute.name] === value.value ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-300 bg-white text-zinc-800 hover:border-zinc-500',
                        !optionHasStock(attribute, value) ? 'opacity-45' : '',
                    ]"
                    :aria-pressed="selected[attribute.name] === value.value"
                    @click="selectValue(attribute, value)"
                >
                    <span v-if="isColorAttribute(attribute)" class="mr-2 h-5 w-5 rounded-full border border-zinc-300" :style="{ backgroundColor: value.color_hex || value.value }"></span>
                    <span>{{ value.value }}</span>
                    <span v-if="!optionHasStock(attribute, value)" class="pointer-events-none absolute left-2 right-2 top-1/2 h-px bg-zinc-500"></span>
                </button>
            </div>
        </div>

        <p v-if="missingText" class="text-sm text-amber-700">{{ missingText }}</p>
        <p v-else-if="isComplete && !resolvedVariant" class="text-sm text-rose-700">This option combination is not available.</p>
    </div>
</template>
