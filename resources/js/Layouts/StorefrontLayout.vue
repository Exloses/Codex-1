<script setup>
import {
    BellIcon,
    ChatBubbleLeftRightIcon,
    HeartIcon,
    MagnifyingGlassIcon,
    ShoppingBagIcon,
    SparklesIcon,
    UserCircleIcon,
} from '@heroicons/vue/24/outline';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

const page = usePage();
const showMobileMenu = ref(false);
const showInstallPrompt = ref(false);
const deferredPrompt = ref(null);
const newsletterForm = useForm({ email: '' });

const user = computed(() => page.props.auth?.user);
const tawk = computed(() => page.props.services?.tawk || { enabled: false, propertyId: null, widgetId: null });
const availableCurrencies = computed(() => page.props.availableCurrencies || ['USD', 'EUR', 'GBP', 'AUD', 'SGD', 'MYR', 'IDR']);
const selectedCurrency = computed(() => page.props.currency || page.props.auth?.user?.currency || 'USD');
const selectedLanguage = computed(() => page.props.language || page.props.auth?.user?.language || 'en');

const navItems = [
    { label: 'Home', href: route('home') },
    { label: 'Products', href: route('products.index') },
    { label: 'Track', href: route('track.index') },
    { label: 'FAQ', href: route('faq.index') },
    { label: 'Support', href: route('support.index') },
    { label: 'Affiliate', href: route('affiliate.landing') },
];

const setPreference = (type, value) => {
    router.post(
        route(type === 'currency' ? 'preferences.currency' : 'preferences.language'),
        { [type]: value },
        { preserveScroll: true },
    );
};

const configureTawkVisitor = () => {
    if (!tawk.value.enabled || !window.Tawk_API || !user.value) return;

    const visitor = {
        name: user.value.name || '',
        email: user.value.email || '',
    };

    window.Tawk_API.visitor = visitor;

    if (typeof window.Tawk_API.setAttributes === 'function' && visitor.email) {
        window.Tawk_API.setAttributes(visitor, () => {});
    }
};

const loadTawkWidget = () => {
    if (!tawk.value.enabled || !tawk.value.propertyId || !tawk.value.widgetId) return;

    window.Tawk_API = window.Tawk_API || {};
    window.Tawk_LoadStart = window.Tawk_LoadStart || new Date();
    window.Tawk_API.onLoad = configureTawkVisitor;

    if (document.getElementById('tawk-widget')) {
        configureTawkVisitor();
        return;
    }

    const script = document.createElement('script');
    script.id = 'tawk-widget';
    script.async = true;
    script.src = `https://embed.tawk.to/${tawk.value.propertyId}/${tawk.value.widgetId}`;
    script.charset = 'UTF-8';
    script.setAttribute('crossorigin', '*');
    script.onload = configureTawkVisitor;
    script.onerror = () => {
        console.warn('Tawk.to widget could not be loaded.');
    };
    document.body.appendChild(script);
};

onMounted(() => {
    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt.value = event;
        showInstallPrompt.value = true;
    });

    loadTawkWidget();
});

watch([user, tawk], loadTawkWidget);

const installPwa = async () => {
    if (!deferredPrompt.value) return;

    deferredPrompt.value.prompt();
    await deferredPrompt.value.userChoice;
    deferredPrompt.value = null;
    showInstallPrompt.value = false;
};

const subscribe = () => {
    newsletterForm.post(route('newsletter.subscribe'), {
        preserveScroll: true,
        onSuccess: () => newsletterForm.reset(),
    });
};
</script>

<template>
    <div class="min-h-screen bg-zinc-50 text-zinc-950">
        <header class="sticky top-0 z-40 border-b border-zinc-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <Link :href="route('home')" class="flex min-w-0 items-center gap-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-600 text-white">
                        <SparklesIcon class="h-5 w-5" />
                    </span>
                    <span class="truncate text-lg font-bold tracking-normal">GlobalDrop</span>
                </Link>

                <nav class="hidden flex-1 items-center justify-center gap-1 lg:flex">
                    <Link
                        v-for="item in navItems"
                        :key="item.label"
                        :href="item.href"
                        class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <form :action="route('products.search')" method="get" class="hidden w-72 items-center rounded-md border border-zinc-300 bg-white px-3 py-2 md:flex">
                    <MagnifyingGlassIcon class="h-4 w-4 text-zinc-400" />
                    <input name="q" class="ml-2 w-full border-0 p-0 text-sm focus:ring-0" placeholder="Search products" />
                </form>

                <select
                    class="hidden rounded-md border-zinc-300 text-sm md:block"
                    :value="selectedCurrency"
                    @change="setPreference('currency', $event.target.value)"
                >
                    <option v-for="currency in availableCurrencies" :key="currency" :value="currency">{{ currency }}</option>
                </select>

                <select
                    class="hidden rounded-md border-zinc-300 text-sm md:block"
                    :value="selectedLanguage"
                    @change="setPreference('language', $event.target.value)"
                >
                    <option value="en">EN</option>
                    <option value="id">ID</option>
                </select>

                <div class="hidden items-center gap-1 sm:flex">
                    <Link :href="route('account.notifications')" class="rounded-md p-2 text-zinc-600 hover:bg-zinc-100" title="Notifications">
                        <BellIcon class="h-5 w-5" />
                    </Link>
                    <Link :href="route('account.wishlist')" class="rounded-md p-2 text-zinc-600 hover:bg-zinc-100" title="Wishlist">
                        <HeartIcon class="h-5 w-5" />
                    </Link>
                    <Link :href="route('cart.index')" class="rounded-md p-2 text-zinc-600 hover:bg-zinc-100" title="Cart">
                        <ShoppingBagIcon class="h-5 w-5" />
                    </Link>
                    <Link :href="route('support.index')" class="rounded-md p-2 text-zinc-600 hover:bg-zinc-100" title="Support">
                        <ChatBubbleLeftRightIcon class="h-5 w-5" />
                    </Link>
                </div>

                <Link
                    v-if="user"
                    :href="route('account.index')"
                    class="hidden rounded-md p-2 text-zinc-600 hover:bg-zinc-100 sm:block"
                    title="Account"
                >
                    <UserCircleIcon class="h-5 w-5" />
                </Link>

                <div v-else class="hidden items-center gap-2 sm:flex">
                    <Link :href="route('social.redirect', 'google')" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Google</Link>
                    <Link :href="route('social.redirect', 'facebook')" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Facebook</Link>
                    <Link :href="route('login')" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Login</Link>
                </div>

                <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold lg:hidden" @click="showMobileMenu = !showMobileMenu">
                    Menu
                </button>
            </div>

            <div v-if="showMobileMenu" class="border-t border-zinc-200 bg-white px-4 py-3 lg:hidden">
                <div class="grid gap-2">
                    <Link v-for="item in navItems" :key="item.label" :href="item.href" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100">
                        {{ item.label }}
                    </Link>
                    <div class="grid grid-cols-2 gap-2 pt-2 sm:grid-cols-4">
                        <Link :href="route('cart.index')" class="rounded-md border border-zinc-300 px-3 py-2 text-center text-sm">Cart</Link>
                        <Link :href="route('account.wishlist')" class="rounded-md border border-zinc-300 px-3 py-2 text-center text-sm">Wishlist</Link>
                        <Link :href="route('support.index')" class="rounded-md border border-zinc-300 px-3 py-2 text-center text-sm">Support</Link>
                        <Link :href="user ? route('account.index') : route('login')" class="rounded-md border border-zinc-300 px-3 py-2 text-center text-sm">Account</Link>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <footer class="border-t border-zinc-200 bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.2fr_0.8fr_0.8fr] lg:px-8">
                <div>
                    <div class="text-lg font-bold">GlobalDrop</div>
                    <p class="mt-3 max-w-md text-sm leading-6 text-zinc-600">
                        Cross-border storefront for curated products from Indonesian vendors, built for clear checkout, tracking, and support.
                    </p>
                    <form class="mt-5 flex max-w-md gap-2" @submit.prevent="subscribe">
                        <input v-model="newsletterForm.email" type="email" required class="min-w-0 flex-1 rounded-md border-zinc-300 text-sm" placeholder="Email for drops and offers" />
                        <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Subscribe</button>
                    </form>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-normal text-zinc-500">Payments</h3>
                    <div class="mt-4 flex flex-wrap gap-2 text-sm font-semibold text-zinc-700">
                        <span class="rounded-md border border-zinc-300 px-3 py-2">Visa</span>
                        <span class="rounded-md border border-zinc-300 px-3 py-2">Mastercard</span>
                        <span class="rounded-md border border-zinc-300 px-3 py-2">Stripe</span>
                        <span class="rounded-md border border-zinc-300 px-3 py-2">PayPal</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-normal text-zinc-500">Carriers</h3>
                    <div class="mt-4 flex flex-wrap gap-2 text-sm font-semibold text-zinc-700">
                        <span class="rounded-md border border-zinc-300 px-3 py-2">DHL</span>
                        <span class="rounded-md border border-zinc-300 px-3 py-2">FedEx</span>
                        <span class="rounded-md border border-zinc-300 px-3 py-2">JNE</span>
                        <span class="rounded-md border border-zinc-300 px-3 py-2">EasyPost</span>
                    </div>
                </div>
            </div>
        </footer>

        <div v-if="showInstallPrompt" class="fixed bottom-4 left-4 right-4 z-50 mx-auto max-w-xl rounded-lg border border-zinc-200 bg-white p-4 shadow-lg sm:left-auto sm:right-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold">Install GlobalDrop</p>
                    <p class="text-sm text-zinc-600">Add the storefront to your device for faster repeat shopping.</p>
                </div>
                <button class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white" @click="installPwa">Install</button>
            </div>
        </div>
    </div>
</template>
