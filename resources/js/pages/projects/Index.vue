<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce, pickBy } from 'lodash';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, PaginationLink, Priority, Project, Status, User } from '@/types';
import { index, show, create } from '@/routes/projects';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button'
import Pagination from '@/components/ui/pagination/Pagination.vue';
import Input from '@/components/ui/input/Input.vue';

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

watch([search, status, priority, assignee], debounce(() => {
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
}, 300));

</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg p-6 pt-0">
                    <div class="flex justify-between items-center flex-wrap gap-4">
                        <h2 class="text-2xl font-bold mb-4 mt-6">Projects</h2>
                        <Link :href="create().url">
                            <Button>Create Project</Button>
                        </Link>
                    </div>

                    <div class="flex items-center space-x-2 flex-wrap">
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Search projects..."
                            class="max-w-sm"
                        />
                        <select v-model="status" class="h-9 px-4 py-2 border rounded-md dark:bg-transparent">
                            <option value="">All Statuses</option>
                            <option v-for="item in statuses" :key="item.id" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                        <select v-model="priority" class="h-9 px-4 py-2 border rounded-md dark:bg-transparent">
                            <option value="">All Priorities</option>
                            <option v-for="item in priorities" :key="item.id" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                        <select v-model="assignee" class="h-9 px-4 py-2 border rounded-md dark:bg-transparent">
                            <option value="">All Assignees</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }}
                            </option>
                        </select>
                    </div>

                    <Pagination :links="props.projects.links" class="mt-6" />

                    <div v-if="props.projects.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                        <div v-for="project in props.projects.data" :key="project.id" class="border rounded-lg p-4 bg-gray-50 dark:bg-[#161615] flex flex-col justify-between">
                            <div>
                                <h4 class="text-md font-semibold">
                                    <Link :href="show(project.id).url">
                                        {{ project.title }}
                                    </Link>
                                </h4>
                                <p class="text-sm text-muted-foreground mt-2">{{ project.description }}</p>
                            </div>

                            <div v-if="project.tags && project.tags.length > 0" class="mt-2 flex flex-wrap gap-1">
                                <span v-for="tag in project.tags" :key="tag.id" class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                    {{ tag.name }}
                                </span>
                            </div>

                            <div class="mt-4 text-xs text-muted-foreground space-y-1">
                                <p>Priority: {{ project.priority_label }}</p>
                                <p>Current status: {{ project.current_status_label }}</p>
                                <p>Current progress: {{ project.current_progress_percentage }}</p>
                                <p>Start date: {{ project.start_date }}</p>
                                <p>Due date: {{ project.due_date }}</p>
                                <p>End date: {{ project.end_date }}</p>
                                <p>Reported by: {{ project.reporter_user.name }}</p>
                                <p>Assigned to: {{ project.assigned_user?.name ?? 'Unassigned' }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12">
                        <h3 class="text-lg font-medium">No projects found</h3>
                        <p class="text-sm text-muted-foreground">Try adjusting your filters.</p>
                    </div>

                    <Pagination :links="props.projects.links" class="mt-6" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
