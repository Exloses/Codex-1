<script setup>
const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const ensureArrays = () => {
    props.form.attributes = props.form.attributes || [];
    props.form.variants = props.form.variants || [];
};

const addAttribute = () => {
    ensureArrays();
    props.form.attributes.push({
        name: '',
        sort_order: props.form.attributes.length,
        values: [{ value: '', color_hex: '', sort_order: 0 }],
    });
};

const removeAttribute = (index) => {
    props.form.attributes.splice(index, 1);
};

const addValue = (attribute) => {
    attribute.values = attribute.values || [];
    attribute.values.push({ value: '', color_hex: '', sort_order: attribute.values.length });
};

const removeValue = (attribute, index) => {
    attribute.values.splice(index, 1);
};

const addVariant = () => {
    ensureArrays();
    const combination = {};

    props.form.attributes.forEach((attribute) => {
        if (attribute.name) {
            combination[attribute.name] = attribute.values?.[0]?.value || '';
        }
    });

    props.form.variants.push({
        combination,
        sku: '',
        price: null,
        vendor_price: null,
        stock: 0,
        image: '',
    });
};

const removeVariant = (index) => {
    props.form.variants.splice(index, 1);
};
</script>

<template>
    <div class="space-y-6 sm:col-span-2">
        <div class="border-t border-zinc-200 pt-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold">Attributes</h2>
                <button type="button" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50" @click="addAttribute">
                    Add attribute
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <div v-for="(attribute, attributeIndex) in form.attributes" :key="attributeIndex" class="rounded-md border border-zinc-200 p-4">
                    <div class="grid gap-3 sm:grid-cols-[1fr_120px_auto]">
                        <input v-model="attribute.name" class="rounded-md border-zinc-300 text-sm" placeholder="Color, Size, Material" />
                        <input v-model.number="attribute.sort_order" type="number" min="0" class="rounded-md border-zinc-300 text-sm" placeholder="Sort" />
                        <button type="button" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50" @click="removeAttribute(attributeIndex)">
                            Remove
                        </button>
                    </div>

                    <div class="mt-3 space-y-2">
                        <div v-for="(value, valueIndex) in attribute.values" :key="valueIndex" class="grid gap-2 sm:grid-cols-[1fr_150px_auto]">
                            <input v-model="value.value" class="rounded-md border-zinc-300 text-sm" placeholder="Red, XL, Cotton" />
                            <input v-model="value.color_hex" class="rounded-md border-zinc-300 text-sm" placeholder="#DC2626" />
                            <button type="button" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50" @click="removeValue(attribute, valueIndex)">
                                Remove
                            </button>
                        </div>
                        <button type="button" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50" @click="addValue(attribute)">
                            Add value
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-zinc-200 pt-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold">Variants</h2>
                <button type="button" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50" @click="addVariant">
                    Add variant
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <div v-for="(variant, variantIndex) in form.variants" :key="variant.id || variantIndex" class="rounded-md border border-zinc-200 p-4">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div v-for="attribute in form.attributes.filter((item) => item.name)" :key="`${variantIndex}-${attribute.name}`">
                            <label class="text-sm font-semibold">{{ attribute.name }}</label>
                            <select v-model="variant.combination[attribute.name]" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                                <option value="">Select {{ attribute.name }}</option>
                                <option v-for="value in attribute.values.filter((item) => item.value)" :key="value.value" :value="value.value">
                                    {{ value.value }}
                                </option>
                            </select>
                        </div>
                        <input v-model="variant.sku" class="rounded-md border-zinc-300 text-sm" placeholder="Variant SKU" />
                        <input v-model.number="variant.price" type="number" min="0" step="0.01" class="rounded-md border-zinc-300 text-sm" placeholder="Selling price override" />
                        <input v-model.number="variant.vendor_price" type="number" min="0" step="0.01" class="rounded-md border-zinc-300 text-sm" placeholder="Vendor price" />
                        <input v-model.number="variant.stock" type="number" min="0" class="rounded-md border-zinc-300 text-sm" placeholder="Stock" />
                        <input v-model="variant.image" class="rounded-md border-zinc-300 text-sm sm:col-span-2" placeholder="Image URL" />
                    </div>
                    <button type="button" class="mt-3 rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50" @click="removeVariant(variantIndex)">
                        Remove variant
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
