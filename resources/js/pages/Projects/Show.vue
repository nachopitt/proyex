<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index, show, edit } from '@/routes/projects';
import { create as createUpdate } from '@/routes/projects/updates';
import { edit as editUpdate } from '@/routes/updates';
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
        <div class="overflow-hidden shadow-xl sm:rounded-lg p-6 pt-0">
          <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold mb-4 mt-6">
              {{ props.project.title }}
            </h2>
            <div class="flex items-center gap-4">
              <Link :href="createUpdate(props.project.id).url">
                <Button>Create Project Update</Button>
              </Link>
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

            <div class="mt-4">
              <h3 class="text-lg font-medium">Priority</h3>
              <p class="mt-1 text-sm text-muted-foreground">{{ props.project.priority_label }}</p>
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
              <p class="mt-1 text-sm text-muted-foreground">{{ props.project.reporter.name }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium">Owned by</h3>
              <p class="mt-1 text-sm text-muted-foreground">{{ props.project.owner.name }}</p>
            </div>

            <div class="mt-4">
              <h3 class="text-lg font-medium">Project Updates</h3>
              <ul class="mt-1 space-y-4">
                <li v-for="update in props.project.project_updates" :key="update.id" class="bg-gray-50 dark:bg-[#161615] p-4 rounded-lg shadow">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="text-base">{{ update.description }}</p>
                      <p class="text-sm text-muted-foreground mt-2">Status: {{ update.status_label }}</p>
                      <p class="text-sm text-muted-foreground mt-2">Progress: {{ update.progress_percentage }}</p>
                      <p class="text-sm text-muted-foreground mt-2">Updated by: {{ update.updater.name }}</p>
                    </div>
                    <Link :href="editUpdate(update.id).url">
                      <Button variant="outline" size="sm">Edit</Button>
                    </Link>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
