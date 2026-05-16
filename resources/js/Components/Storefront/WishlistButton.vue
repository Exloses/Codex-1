<script setup>
import { HeartIcon as HeartOutlineIcon } from '@heroicons/vue/24/outline';
import { HeartIcon as HeartSolidIcon } from '@heroicons/vue/24/solid';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    showLabel: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const processing = ref(false);

const pageWishlistIds = computed(() => page.props.wishlistProductIds || []);
const isWishlistedFromProps = computed(() => {
    if (props.product.is_wishlisted !== undefined) {
        return Boolean(props.product.is_wishlisted);
    }

    return pageWishlistIds.value.map(Number).includes(Number(props.product.id));
});
const isWishlisted = ref(isWishlistedFromProps.value);

watch(isWishlistedFromProps, (value) => {
    isWishlisted.value = value;
});

const toggleWishlist = () => {
    if (processing.value) return;

    processing.value = true;
    const nextState = !isWishlisted.value;

    router.post(
        route('wishlist.toggle', props.product.id),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                isWishlisted.value = nextState;
            },
            onFinish: () => {
                processing.value = false;
            },
        },
    );
};
</script>

<template>
    <button
        type="button"
        class="inline-flex items-center justify-center rounded-md transition disabled:cursor-not-allowed disabled:opacity-60"
        :class="[
            showLabel ? 'gap-2 border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-rose-50' : 'p-2 hover:bg-rose-50',
            isWishlisted ? 'text-rose-600' : 'text-zinc-500 hover:text-rose-600',
        ]"
        :title="isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'"
        :aria-pressed="isWishlisted"
        :disabled="processing"
        @click.stop.prevent="toggleWishlist"
    >
        <HeartSolidIcon v-if="isWishlisted" class="h-5 w-5" />
        <HeartOutlineIcon v-else class="h-5 w-5" />
        <span v-if="showLabel">{{ isWishlisted ? 'Saved' : 'Save' }}</span>
    </button>
</template>
