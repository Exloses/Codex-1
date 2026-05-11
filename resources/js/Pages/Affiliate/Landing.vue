<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
</script>

<template>
    <StorefrontLayout>
        <Head title="Affiliate program" />
        <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-zinc-950 p-8 text-white">
                <p class="text-sm font-semibold uppercase tracking-normal text-emerald-200">Affiliate program</p>
                <h1 class="mt-3 text-4xl font-bold tracking-normal">Earn from global dropship referrals</h1>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-zinc-200">Generate links, track commissions, and request payouts from your account dashboard.</p>
                <div class="mt-6 flex gap-3">
                    <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white" @click="router.post(route('affiliate.register'))">Join now</button>
                    <Link :href="route('affiliate.dashboard')" class="rounded-md border border-white/30 px-4 py-2 text-sm font-semibold">Dashboard</Link>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
