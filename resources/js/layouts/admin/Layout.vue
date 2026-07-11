<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutDashboard, Users, Activity } from 'lucide-vue-next';

interface Props {
    title: string;
    description: string;
    breadcrumbs: BreadcrumbItem[];
}

defineProps<Props>();

const sidebarNavItems = [
    {
        title: 'Overview',
        href: '/admin',
        icon: LayoutDashboard,
    },
    {
        title: 'User Management',
        href: '/admin/users',
        icon: Users,
    },
    {
        title: 'Audit Logs',
        href: '/admin/logs',
        icon: Activity,
    },
];

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

const isUrlActive = (href: string) => {
    if (href === '/admin') {
        return currentPath === '/admin';
    }
    return currentPath.startsWith(href);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl space-y-6 p-6 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-col gap-2 border-b border-neutral-200 pb-5 dark:border-neutral-800">
                <h1 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-50">
                    {{ title }}
                </h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ description }}
                </p>
            </div>

            <div class="flex flex-col gap-6 lg:flex-row">
                <!-- Navigation Submenu -->
                <aside class="w-full shrink-0 lg:w-64">
                    <nav
                        class="flex flex-row gap-1 overflow-x-auto rounded-2xl border border-neutral-200 bg-white p-2 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 lg:flex-col lg:overflow-x-visible lg:p-3"
                    >
                        <Button
                            v-for="item in sidebarNavItems"
                            :key="item.href"
                            variant="ghost"
                            :class="[
                                'flex items-center gap-2 justify-start rounded-xl px-3 py-2.5 text-sm font-semibold transition-all duration-200 active:scale-[0.98]',
                                isUrlActive(item.href)
                                    ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-neutral-50'
                                    : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800/50 dark:hover:text-neutral-50',
                            ]"
                            as-child
                        >
                            <Link :href="item.href">
                                <component :is="item.icon" class="size-4" />
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
    </AppLayout>
</template>
