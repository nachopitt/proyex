<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, PaginationLink, Project } from '@/types';
import { index, show, create } from '@/routes/projects';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button'
import Pagination from '@/components/ui/pagination/Pagination.vue';

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
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search);

watch(
    search,
    debounce((value: string) => {
        const query = value ? { search: value } : {};
        router.get(
            index().url,
            query,
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 300),
);
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg p-6 pt-0">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold mb-4 mt-6">Projects</h2>
                        <div class="flex items-center space-x-4">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search..."
                                class="px-4 py-2 border rounded-md"
                            />
                            <Link :href="create().url">
                                <Button>Create Project</Button>
                            </Link>
                        </div>
                    </div>

                    <Pagination :links="props.projects.links" />

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
                        <h3 class="text-lg font-medium">No projects yet</h3>
                        <p class="text-sm text-muted-foreground">Create one to get started!</p>
                        <Link :href="create().url" class="mt-4 inline-block">
                            <Button>Create Project</Button>
                        </Link>
                    </div>

                    <Pagination :links="props.projects.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
