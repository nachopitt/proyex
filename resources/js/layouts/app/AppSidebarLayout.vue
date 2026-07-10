<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const flash = computed(() => page.props.flash as { warning?: string; success?: string; error?: string } | undefined);
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />

            <div class="px-6 py-2" v-if="flash?.warning || flash?.success || flash?.error">
                <div
                    v-if="flash.warning"
                    class="flex animate-in items-center justify-between rounded-lg border border-amber-500/30 bg-amber-500/15 px-4 py-3 text-sm text-amber-600 shadow-sm backdrop-blur-sm duration-300 fade-in slide-in-from-top-2 dark:text-amber-400"
                >
                    <span>{{ flash.warning }}</span>
                </div>
                <div
                    v-if="flash.success"
                    class="flex animate-in items-center justify-between rounded-lg border border-emerald-500/30 bg-emerald-500/15 px-4 py-3 text-sm text-emerald-600 shadow-sm backdrop-blur-sm duration-300 fade-in slide-in-from-top-2 dark:text-emerald-400"
                >
                    <span>{{ flash.success }}</span>
                </div>
                <div
                    v-if="flash.error"
                    class="flex animate-in items-center justify-between rounded-lg border border-rose-500/30 bg-rose-500/15 px-4 py-3 text-sm text-rose-600 shadow-sm backdrop-blur-sm duration-300 fade-in slide-in-from-top-2 dark:text-rose-400"
                >
                    <span>{{ flash.error }}</span>
                </div>
            </div>

            <slot />
        </AppContent>
    </AppShell>
</template>
