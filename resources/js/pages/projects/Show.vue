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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden p-6 pt-0 shadow-xl sm:rounded-lg">
                    <div class="flex items-center justify-between">
                        <h2 class="mt-6 mb-4 text-2xl font-bold">
                            {{ props.project.title }}
                        </h2>
                        <div class="flex items-center gap-4">
                            <Link :href="edit(props.project.id).url">
                                <Button>Edit Project</Button>
                            </Link>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Description</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.description }}</p>
                        </div>

                        <div v-if="project.parent" class="mt-4">
                            <h3 class="text-lg font-medium">Parent project</h3>
                            <Link :href="show(project.parent.id).url" class="text-sm font-semibold hover:underline">
                                {{ project.parent.title }}
                            </Link>
                        </div>

                        <div v-if="project.children && project.children.length > 0" class="mt-4">
                            <h3 class="text-lg font-medium">Children projects</h3>
                            <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                                <table class="responsive-table min-w-full divide-y divide-gray-200 bg-white">
                                    <thead class="hidden bg-gray-50 sm:table-header-group">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                                Title
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                                Current Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                                Priority
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                                Due Date
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="block divide-y divide-gray-200 sm:table-row-group">
                                        <tr v-for="child in project.children" :key="child.id" class="block border-b sm:table-row sm:border-none">
                                            <td
                                                data-label="Title"
                                                class="block px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:table-cell"
                                            >
                                                <Link :href="show(child.id).url" class="hover:underline">
                                                    {{ child.title }}
                                                </Link>
                                            </td>
                                            <td
                                                data-label="Current Status"
                                                class="block px-6 py-4 text-sm whitespace-nowrap text-gray-500 sm:table-cell"
                                            >
                                                {{ child.current_status_label }}
                                            </td>
                                            <td data-label="Priority" class="block px-6 py-4 text-sm whitespace-nowrap text-gray-500 sm:table-cell">
                                                {{ child.priority_label }}
                                            </td>
                                            <td data-label="Due Date" class="block px-6 py-4 text-sm whitespace-nowrap text-gray-500 sm:table-cell">
                                                {{ child.due_date }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Tags</h3>
                            <div v-if="project.tags && project.tags.length > 0" class="mt-2 flex flex-wrap gap-1">
                                <span
                                    v-for="tag in project.tags"
                                    :key="tag.id"
                                    class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-500/10 ring-inset"
                                >
                                    {{ tag.name }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Priority</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.priority_label }}</p>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Current status</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.current_status_label }}</p>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Current progress</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.current_progress_percentage }}</p>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Start date</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.start_date }}</p>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Due date</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.due_date }}</p>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">End date</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.end_date }}</p>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Reported by</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.reporter_user.name }}</p>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Assigned to</h3>
                            <p class="mt-1 text-sm text-muted-foreground">{{ props.project.assigned_user?.name ?? 'Unassigned' }}</p>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Log Progress</h3>
                            <LogProgress :project="props.project" :statuses="props.statuses" />
                        </div>

                        <Pagination :links="props.project_updates.links" />

                        <div class="mt-4">
                            <h3 class="text-lg font-medium">Project Updates</h3>
                            <ul class="mt-1 space-y-4">
                                <li
                                    v-for="update in props.project_updates.data"
                                    :key="update.id"
                                    class="rounded-lg bg-gray-50 p-4 shadow dark:bg-[#161615]"
                                >
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-base">{{ update.description }}</p>
                                            <p class="mt-2 text-sm text-muted-foreground">Status: {{ update.status_label }}</p>
                                            <p class="mt-2 text-sm text-muted-foreground">Progress: {{ update.progress_percentage }}</p>
                                            <p class="mt-2 text-sm text-muted-foreground">Updated by: {{ update.updater_user.name }}</p>
                                        </div>
                                        <Link :href="editUpdate(update.id).url">
                                            <Button variant="outline" size="sm">Edit</Button>
                                        </Link>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <Pagination :links="props.project_updates.links" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Responsive table styles */
@media (max-width: 639px) {
    /* Tailwind's 'sm' breakpoint is 640px */
    .responsive-table td {
        display: flex; /* Use flexbox for better alignment of label and content */
        flex-direction: column; /* Stack label and content vertically */
        align-items: flex-start; /* Align content to the start */
        padding-left: 1rem; /* Ensure consistent padding */
        padding-right: 1rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        /* Removed border-bottom from td */
    }

    .responsive-table td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #6b7280; /* A slightly muted color for the label */
        margin-bottom: 0.25rem; /* Small space between label and value */
        width: 100%; /* Allow label to take full width */
    }

    /* The tr already has border-b, so this might not be needed or might conflict */
    /* .responsive-table tr:last-child td {
        border-bottom: none;
    } */
}
</style>
