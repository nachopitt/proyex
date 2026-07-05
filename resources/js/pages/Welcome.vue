<script setup lang="ts">
import { dashboard, login } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, FolderKanban, LogIn } from 'lucide-vue-next';
</script>

<template>
    <Head title="Welcome to Proyex" />

    <div
        class="flex min-h-screen flex-col items-center justify-center bg-neutral-50 px-6 py-12 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100"
    >
        <!-- Decorative subtle background gradients -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 left-1/4 h-96 w-96 rounded-full bg-blue-500/10 blur-3xl dark:bg-blue-900/10"></div>
            <div class="absolute right-1/4 bottom-10 h-96 w-96 rounded-full bg-purple-500/10 blur-3xl dark:bg-purple-900/10"></div>
        </div>

        <main class="animate-fade-in relative z-10 w-full max-w-xl space-y-8 text-center">
            <!-- App Logo / Icon -->
            <div class="inline-flex rounded-3xl bg-neutral-900 p-4 text-white shadow-xl shadow-neutral-500/10 dark:bg-white dark:text-neutral-950">
                <FolderKanban class="size-10" />
            </div>

            <!-- Typography Header -->
            <div class="space-y-3">
                <h1
                    class="bg-gradient-to-r from-neutral-950 via-neutral-800 to-neutral-600 bg-clip-text text-4xl font-extrabold tracking-tight text-transparent sm:text-5xl dark:from-white dark:via-neutral-200 dark:to-neutral-400"
                >
                    Welcome to Proyex
                </h1>
                <p class="mx-auto max-w-md text-base leading-relaxed text-neutral-500 sm:text-lg dark:text-neutral-400">
                    A clean, modern workspace for managing your personal projects, subtasks, and progress logs.
                </p>
            </div>

            <!-- Call to Actions -->
            <div class="flex items-center justify-center gap-4">
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="dashboard().url"
                        class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 px-5 py-3 font-bold text-white shadow-lg shadow-neutral-900/10 transition-all hover:bg-neutral-800 active:scale-95 dark:bg-white dark:text-neutral-950 dark:hover:bg-neutral-100"
                    >
                        Go to Dashboard
                        <ArrowRight class="size-4" />
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="login().url"
                        class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 px-6 py-3 font-bold text-white shadow-lg shadow-neutral-900/10 transition-all hover:bg-neutral-800 active:scale-95 dark:bg-white dark:text-neutral-950 dark:hover:bg-neutral-100"
                    >
                        Log In
                        <LogIn class="size-4" />
                    </Link>
                </template>
            </div>
        </main>

        <!-- Footer -->
        <footer class="absolute bottom-6 text-center text-xs text-neutral-400 dark:text-neutral-600">
            &copy; 2026 Proyex. Built for high productivity.
        </footer>
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
