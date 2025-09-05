<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Project } from '@/types';
import { index, show, create } from '@/routes/projects';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button'

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
        links: any[]; // You might want to define a more specific type for pagination links
    };
}

const props = defineProps<Props>();
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 pt-0">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-6">Projects</h2>
                        <Link :href="create().url">
                            <Button>Create Project</Button>
                        </Link>
                    </div>

                    <div v-if="projects.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                        <div v-for="project in projects.data" :key="project.id" class="border rounded-lg p-4 flex flex-col justify-between">
                            <div>
                                <h4 class="text-md font-semibold">
                                    <Link :href="show(project.id).url" class="text-gray-900">
                                        {{ project.title }}
                                    </Link>
                                </h4>
                                <p class="text-sm text-gray-600 mt-2">{{ project.description }}</p>
                            </div>
                            <div class="mt-4 text-xs text-gray-500 space-y-1">
                                <p>Priority: {{ project.priority_label }}</p>
                                <p>Start date: {{ project.start_date }}</p>
                                <p>Due date: {{ project.due_date }}</p>
                                <p>End date: {{ project.end_date }}</p>
                                <p>Reported by: {{ project.reporter.name }}</p>
                                <p>Owned by: {{ project.owner.name }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900">No projects yet</h3>
                        <p class="text-sm text-gray-600">Create one to get started!</p>
                        <Link :href="create().url" class="mt-4 inline-block">
                            <Button>Create Project</Button>
                        </Link>
                    </div>

                    <div class="mt-4 flex justify-center">
                        <div class="flex space-x-1">
                            <template v-for="(link, index) in projects.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-1 border rounded-md',
                                        { 'font-bold bg-gray-200': link.active },
                                        { 'hover:bg-gray-100': link.url },
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="px-3 py-1 border rounded-md text-gray-400 cursor-not-allowed"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
