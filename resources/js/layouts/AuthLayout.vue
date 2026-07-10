<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-neutral-50 px-4 py-12 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100"
    >
        <!-- Decorative subtle background gradients matching the layout theme -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 left-1/4 h-96 w-96 rounded-full bg-blue-500/10 blur-3xl dark:bg-blue-900/10"></div>
            <div class="absolute right-1/4 bottom-10 h-96 w-96 rounded-full bg-purple-500/10 blur-3xl dark:bg-purple-900/10"></div>
        </div>

        <main class="animate-fade-in relative z-10 w-full max-w-md space-y-6">
            <!-- Centered Logo Treatment -->
            <div class="flex flex-col items-center justify-center">
                <Link :href="home()" class="group flex flex-col items-center gap-2">
                    <div
                        class="inline-flex rounded-2xl bg-neutral-900 p-3 text-white shadow-xl shadow-neutral-900/10 transition-transform duration-300 group-hover:scale-105 dark:bg-white dark:text-neutral-950"
                    >
                        <AppLogoIcon class="size-6 fill-none stroke-current" />
                    </div>
                    <span
                        class="mt-2 bg-gradient-to-r from-neutral-950 via-neutral-800 to-neutral-700 bg-clip-text text-xl font-extrabold tracking-tight text-transparent dark:from-white dark:via-neutral-200 dark:to-neutral-400"
                        >Proyex</span
                    >
                </Link>
            </div>

            <!-- Premium Card Structure Mirroring Dashboard Cards -->
            <div
                class="rounded-2xl border border-neutral-200 bg-white/80 p-8 shadow-xl shadow-neutral-950/5 backdrop-blur-md dark:border-neutral-800 dark:bg-neutral-900/80 dark:shadow-black/40"
            >
                <div class="mb-6 text-center">
                    <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
                        {{ title }}
                    </h1>
                    <p class="mt-1.5 text-sm text-neutral-500 dark:text-neutral-400">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
