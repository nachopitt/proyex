<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { toUrl, urlIsActive } from '@/lib/utils';
import { appearance } from '@/routes';
import { edit as editPassword } from '@/routes/password';
import { edit } from '@/routes/profile';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: edit(),
    },
    {
        title: 'Password',
        href: editPassword(),
    },
    {
        title: 'Appearance',
        href: appearance(),
    },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="mx-auto max-w-7xl space-y-6 p-6 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex flex-col gap-1 border-b border-neutral-200 pb-5 dark:border-neutral-800">
            <h1 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-50">Settings</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Manage your profile and account settings</p>
        </div>

        <div class="flex flex-col gap-6 lg:flex-row">
            <!-- Sidebar Navigation -->
            <aside class="w-full shrink-0 lg:w-64">
                <nav
                    class="flex flex-col gap-1 rounded-2xl border border-neutral-200 bg-white p-3 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start rounded-xl px-3 py-2 text-sm font-semibold transition-all duration-200 active:scale-[0.98]',
                            urlIsActive(item.href, currentPath)
                                ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-neutral-50'
                                : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800/50 dark:hover:text-neutral-50',
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 space-y-6">
                <slot />
            </div>
        </div>
    </div>
</template>
