<script setup lang="ts">
import LogProgress from '@/components/LogProgress.vue';
import { Button } from '@/components/ui/button';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { edit, index, show } from '@/routes/projects';
import { edit as editUpdate } from '@/routes/updates';
import { BreadcrumbItem, PaginationLink, Project, ProjectUpdate, Status } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Layers, MessageSquare, Tag as TagIcon, User } from 'lucide-vue-next';

interface Props {
    project: Project;
    project_updates: {
        data: ProjectUpdate[];
        links: PaginationLink[];
    };
    statuses: Status[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Projects',
        href: index().url,
    },
    {
        title: props.project.title.slice(0, 20) + '...',
        href: show(props.project.id).url,
    },
];

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
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl space-y-6 p-6 sm:px-6 lg:px-8">
            <!-- Header/Navigation Section -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-200 pb-5 dark:border-neutral-800">
                <div class="flex items-center gap-3">
                    <Link
                        :href="index().url"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        <ArrowLeft class="size-4" />
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold text-neutral-950 dark:text-neutral-50">Project Details</h1>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">View and manage project settings</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <Link :href="edit(props.project.id).url">
                        <Button class="rounded-xl">Edit Project</Button>
                    </Link>
                </div>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column (2/3 width) -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Project Details Card -->
                    <div class="space-y-4 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <div>
                            <div class="flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400">
                                <span v-if="project.parent" class="flex items-center gap-1.5">
                                    <Layers class="size-3.5" />
                                    Parent:
                                    <Link :href="show(project.parent.id).url" class="font-semibold text-blue-600 hover:underline dark:text-blue-400">
                                        {{ project.parent.title }}
                                    </Link>
                                </span>
                                <span v-if="project.parent" class="text-neutral-300 dark:text-neutral-700">|</span>
                                <span>Project Details</span>
                            </div>
                            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-50">
                                {{ props.project.title }}
                            </h2>
                        </div>

                        <div class="border-t border-neutral-100 pt-4 dark:border-neutral-800/80">
                            <h3 class="mb-1 text-sm font-bold text-neutral-900 dark:text-neutral-100">Description</h3>
                            <p class="text-sm leading-relaxed whitespace-pre-line text-neutral-600 dark:text-neutral-300">
                                {{ props.project.description || 'No description provided.' }}
                            </p>
                        </div>

                        <div v-if="project.tags && project.tags.length > 0" class="border-t border-neutral-100 pt-4 dark:border-neutral-800/80">
                            <h3 class="mb-2 text-sm font-bold text-neutral-900 dark:text-neutral-100">Tags</h3>
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="tag in project.tags"
                                    :key="tag.id"
                                    class="inline-flex items-center gap-1 rounded-md bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                >
                                    <TagIcon class="size-3" />
                                    {{ tag.name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Subtasks/Children Projects Widget -->
                    <div
                        v-if="project.children && project.children.length > 0"
                        class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                    >
                        <div class="border-b border-neutral-100 p-6 dark:border-neutral-800/80">
                            <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">Subtasks & Children Projects</h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Breakdown of initiatives linked to this project</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-neutral-100 dark:divide-neutral-800/50">
                                <thead class="bg-neutral-50 dark:bg-neutral-950/50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                        >
                                            Title
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                        >
                                            Status
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                        >
                                            Priority
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400"
                                        >
                                            Due Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100 bg-white dark:divide-neutral-800/50 dark:bg-neutral-900">
                                    <tr
                                        v-for="child in project.children"
                                        :key="child.id"
                                        class="transition-colors hover:bg-neutral-50/50 dark:hover:bg-neutral-950/20"
                                    >
                                        <td class="px-6 py-4 text-sm font-bold whitespace-nowrap text-neutral-900 dark:text-neutral-100">
                                            <Link :href="show(child.id).url" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ child.title }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset"
                                                :class="getStatusBadgeClass(child.current_status_label)"
                                            >
                                                {{ child.current_status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center gap-1.5 text-xs font-bold"
                                                :class="getPriorityTextColor(child.priority_label)"
                                            >
                                                <span class="h-1.5 w-1.5 rounded-full" :class="getPriorityColor(child.priority_label)"></span>
                                                {{ child.priority_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-neutral-500 dark:text-neutral-400">
                                            {{ child.due_date }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Log Progress Card -->
                    <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <div class="mb-4">
                            <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">Log Progress</h3>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Record a new progress entry or update status</p>
                        </div>
                        <LogProgress :project="props.project" :statuses="props.statuses" />
                    </div>
                </div>

                <!-- Right Column (1/3 width) -->
                <div class="space-y-6">
                    <!-- Project Overview Widget -->
                    <div class="space-y-6 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <div>
                            <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">Overview</h3>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Current status and properties</p>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-neutral-500 dark:text-neutral-400">Status</span>
                            <span
                                class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset"
                                :class="getStatusBadgeClass(props.project.current_status_label)"
                            >
                                {{ props.project.current_status_label }}
                            </span>
                        </div>

                        <!-- Priority Badge -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-neutral-500 dark:text-neutral-400">Priority</span>
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-bold"
                                :class="getPriorityTextColor(props.project.priority_label)"
                            >
                                <span class="h-1.5 w-1.5 rounded-full" :class="getPriorityColor(props.project.priority_label)"></span>
                                {{ props.project.priority_label }} Priority
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <span class="font-medium text-neutral-500 dark:text-neutral-400">Progress</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ props.project.current_progress_percentage }}%</span>
                            </div>
                            <div class="h-2.5 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-blue-500 to-emerald-500 transition-all duration-500"
                                    :style="{ width: props.project.current_progress_percentage + '%' }"
                                ></div>
                            </div>
                        </div>

                        <div class="space-y-3 border-t border-neutral-100 pt-4 dark:border-neutral-800/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-neutral-500 dark:text-neutral-400">
                                    <User class="size-3.5 text-neutral-400" />
                                    <span class="font-medium text-neutral-700 dark:text-neutral-300">{{
                                        props.project.assigned_user?.name ?? 'Unassigned'
                                    }}</span>
                                </span>
                                <span class="text-3xs text-neutral-400 dark:text-neutral-500">Assigned</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-neutral-500 dark:text-neutral-400">
                                    <User class="size-3.5 text-neutral-400 opacity-60" />
                                    <span class="font-medium text-neutral-700 dark:text-neutral-300">{{ props.project.reporter_user.name }}</span>
                                </span>
                                <span class="text-3xs text-neutral-400 dark:text-neutral-500">Reporter</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-neutral-500 dark:text-neutral-400">
                                    <Calendar class="size-3.5 text-neutral-400" />
                                    <span class="font-medium text-neutral-700 dark:text-neutral-300">{{ props.project.start_date }}</span>
                                </span>
                                <span class="text-3xs text-neutral-400 dark:text-neutral-500">Start Date</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-neutral-500 dark:text-neutral-400">
                                    <Calendar class="size-3.5 text-neutral-400" />
                                    <span class="font-medium text-amber-600 text-neutral-700 dark:text-amber-400 dark:text-neutral-300">{{
                                        props.project.due_date
                                    }}</span>
                                </span>
                                <span class="text-3xs text-neutral-400 dark:text-neutral-500">Due Date</span>
                            </div>
                            <div v-if="props.project.end_date" class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-neutral-500 dark:text-neutral-400">
                                    <Calendar class="size-3.5 text-neutral-400" />
                                    <span class="font-medium text-emerald-600 text-neutral-700 dark:text-emerald-400 dark:text-neutral-300">{{
                                        props.project.end_date
                                    }}</span>
                                </span>
                                <span class="text-3xs text-neutral-400 dark:text-neutral-500">End Date</span>
                            </div>
                        </div>
                    </div>

                    <!-- Project Updates Activity Feed Widget -->
                    <div
                        class="flex flex-col overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                    >
                        <div class="flex items-center justify-between border-b border-neutral-100 p-6 dark:border-neutral-800/80">
                            <div class="space-y-0.5">
                                <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">Project Updates</h3>
                                <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Activity and progress history</p>
                            </div>
                            <MessageSquare class="size-5 text-neutral-400" />
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <div
                                v-if="props.project_updates.data.length === 0"
                                class="flex flex-1 flex-col items-center justify-center py-12 text-center"
                            >
                                <div class="mb-3 rounded-full bg-neutral-50 p-3 text-neutral-400 dark:bg-neutral-800 dark:text-neutral-500">
                                    <MessageSquare class="size-6" />
                                </div>
                                <p class="text-sm font-bold font-semibold text-neutral-700 dark:text-neutral-300">No updates yet</p>
                                <p class="mt-1 max-w-xs text-xs font-medium text-neutral-500 dark:text-neutral-400">
                                    Log progress to record the first activity update.
                                </p>
                            </div>

                            <div v-else class="space-y-6">
                                <div
                                    v-for="update in props.project_updates.data"
                                    :key="update.id"
                                    class="relative border-l-2 border-neutral-100 pb-2 pl-6 last:border-transparent last:pb-0 dark:border-neutral-800"
                                >
                                    <!-- Timeline Dot Indicator -->
                                    <div
                                        class="absolute top-1.5 -left-[7px] h-3 w-3 rounded-full border-2 border-white bg-blue-500 shadow-sm dark:border-neutral-900"
                                    ></div>

                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="text-xs font-semibold text-neutral-900 dark:text-neutral-100">
                                                {{ update.updater_user.name }}
                                            </span>
                                            <Link :href="editUpdate(update.id).url">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    class="text-3xs h-6 rounded-md px-2 text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400"
                                                >
                                                    Edit
                                                </Button>
                                            </Link>
                                        </div>

                                        <p class="text-sm leading-relaxed text-neutral-600 dark:text-neutral-300">
                                            {{ update.description }}
                                        </p>

                                        <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                                            <span class="text-3xs font-medium text-neutral-500 dark:text-neutral-400">Status:</span>
                                            <span
                                                class="text-3xs inline-flex items-center rounded px-1.5 py-0.5 font-semibold ring-1 ring-inset"
                                                :class="getStatusBadgeClass(update.status_label)"
                                            >
                                                {{ update.status_label }}
                                            </span>
                                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-neutral-200 dark:bg-neutral-800"></span>
                                            <span class="text-3xs font-medium text-neutral-500 dark:text-neutral-400">Progress:</span>
                                            <span class="text-3xs font-bold text-neutral-800 dark:text-neutral-200"
                                                >{{ update.progress_percentage }}%</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <Pagination :links="props.project_updates.links" class="mt-6" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom extra-small font sizing */
.text-2xs {
    font-size: 0.65rem;
}
.text-3xs {
    font-size: 0.55rem;
}
</style>
