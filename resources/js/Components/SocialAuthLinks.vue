<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="grid gap-3 sm:grid-cols-2">
        <Link
            :href="route('social.redirect', 'google')"
            class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            Continue with Google
        </Link>
        <Link
            :href="route('social.redirect', 'facebook')"
            class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            Continue with Facebook
        </Link>
    </div>
</template>
