<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import InputError from '@/Components/InputError.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    mode: { type: String, default: 'index' },
    returns: { type: Object, default: () => ({ data: [] }) },
    returnRequest: { type: Object, default: null },
    order: { type: Object, default: null },
    refundMethods: { type: Array, default: () => ['original_payment', 'store_credit', 'manual'] },
});

const title = computed(() => {
    if (props.mode === 'create') return 'Request return';
    if (props.mode === 'show') return props.returnRequest?.return_number || 'Return request';
    return 'Returns';
});

const form = useForm({
    order_id: props.order?.id,
    reason: '',
    description: '',
    refund_method: 'original_payment',
    images: [],
});

const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
const pretty = (value) => String(value || '').replaceAll('_', ' ');

const setImages = (event) => {
    form.images = Array.from(event.target.files || []);
};

const submit = () => {
    form.post(route('returns.store'), { forceFormData: true });
};

const cancelReturn = () => {
    router.post(route('returns.cancel', props.returnRequest.id), {}, { preserveScroll: true });
};

const timeline = computed(() => [
    { key: 'pending', label: 'Submitted' },
    { key: 'under_review', label: 'Under review' },
    { key: 'approved', label: 'Approved' },
    { key: 'refund_pending', label: 'Refund pending' },
    { key: 'refunded', label: 'Refunded' },
]);

const currentIndex = computed(() => timeline.value.findIndex((step) => step.key === props.returnRequest?.status));
</script>

<template>
    <StorefrontLayout>
        <Head :title="title" />

        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm text-zinc-500">Account</p>
                    <h1 class="text-3xl font-bold tracking-normal">{{ title }}</h1>
                </div>
                <Link :href="route('account.orders')" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold">
                    Orders
                </Link>
            </div>

            <div v-if="mode === 'index'" class="mt-6 space-y-3">
                <Link
                    v-for="item in returns.data"
                    :key="item.id"
                    :href="route('returns.show', item.id)"
                    class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-4 sm:grid-cols-[1fr_auto]"
                >
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold">{{ item.return_number }}</p>
                            <StatusBadge :value="item.status" />
                        </div>
                        <p class="mt-1 text-sm text-zinc-500">{{ item.order.order_number }} - {{ pretty(item.reason) }}</p>
                    </div>
                    <div class="text-left text-sm font-semibold sm:text-right">
                        <p>{{ money(item.refund_amount_usd || item.order.total_usd) }}</p>
                        <p class="font-normal text-zinc-500">{{ pretty(item.refund_method) }}</p>
                    </div>
                </Link>
                <EmptyState v-if="!returns.data?.length" title="No return requests yet" />
            </div>

            <form v-else-if="mode === 'create'" class="mt-6 grid gap-6 lg:grid-cols-[1fr_0.8fr]" @submit.prevent="submit">
                <div class="rounded-lg border border-zinc-200 bg-white p-5">
                    <div class="grid gap-4">
                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Reason</span>
                            <select v-model="form.reason" class="rounded-md border-zinc-300 text-sm">
                                <option value="">Select reason</option>
                                <option value="damaged">Damaged item</option>
                                <option value="wrong_item">Wrong item</option>
                                <option value="not_as_described">Not as described</option>
                                <option value="quality_issue">Quality issue</option>
                                <option value="other">Other</option>
                            </select>
                            <InputError :message="form.errors.reason" />
                        </label>

                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Description</span>
                            <textarea v-model="form.description" rows="6" class="rounded-md border-zinc-300 text-sm" />
                            <InputError :message="form.errors.description" />
                        </label>

                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Refund method</span>
                            <select v-model="form.refund_method" class="rounded-md border-zinc-300 text-sm">
                                <option v-for="method in refundMethods" :key="method" :value="method">{{ pretty(method) }}</option>
                            </select>
                            <InputError :message="form.errors.refund_method" />
                        </label>

                        <label class="grid gap-2">
                            <span class="text-sm font-semibold">Photos</span>
                            <input type="file" multiple accept="image/*" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @change="setImages" />
                            <InputError :message="form.errors.images || form.errors['images.0']" />
                        </label>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white" :disabled="form.processing">
                            Submit
                        </button>
                        <Link :href="route('account.orders.show', order.id)" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold">
                            Back to order
                        </Link>
                    </div>
                </div>

                <aside class="rounded-lg border border-zinc-200 bg-white p-5">
                    <p class="text-sm text-zinc-500">Order</p>
                    <h2 class="mt-1 text-xl font-bold tracking-normal">{{ order.order_number }}</h2>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span>Status</span>
                        <StatusBadge :value="order.status" />
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm">
                        <span>Total</span>
                        <span class="font-semibold">{{ money(order.total_usd) }}</span>
                    </div>
                    <div class="mt-4 divide-y divide-zinc-100">
                        <div v-for="item in order.items || []" :key="item.id" class="py-3 text-sm">
                            <p class="font-medium">{{ item.product?.name }}</p>
                            <p class="text-zinc-500">Qty {{ item.quantity }} - {{ money(item.subtotal_usd) }}</p>
                        </div>
                    </div>
                </aside>
            </form>

            <div v-else-if="returnRequest" class="mt-6 grid gap-6 lg:grid-cols-[1fr_0.75fr]">
                <div class="rounded-lg border border-zinc-200 bg-white p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-sm text-zinc-500">Return number</p>
                            <h2 class="text-2xl font-bold tracking-normal">{{ returnRequest.return_number }}</h2>
                        </div>
                        <StatusBadge :value="returnRequest.status" />
                    </div>

                    <div class="mt-6 space-y-4">
                        <div v-for="(step, index) in timeline" :key="step.key" class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="h-3 w-3 rounded-full" :class="index <= currentIndex ? 'bg-emerald-600' : 'bg-zinc-300'" />
                                <div v-if="index < timeline.length - 1" class="h-9 w-px" :class="index < currentIndex ? 'bg-emerald-600' : 'bg-zinc-200'" />
                            </div>
                            <div class="pb-4 text-sm">
                                <p class="font-semibold">{{ step.label }}</p>
                                <p class="capitalize text-zinc-500">{{ step.key === returnRequest.status ? 'Current status' : pretty(step.key) }}</p>
                            </div>
                        </div>
                    </div>

                    <dl class="mt-6 grid gap-4 border-t border-zinc-100 pt-5 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-zinc-500">Reason</dt>
                            <dd class="mt-1 font-medium capitalize">{{ pretty(returnRequest.reason) }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-500">Refund method</dt>
                            <dd class="mt-1 font-medium capitalize">{{ pretty(returnRequest.refund_method) }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-500">Refund amount</dt>
                            <dd class="mt-1 font-medium">{{ money(returnRequest.refund_amount_usd || returnRequest.order.total_usd) }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-500">Reference</dt>
                            <dd class="mt-1 font-medium">{{ returnRequest.refund_reference || '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 rounded-md bg-zinc-50 p-4 text-sm leading-6 text-zinc-700">
                        {{ returnRequest.description }}
                    </div>

                    <div v-if="returnRequest.admin_notes" class="mt-4 rounded-md border border-zinc-200 p-4 text-sm leading-6 text-zinc-700">
                        {{ returnRequest.admin_notes }}
                    </div>
                </div>

                <aside class="rounded-lg border border-zinc-200 bg-white p-5">
                    <p class="text-sm text-zinc-500">Order</p>
                    <Link :href="route('account.orders.show', returnRequest.order.id)" class="mt-1 block text-xl font-bold tracking-normal">
                        {{ returnRequest.order.order_number }}
                    </Link>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span>Order status</span>
                        <StatusBadge :value="returnRequest.order.status" />
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm">
                        <span>Payment</span>
                        <span class="font-semibold capitalize">{{ returnRequest.order.payment_status }}</span>
                    </div>
                    <div v-if="['pending', 'under_review'].includes(returnRequest.status)" class="mt-5">
                        <button class="w-full rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold" @click="cancelReturn">
                            Cancel request
                        </button>
                    </div>
                </aside>
            </div>
        </section>
    </StorefrontLayout>
</template>
