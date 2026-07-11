<script setup lang="ts">
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/admin/Layout.vue';
import { useToast } from '@/composables/useToast';
import { BreadcrumbItem } from '@/types';
import {
    Users as UsersIcon,
    Folder as FolderIcon,
    Tag as TagIcon,
    Server,
    Trash2,
    Shield,
    Settings,
    RefreshCw
} from 'lucide-vue-next';

interface Props {
    metrics: {
        users: {
            total: number;
            active: number;
            inactive: number;
            admins: number;
            regular: number;
        };
        projects: {
            total: number;
            active: number;
            completed: number;
            overdue: number;
        };
        tags_count: number;
    };
    system_info: {
        laravel_version: string;
        php_version: string;
        db_driver: string;
        db_version: string;
        app_env: string;
        debug_mode: boolean;
        timezone: string;
    };
}

defineProps<Props>();
const { addToast } = useToast();
const isClearingCache = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Admin Panel',
        href: '/admin',
    },
];

const handleClearCache = () => {
    isClearingCache.value = true;
    router.post(
        '/admin/actions/clear-cache',
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                isClearingCache.value = false;
                addToast('System cache cleared successfully.', 'success');
            },
            onError: () => {
                isClearingCache.value = false;
                addToast('Failed to clear cache.', 'error');
            },
        }
    );
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout
        title="Admin Panel"
        description="Monitor system-wide activity, manage users, and perform server maintenance actions."
        :breadcrumbs="breadcrumbs"
    >
        <div class="space-y-6">
            <!-- Summary Stats Cards -->
            <div class="grid gap-6 md:grid-cols-3">
                <!-- User Stats Card -->
                <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold tracking-wide text-neutral-500 uppercase dark:text-neutral-400">Total Users</p>
                            <h3 class="mt-2 text-3xl font-extrabold text-neutral-900 dark:text-neutral-50">
                                {{ metrics.users.total }}
                            </h3>
                        </div>
                        <div class="rounded-xl bg-blue-50 p-3 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                            <UsersIcon class="size-6" />
                        </div>
                    </div>
                    <div class="mt-6 border-t border-neutral-100 pt-4 dark:border-neutral-800">
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-neutral-500 dark:text-neutral-400 block">Admins / Members</span>
                                <span class="font-bold text-neutral-900 dark:text-neutral-50">{{ metrics.users.admins }} / {{ metrics.users.regular }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-500 dark:text-neutral-400 block">Active Status</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ metrics.users.active }} Active</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Stats Card -->
                <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold tracking-wide text-neutral-500 uppercase dark:text-neutral-400">Projects Managed</p>
                            <h3 class="mt-2 text-3xl font-extrabold text-neutral-900 dark:text-neutral-50">
                                {{ metrics.projects.total }}
                            </h3>
                        </div>
                        <div class="rounded-xl bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                            <FolderIcon class="size-6" />
                        </div>
                    </div>
                    <div class="mt-6 border-t border-neutral-100 pt-4 dark:border-neutral-800">
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <span class="text-neutral-500 dark:text-neutral-400 block">Active</span>
                                <span class="font-bold text-neutral-900 dark:text-neutral-50">{{ metrics.projects.active }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-500 dark:text-neutral-400 block">Completed</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ metrics.projects.completed }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-500 dark:text-neutral-400 block">Overdue</span>
                                <span class="font-bold text-rose-600 dark:text-rose-400">{{ metrics.projects.overdue }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tags Stats Card -->
                <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold tracking-wide text-neutral-500 uppercase dark:text-neutral-400">Global Tags</p>
                            <h3 class="mt-2 text-3xl font-extrabold text-neutral-900 dark:text-neutral-50">
                                {{ metrics.tags_count }}
                            </h3>
                        </div>
                        <div class="rounded-xl bg-amber-50 p-3 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                            <TagIcon class="size-6" />
                        </div>
                    </div>
                    <div class="mt-6 border-t border-neutral-100 pt-4 dark:border-neutral-800">
                        <div class="text-xs">
                            <span class="text-neutral-500 dark:text-neutral-400 block">Standardized labels</span>
                            <span class="font-bold text-neutral-900 dark:text-neutral-50">Used for project classification and routing.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout: System Info and Admin Maintenance -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- System Information -->
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="flex items-center gap-2 text-lg font-bold text-neutral-900 dark:text-neutral-50 mb-4">
                        <Server class="size-5 text-neutral-600 dark:text-neutral-400" />
                        System Diagnostics
                    </h3>
                    <div class="divide-y divide-neutral-100 dark:divide-neutral-800 text-sm">
                        <div class="flex justify-between py-3">
                            <span class="text-neutral-500 dark:text-neutral-400">Laravel Version</span>
                            <span class="font-mono font-medium text-neutral-800 dark:text-neutral-200">{{ system_info.laravel_version }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-neutral-500 dark:text-neutral-400">PHP Version</span>
                            <span class="font-mono font-medium text-neutral-800 dark:text-neutral-200">{{ system_info.php_version }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-neutral-500 dark:text-neutral-400">Database Engine</span>
                            <span class="font-mono font-medium text-neutral-800 dark:text-neutral-200">{{ system_info.db_driver }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-neutral-500 dark:text-neutral-400">Database Version</span>
                            <span class="font-mono font-medium text-neutral-800 dark:text-neutral-200 max-w-[200px] truncate" :title="system_info.db_version">{{ system_info.db_version }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-neutral-500 dark:text-neutral-400">Environment</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-semibold uppercase tracking-wider"
                                  :class="system_info.app_env === 'production'
                                      ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400'
                                      : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400'">
                                {{ system_info.app_env }}
                            </span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-neutral-500 dark:text-neutral-400">Debug Mode</span>
                            <span class="font-medium" :class="system_info.debug_mode ? 'text-amber-600' : 'text-neutral-500'">
                                {{ system_info.debug_mode ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-neutral-500 dark:text-neutral-400">System Timezone</span>
                            <span class="text-neutral-800 dark:text-neutral-200">{{ system_info.timezone }}</span>
                        </div>
                    </div>
                </div>

                <!-- Administrative Actions / Maintenance -->
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="flex items-center gap-2 text-lg font-bold text-neutral-900 dark:text-neutral-50 mb-4">
                        <Settings class="size-5 text-neutral-600 dark:text-neutral-400" />
                        Maintenance & Utility Tools
                    </h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-6">
                        Use these utility tools to keep the application tuned and clean up configuration file cache state.
                    </p>

                    <div class="space-y-4">
                        <div class="flex flex-col gap-4 p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800">
                            <div>
                                <h4 class="text-sm font-bold text-neutral-900 dark:text-neutral-50 flex items-center gap-1.5">
                                    <Trash2 class="size-4 text-neutral-500" />
                                    Clear Application Cache
                                </h4>
                                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    Flushes the application cache, cached configurations, cached views, and path routing tables to force new updates.
                                </p>
                            </div>
                            <div>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="isClearingCache"
                                    @click="handleClearCache"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl text-xs font-semibold bg-white border border-neutral-300 shadow-sm hover:bg-neutral-50 dark:bg-neutral-950 dark:border-neutral-800 dark:hover:bg-neutral-900"
                                >
                                    <RefreshCw v-if="isClearingCache" class="size-3.5 animate-spin" />
                                    <Trash2 v-else class="size-3.5" />
                                    {{ isClearingCache ? 'Clearing Cache...' : 'Flush Cache Store' }}
                                </Button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800">
                            <div>
                                <h4 class="text-sm font-bold text-neutral-900 dark:text-neutral-50 flex items-center gap-1.5">
                                    <Shield class="size-4 text-neutral-500" />
                                    Self-Registration Rules
                                </h4>
                                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    Public self-registration is currently **Disabled** by system policy. Only administrators can onboard and manage new user accounts via the create menu.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
