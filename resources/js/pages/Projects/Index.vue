<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Project } from '@/types';

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

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Projects</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900">Project List</h3>
                    <ul class="mt-4 space-y-2">
                        <li v-for="project in projects.data" :key="project.id" class="p-4 border rounded-md">
                            <h4 class="text-md font-semibold">{{ project.title }}</h4>
                            <p class="text-sm text-gray-600">{{ project.description }}</p>
                            <p class="text-xs text-gray-500">Status: {{ project.status }}</p>
                            <p class="text-xs text-gray-500">Priority: {{ project.priority }}</p>
                        </li>
                    </ul>
                    <!-- Basic Pagination Links (you'll need to style these) -->
                    <div class="mt-4 flex justify-between">
                        <template v-for="link in projects.links" :key="link.url">
                            <a :href="link.url" v-html="link.label" :class="{'font-bold': link.active}" class="px-3 py-1 border rounded-md"></a>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
