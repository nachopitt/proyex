<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index, show, edit } from '@/routes/projects';
import { BreadcrumbItem, Project } from '@/types';
import { Link } from '@inertiajs/vue3';

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
        href: show(props.project.id).url,
    },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 pt-0">
          <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-6">
              {{ props.project.title }}
            </h2>
            <Link :href="edit(props.project.id).url">
              <Button>Edit Project</Button>
            </Link>
          </div>

          <div class="mt-6 space-y-2">
            <div class="mt-4">
              <h3 class="text-lg font-medium text-gray-900">Description</h3>
              <p class="mt-1 text-sm text-gray-600">{{ props.project.description }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium text-gray-900">Priority</h3>
              <p class="mt-1 text-sm text-gray-600">{{ props.project.priority_label }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium text-gray-900">Start date</h3>
              <p class="mt-1 text-sm text-gray-600">{{ props.project.start_date }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium text-gray-900">Due date</h3>
              <p class="mt-1 text-sm text-gray-600">{{ props.project.due_date }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium text-gray-900">End date</h3>
              <p class="mt-1 text-sm text-gray-600">{{ props.project.end_date }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium text-gray-900">Reported by</h3>
              <p class="mt-1 text-sm text-gray-600">{{ props.project.reporter.name }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium text-gray-900">Owned by</h3>
              <p class="mt-1 text-sm text-gray-600">{{ props.project.owner.name }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium text-gray-900">Project Updates</h3>
              <ul class="mt-1 space-y-4">
                <li v-for="update in props.project.project_updates" :key="update.id" class="bg-gray-50 p-4 rounded-lg shadow">
                  <p class="text-base text-gray-800">{{ update.description }}</p>
                  <p class="text-sm text-gray-500 mt-2">Status: {{ update.status_label }}</p>
                  <p class="text-sm text-gray-500 mt-2">Progress: {{ update.progress_percentage }}</p>
                  <p class="text-sm text-gray-500 mt-2">Updated by: {{ update.updater.name }}</p>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
