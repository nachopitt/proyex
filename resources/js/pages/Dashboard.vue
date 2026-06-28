<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { create, show } from '@/routes/projects';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { AlertCircle, ArrowRight, Calendar, CheckCircle2, FolderOpen, Layers, MessageSquare, Plus, TrendingUp, User } from 'lucide-vue-next';
import { computed } from 'vue';

interface Stats {
    active_projects: number;
    completed_projects: number;
    overdue_projects: number;
    total_subtasks: number;
    completion_rate: number;
}

interface ProjectBrief {
    id: number;
    title: string;
    due_date: string;
    assigned_user?: { id: number; name: string };
    current_status_label: string;
    priority_label: string;
}

interface RecentUpdate {
    id: number;
    description: string;
    status_label: string;
    progress_percentage: number;
    project: { id: number; title: string };
    updater_user: { id: number; name: string };
    created_at_human: string;
}

interface Breakdown {
    name: string;
    value: string;
    count: number;
}

const props = defineProps<{
    stats: Stats;
    recent_updates: RecentUpdate[];
    upcoming_projects: ProjectBrief[];
    priority_breakdown: Breakdown[];
    status_breakdown: Breakdown[];
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Calculation of total priority items to show proportional bars
const totalPriorityCount = computed(() => {
    return props.priority_breakdown.reduce((sum, item) => sum + item.count, 0);
});

// Calculations for SVG Progress Dial
const radius = 50;
const circumference = 2 * Math.PI * radius; // ~314.159
const strokeDashoffset = computed(() => {
    const rate = Math.min(Math.max(props.stats.completion_rate, 0), 100);
    return circumference - (rate / 100) * circumference;
});

// Color mapping helper for Priorities
const getPriorityColor = (value: string) => {
    switch (value.toLowerCase()) {
        case 'high':
            return 'bg-rose-500 dark:bg-rose-600';
        case 'medium':
            return 'bg-amber-500 dark:bg-amber-600';
        case 'low':
            return 'bg-emerald-500 dark:bg-emerald-600';
        default:
            return 'bg-neutral-500';
    }
};

// Text color mapping helper for Priorities
const getPriorityTextColor = (value: string) => {
    switch (value.toLowerCase()) {
        case 'high':
            return 'text-rose-600 dark:text-rose-400';
        case 'medium':
            return 'text-amber-600 dark:text-amber-400';
        case 'low':
            return 'text-emerald-600 dark:text-emerald-400';
        default:
            return 'text-neutral-600';
    }
};

// Color mapping helper for Statuses
const getStatusBadgeClass = (value: string) => {
    switch (value.toLowerCase()) {
        case 'planned':
            return 'bg-slate-100 text-slate-700 dark:bg-slate-900/50 dark:text-slate-300 ring-slate-500/10 dark:ring-slate-500/20';
        case 'in-progress':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-300 ring-blue-500/10 dark:ring-blue-500/20';
        case 'on-hold':
            return 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300 ring-amber-500/10 dark:ring-amber-500/20';
        case 'completed':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300 ring-emerald-500/10 dark:ring-emerald-500/20';
        case 'cancelled':
            return 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300 ring-rose-500/10 dark:ring-rose-500/20';
        default:
            return 'bg-neutral-50 text-neutral-600 dark:bg-neutral-900/30 dark:text-neutral-400 ring-neutral-500/10 dark:ring-neutral-500/20';
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl space-y-6 p-6 sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div
                class="animate-fade-in relative overflow-hidden rounded-2xl border border-neutral-200 bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-950 p-6 text-white shadow-xl dark:border-neutral-800 dark:shadow-neutral-950/50"
            >
                <div class="pointer-events-none absolute top-0 right-0 -mt-6 -mr-6 h-48 w-48 rounded-full bg-neutral-800/20 blur-3xl"></div>
                <div class="pointer-events-none absolute bottom-0 left-1/3 -mb-10 h-72 w-72 rounded-full bg-neutral-700/10 blur-3xl"></div>

                <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-2">
                        <h1 class="text-3xl font-extrabold tracking-tight">Welcome back, {{ user?.name }}!</h1>
                        <p class="max-w-xl text-sm text-neutral-300 md:text-base">
                            Here is a summary of your workspace. You have successfully completed
                            <span class="font-semibold text-emerald-400">{{ stats.completion_rate }}%</span> of all top-level projects.
                        </p>
                    </div>
                    <div>
                        <Link
                            :href="create().url"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-neutral-900 shadow-md transition-all hover:bg-neutral-100 active:scale-95"
                        >
                            <Plus class="size-4" />
                            New Project
                        </Link>
                    </div>
                </div>
            </div>

            <!-- KPI Metrics Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Active Projects Card -->
                <div
                    class="group flex items-center justify-between rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Active Projects</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ stats.active_projects }}
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-blue-50 p-3 text-blue-600 transition-transform duration-300 group-hover:scale-110 dark:bg-blue-950/30 dark:text-blue-400"
                    >
                        <FolderOpen class="size-6" />
                    </div>
                </div>

                <!-- Completed Projects Card -->
                <div
                    class="group flex items-center justify-between rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Completed Projects</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ stats.completed_projects }}
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-emerald-50 p-3 text-emerald-600 transition-transform duration-300 group-hover:scale-110 dark:bg-emerald-950/30 dark:text-emerald-400"
                    >
                        <CheckCircle2 class="size-6" />
                    </div>
                </div>

                <!-- Overdue Projects Card -->
                <div
                    class="group flex items-center justify-between rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Overdue Tasks</p>
                        <p
                            class="text-3xl font-bold"
                            :class="stats.overdue_projects > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-neutral-900 dark:text-neutral-100'"
                        >
                            {{ stats.overdue_projects }}
                        </p>
                    </div>
                    <div
                        class="rounded-xl p-3 transition-transform duration-300 group-hover:scale-110"
                        :class="
                            stats.overdue_projects > 0
                                ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400'
                                : 'bg-neutral-50 text-neutral-500 dark:bg-neutral-800'
                        "
                    >
                        <AlertCircle class="size-6" />
                    </div>
                </div>

                <!-- Subtasks Card -->
                <div
                    class="group flex items-center justify-between rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Subtasks & Milestones</p>
                        <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ stats.total_subtasks }}
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-purple-50 p-3 text-purple-600 transition-transform duration-300 group-hover:scale-110 dark:bg-purple-950/30 dark:text-purple-400"
                    >
                        <Layers class="size-6" />
                    </div>
                </div>
            </div>

            <!-- Analysis and Progress Charts Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Completion Rate Dial Widget -->
                <div
                    class="flex flex-col items-center justify-center space-y-4 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="w-full text-left">
                        <h3 class="flex items-center gap-2 text-base font-bold text-neutral-900 dark:text-neutral-100">
                            <TrendingUp class="size-4 text-emerald-500" />
                            Completion Efficiency
                        </h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Top-level project completion rate</p>
                    </div>

                    <div class="relative flex items-center justify-center py-4">
                        <svg class="h-36 w-36 -rotate-90 transform">
                            <!-- Background Circle -->
                            <circle
                                cx="72"
                                cy="72"
                                :r="radius"
                                stroke-width="12"
                                stroke="currentColor"
                                class="text-neutral-100 dark:text-neutral-800"
                                fill="transparent"
                            />
                            <!-- Indicator Circle -->
                            <circle
                                cx="72"
                                cy="72"
                                :r="radius"
                                stroke-width="12"
                                stroke="url(#gradient)"
                                stroke-linecap="round"
                                :stroke-dasharray="circumference"
                                :stroke-dashoffset="strokeDashoffset"
                                fill="transparent"
                                class="transition-all duration-1000 ease-out"
                            />
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#10b981" />
                                    <stop offset="100%" stop-color="#3b82f6" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="absolute flex flex-col items-center justify-center">
                            <span class="text-3xl font-extrabold text-neutral-900 dark:text-neutral-100">{{ stats.completion_rate }}%</span>
                            <span class="text-2xs font-semibold tracking-wider text-neutral-500 uppercase">Done</span>
                        </div>
                    </div>

                    <p class="px-4 text-center text-xs text-neutral-500 dark:text-neutral-400">
                        All closed projects compared to planned, active, or on-hold tasks. Keep pushing!
                    </p>
                </div>

                <!-- Priority Breakdown Card -->
                <div
                    class="flex flex-col justify-between rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">Priority Profile</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Active distribution by project priority</p>
                    </div>

                    <div class="my-6 space-y-4">
                        <div v-for="item in priority_breakdown" :key="item.value" class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold">
                                <span :class="getPriorityTextColor(item.value)">{{ item.name }} Priority</span>
                                <span class="text-neutral-500 dark:text-neutral-400">
                                    {{ item.count }} {{ item.count === 1 ? 'project' : 'projects' }}
                                </span>
                            </div>
                            <div class="h-2.5 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                                <div
                                    :class="getPriorityColor(item.value)"
                                    class="h-full rounded-full transition-all duration-500"
                                    :style="{ width: totalPriorityCount > 0 ? (item.count / totalPriorityCount) * 100 + '%' : '0%' }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-neutral-100 pt-3 text-xs text-neutral-500 dark:border-neutral-800 dark:text-neutral-400">
                        Focus on resolving high-priority blockers first.
                    </div>
                </div>

                <!-- Status Distribution Card -->
                <div
                    class="flex flex-col justify-between rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="mb-4 space-y-1">
                        <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">Status Breakdowns</h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Project status distributions</p>
                    </div>

                    <div class="flex flex-1 flex-col justify-center divide-y divide-neutral-100 dark:divide-neutral-800/50">
                        <div v-for="item in status_breakdown" :key="item.value" class="flex items-center justify-between py-2 text-sm">
                            <span
                                class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset"
                                :class="getStatusBadgeClass(item.value)"
                            >
                                {{ item.name }}
                            </span>
                            <span class="font-bold text-neutral-800 dark:text-neutral-200">
                                {{ item.count }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Layout: Recent Updates and Upcoming Deadlines -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Upcoming Project Deadlines -->
                <div
                    class="flex flex-col overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="flex items-center justify-between border-b border-neutral-100 p-6 dark:border-neutral-800/80">
                        <div class="space-y-0.5">
                            <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">Upcoming Deadlines</h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Projects with approaching due dates</p>
                        </div>
                        <Calendar class="size-5 text-neutral-400" />
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        <div v-if="upcoming_projects.length === 0" class="flex flex-1 flex-col items-center justify-center py-12 text-center">
                            <div class="mb-3 rounded-full bg-neutral-50 p-3 text-neutral-400 dark:bg-neutral-800 dark:text-neutral-500">
                                <Calendar class="size-6" />
                            </div>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">All caught up!</p>
                            <p class="mt-1 max-w-xs text-xs text-neutral-500 dark:text-neutral-400">No pending projects due in the near future.</p>
                        </div>

                        <ul v-else class="-my-3 divide-y divide-neutral-100 dark:divide-neutral-800/50">
                            <li
                                v-for="proj in upcoming_projects"
                                :key="proj.id"
                                class="group flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="space-y-1">
                                    <Link
                                        :href="show(proj.id).url"
                                        class="flex items-center gap-1.5 font-semibold text-neutral-950 transition-colors hover:text-blue-600 dark:text-neutral-50 dark:hover:text-blue-400"
                                    >
                                        {{ proj.title }}
                                        <ArrowRight
                                            class="size-3.5 opacity-0 transition-all duration-300 group-hover:translate-x-0.5 group-hover:opacity-100"
                                        />
                                    </Link>
                                    <div class="flex flex-wrap items-center gap-x-2.5 text-xs text-neutral-500 dark:text-neutral-400">
                                        <span class="flex items-center gap-1">
                                            <User class="size-3" />
                                            {{ proj.assigned_user?.name ?? 'Unassigned' }}
                                        </span>
                                        <span class="inline-block h-1 w-1 rounded-full bg-neutral-300 dark:bg-neutral-700"></span>
                                        <span class="flex items-center gap-1 font-medium text-amber-600 dark:text-amber-400">
                                            Due {{ proj.due_date }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset"
                                        :style="{ color: getPriorityTextColor(proj.priority_label) }"
                                        :class="getStatusBadgeClass(proj.current_status_label)"
                                    >
                                        {{ proj.current_status_label }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div
                    class="flex flex-col overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="flex items-center justify-between border-b border-neutral-100 p-6 dark:border-neutral-800/80">
                        <div class="space-y-0.5">
                            <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">Workspace Activity</h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Latest progress updates logged by the team</p>
                        </div>
                        <MessageSquare class="size-5 text-neutral-400" />
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        <div v-if="recent_updates.length === 0" class="flex flex-1 flex-col items-center justify-center py-12 text-center">
                            <div class="mb-3 rounded-full bg-neutral-50 p-3 text-neutral-400 dark:bg-neutral-800 dark:text-neutral-500">
                                <MessageSquare class="size-6" />
                            </div>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">No updates yet</p>
                            <p class="mt-1 max-w-xs text-xs text-neutral-500 dark:text-neutral-400">
                                Start logging updates inside project pages to see them here.
                            </p>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="update in recent_updates"
                                :key="update.id"
                                class="relative border-l-2 border-neutral-100 pb-2 pl-6 last:border-transparent last:pb-0 dark:border-neutral-800"
                            >
                                <!-- Timeline Dot Indicator -->
                                <div
                                    class="absolute top-1.5 -left-[7px] h-3 w-3 rounded-full border-2 border-white bg-blue-500 shadow-sm dark:border-neutral-900"
                                ></div>

                                <div class="space-y-1">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <span class="text-xs font-semibold text-neutral-900 dark:text-neutral-100">
                                            {{ update.updater_user.name }}
                                        </span>
                                        <span class="text-3xs font-medium text-neutral-400 dark:text-neutral-500">
                                            {{ update.created_at_human }}
                                        </span>
                                    </div>

                                    <p class="text-sm leading-relaxed text-neutral-600 dark:text-neutral-300">
                                        {{ update.description }}
                                    </p>

                                    <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                                        <span class="text-neutral-500 dark:text-neutral-400">on</span>
                                        <Link
                                            :href="show(update.project.id).url"
                                            class="font-medium text-blue-600 hover:underline dark:text-blue-400"
                                        >
                                            {{ update.project.title }}
                                        </Link>
                                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-neutral-200 dark:bg-neutral-800"></span>
                                        <span class="text-neutral-500 dark:text-neutral-400">Progress:</span>
                                        <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ update.progress_percentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Custom extra-small font sizing */
.text-2xs {
    font-size: 0.65rem;
}
.text-3xs {
    font-size: 0.55rem;
}
</style>
