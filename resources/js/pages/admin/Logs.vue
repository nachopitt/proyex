<script setup lang="ts">
import { ref, watch } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import AdminLayout from '@/layouts/admin/Layout.vue';
import { BreadcrumbItem, PaginationLink } from '@/types';
import { Search, Clock, FileText, User, ArrowUpRight } from 'lucide-vue-next';

interface LogUpdate {
    id: number;
    description: string;
    status: string | null;
    status_label: string | null;
    progress_percentage: number | null;
    project: {
        id: number;
        title: string;
    } | null;
    updater_user: {
        id: number;
        name: string;
    } | null;
    created_at: string;
    created_at_human: string;
}

interface Props {
    logs: {
        data: LogUpdate[];
        links: PaginationLink[];
    };
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Admin Panel',
        href: '/admin',
    },
    {
        title: 'Audit Logs',
        href: '/admin/logs',
    },
];

// Self-contained debounce function
function debounce<T extends (...args: any[]) => any>(fn: T, delay: number) {
    let timeoutId: ReturnType<typeof setTimeout> | null = null;
    return (...args: Parameters<T>) => {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(() => {
            fn(...args);
        }, delay);
    };
}

const applyFilters = debounce(() => {
    router.get(
        '/admin/logs',
        {
            search: search.value,
            status: statusFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}, 300);

watch([search, statusFilter], () => {
    applyFilters();
});

const getStatusClass = (status: string | null) => {
    if (!status) return 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300';
    switch (status) {
        case 'planned':
            return 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300';
        case 'in-progress':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400';
        case 'on-hold':
            return 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400';
        case 'completed':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400';
        case 'cancelled':
            return 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400';
        default:
            return 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300';
    }
};
</script>

<template>
    <Head title="Audit Logs" />

    <AdminLayout
        title="Audit Logs & Activity"
        description="Monitor system-wide updates, progress changes, and state transitions chronologically."
        :breadcrumbs="breadcrumbs"
    >
        <div class="space-y-6">
            <!-- Filters Panel -->
            <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="relative min-w-[240px] flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400 dark:text-neutral-500">
                        <Search class="size-4" />
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search logs by description, user, or project..."
                        class="w-full rounded-xl border border-neutral-200 bg-white py-2 pr-4 pl-10 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-100"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <select
                        v-model="statusFilter"
                        class="h-9 rounded-xl border border-neutral-200 bg-white px-3 text-sm text-neutral-700 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-300"
                    >
                        <option value="">All Status Transitions</option>
                        <option value="planned">Planned</option>
                        <option value="in-progress">In Progress</option>
                        <option value="on-hold">On Hold</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Audit Logs Timeline / Table -->
            <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] border-collapse text-left">
                        <thead>
                            <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900/50">
                                <th class="px-6 py-4 text-xs font-bold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Timestamp</th>
                                <th class="px-6 py-4 text-xs font-bold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Author & Target</th>
                                <th class="px-6 py-4 text-xs font-bold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Update Description</th>
                                <th class="px-6 py-4 text-xs font-bold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Progress</th>
                                <th class="px-6 py-4 text-xs font-bold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Project Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 text-sm">
                            <tr v-for="log in props.logs.data" :key="log.id" class="group hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30">
                                <!-- Timestamp -->
                                <td class="px-6 py-4 whitespace-nowrap text-neutral-500 dark:text-neutral-400">
                                    <div class="flex items-center gap-1.5" :title="new Date(log.created_at).toLocaleString()">
                                        <Clock class="size-3.5" />
                                        <span>{{ log.created_at_human }}</span>
                                    </div>
                                </td>

                                <!-- Author & Target Project -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1 text-xs text-neutral-600 dark:text-neutral-400">
                                            <User class="size-3 text-neutral-400" />
                                            <span class="font-semibold">{{ log.updater_user?.name || 'System' }}</span>
                                        </div>
                                        <div v-if="log.project" class="inline-flex items-center gap-1">
                                            <Link :href="`/projects/${log.project.id}`" class="font-medium text-neutral-900 hover:text-blue-600 dark:text-neutral-100 dark:hover:text-blue-400 inline-flex items-center gap-0.5">
                                                {{ log.project.title }}
                                                <ArrowUpRight class="size-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                            </Link>
                                        </div>
                                        <span v-else class="text-xs text-neutral-400">Orphaned Update</span>
                                    </div>
                                </td>

                                <!-- Description -->
                                <td class="px-6 py-4 max-w-[320px]">
                                    <div class="flex items-start gap-1.5">
                                        <FileText class="size-4 mt-0.5 shrink-0 text-neutral-400" />
                                        <p class="text-neutral-700 dark:text-neutral-300 break-words line-clamp-2" :title="log.description">
                                            {{ log.description }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Progress Percentage -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="log.progress_percentage !== null" class="flex items-center gap-2">
                                        <div class="w-12 bg-neutral-200 dark:bg-neutral-800 rounded-full h-1.5">
                                            <div class="bg-blue-600 h-1.5 rounded-full" :style="{ width: log.progress_percentage + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">
                                            {{ log.progress_percentage }}%
                                        </span>
                                    </div>
                                    <span v-else class="text-xs text-neutral-400">-</span>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold uppercase tracking-wider"
                                          :class="getStatusClass(log.status)">
                                        {{ log.status_label || 'Unchanged' }}
                                    </span>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="props.logs.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    No audit logs found matching the selected filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <Pagination :links="props.logs.links" />
            </div>
        </div>
    </AdminLayout>
</template>
