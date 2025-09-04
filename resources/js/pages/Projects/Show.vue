<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index } from '@/routes/projects';
import { BreadcrumbItem, Project } from '@/types';

interface Props {
  project: Project;
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
        title: props.project.title.slice(0, 20) + "...",
        href: index().url,
    },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 pt-0">
          <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-6">
            {{ props.project.title }}
          </h2>
          <h3 class="text-lg font-medium text-gray-900">Description</h3>
          <p class="mt-1 text-sm text-gray-600">{{ props.project.description }}</p>

          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900">Reporter</h3>
            <p class="mt-1 text-sm text-gray-600">{{ props.project.reporter.name }}</p>
          </div>

          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900">Owner</h3>
            <p class="mt-1 text-sm text-gray-600">{{ props.project.owner.name }}</p>
          </div>

          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900">Project Updates</h3>
            <ul class="mt-1 space-y-4">
              <li v-for="update in props.project.project_updates" :key="update.id" class="bg-gray-50 p-4 rounded-lg shadow">
                <p class="text-base text-gray-800">{{ update.description }}</p>
                <p class="text-xs text-gray-500 mt-2">Updated by: {{ update.updater.name }}</p>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
