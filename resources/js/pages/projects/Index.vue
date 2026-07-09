<script setup lang="ts">
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { create, index, show } from '@/routes/projects';
import { BreadcrumbItem, PaginationLink, Priority, Project, Status, User } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, Calendar, FolderOpen, Plus, Tag as TagIcon, User as UserIcon } from 'lucide-vue-next';
import { debounce, pickBy } from 'lodash';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Projects',
        href: index().url,
    },
];

interface Props {
    projects: {
        data: Project[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
        status: string;
        priority: string;
        assignee: string;
    };
    statuses: Status[];
    priorities: Priority[];
    users: User[];
}

const props = defineProps<Props>();

const search = ref(props.filters.search);
const status = ref(props.filters.status);
const priority = ref(props.filters.priority);
const assignee = ref(props.filters.assignee);

watch(
    [search, status, priority, assignee],
    debounce(() => {
        const query = pickBy({
            search: search.value,
            status: status.value,
            priority: priority.value,
            assignee: assignee.value,
        });

        router.get(index().url, query, {
            preserveState: true,
            replace: true,
        });
    }, 300),
);

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
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl space-y-6 p-6 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-200 pb-5 dark:border-neutral-800">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-50">Projects</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Manage and track your team's workspace initiatives</p>
                </div>
                <Link :href="create().url">
                    <Button class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 text-white hover:bg-neutral-800 dark:bg-neutral-50 dark:text-neutral-950 dark:hover:bg-neutral-200">
                        <Plus class="size-4" />
                        Create Project
                    </Button>
                </Link>
            </div>

            <!-- Filters Panel -->
            <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex-1 min-w-[240px]">
                    <Input v-model="search" type="text" placeholder="Search projects..." class="w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950" />
                </div>
                <div class="flex flex-wrap gap-2">
                    <select v-model="status" class="h-9 rounded-xl border border-neutral-200 bg-white px-3 text-sm text-neutral-700 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-300">
                        <option value="">All Statuses</option>
                        <option v-for="item in statuses" :key="item.id" :value="item.id">
                            {{ item.name }}
                        </option>
                    </select>
                    <select v-model="priority" class="h-9 rounded-xl border border-neutral-200 bg-white px-3 text-sm text-neutral-700 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-300">
                        <option value="">All Priorities</option>
                        <option v-for="item in priorities" :key="item.id" :value="item.id">
                            {{ item.name }}
                        </option>
                    </select>
                    <select v-model="assignee" class="h-9 rounded-xl border border-neutral-200 bg-white px-3 text-sm text-neutral-700 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-300">
                        <option value="">All Assignees</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Pagination Top -->
            <Pagination :links="props.projects.links" class="mt-2" />

            <!-- Projects Grid -->
            <div v-if="props.projects.data.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="project in props.projects.data"
                    :key="project.id"
                    class="group flex flex-col justify-between rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <span
                                class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset"
                                :class="getStatusBadgeClass(project.current_status_label)"
                            >
                                {{ project.current_status_label }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-bold"
                                :class="getPriorityTextColor(project.priority_label)"
                            >
                                <span class="h-1.5 w-1.5 rounded-full" :class="getPriorityColor(project.priority_label)"></span>
                                {{ project.priority_label }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <h4 class="text-lg font-bold text-neutral-900 dark:text-neutral-50">
                                <Link :href="show(project.id).url" class="group/title inline-flex items-center gap-1.5 hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ project.title }}
                                    <ArrowRight class="size-4 opacity-0 transition-all duration-300 group-hover/title:translate-x-0.5 group-hover/title:opacity-100" />
                                </Link>
                            </h4>
                            <p class="mt-1.5 text-sm text-neutral-500 line-clamp-2 dark:text-neutral-400">
                                {{ project.description }}
                            </p>
                        </div>

                        <div v-if="project.tags && project.tags.length > 0" class="mt-3 flex flex-wrap gap-1.5">
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

                    <div class="mt-5 space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <span class="text-neutral-500 dark:text-neutral-400">Progress</span>
                                <span class="text-neutral-800 dark:text-neutral-200">{{ project.current_progress_percentage }}%</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-blue-500 to-emerald-500 transition-all duration-500"
                                    :style="{ width: project.current_progress_percentage + '%' }"
                                ></div>
                            </div>
                        </div>

                        <div class="space-y-2 border-t border-neutral-100 pt-3 dark:border-neutral-800/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-neutral-500 dark:text-neutral-400">
                                    <UserIcon class="size-3.5 text-neutral-400" />
                                    <span class="font-medium text-neutral-700 dark:text-neutral-300">{{ project.assigned_user?.name ?? 'Unassigned' }}</span>
                                </span>
                                <span class="text-neutral-400 dark:text-neutral-500 text-3xs">Assigned</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-neutral-500 dark:text-neutral-400">
                                    <UserIcon class="size-3.5 text-neutral-400 opacity-60" />
                                    <span class="font-medium text-neutral-700 dark:text-neutral-300">{{ project.reporter_user.name }}</span>
                                </span>
                                <span class="text-neutral-400 dark:text-neutral-500 text-3xs">Reporter</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-neutral-500 dark:text-neutral-400">
                                    <Calendar class="size-3.5 text-neutral-400" />
                                    <span class="font-medium text-neutral-700 dark:text-neutral-300 text-right">{{ project.start_date }} to {{ project.due_date }}</span>
                                </span>
                                <span class="text-neutral-400 dark:text-neutral-500 text-3xs">Timeline</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-neutral-200 bg-white py-16 text-center dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 rounded-full bg-neutral-50 p-3 text-neutral-400 dark:bg-neutral-800 dark:text-neutral-500">
                    <FolderOpen class="size-6" />
                </div>
                <h3 class="text-lg font-bold text-neutral-950 dark:text-neutral-50">No projects found</h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Try adjusting your search or filters.</p>
            </div>

            <!-- Pagination Bottom -->
            <Pagination :links="props.projects.links" class="mt-6" />
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
