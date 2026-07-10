<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { create, index, store } from '@/routes/projects';
import { BreadcrumbItem, Priority, Project, Tag, User } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';

interface Props {
    priorities: Priority[];
    users: User[];
    tags: Tag[];
    projects: Project[];
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
        title: 'Create Project',
        href: create().url,
    },
];

const form = useForm({
    title: '',
    description: '',
    priority: '',
    assigned_user_id: '',
    start_date: '',
    due_date: '',
    tags: [] as Tag[], // Explicitly type as Tag[]
    parent_id: '',
});

// Create a local ref for tags
const availableTags = ref(props.tags);

function submit() {
    form.transform((data) => ({
        ...data,
        tags: data.tags.map((tag) => tag.name),
    })).post(store().url);
}

// Custom tag handler
const addTag = (newTagName: string) => {
    // Check if tag already exists to prevent duplicates
    if (!availableTags.value.some((tag) => tag.name === newTagName)) {
        const newTag: Tag = { id: Date.now() * -1, name: newTagName }; // Temporary negative ID
        availableTags.value.push(newTag);
        form.tags.push(newTag);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6 p-6 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-200 pb-5 dark:border-neutral-800">
                <div class="flex items-center gap-3">
                    <Link
                        :href="index().url"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        <ArrowLeft class="size-4" />
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold text-neutral-950 dark:text-neutral-50">Create Project</h1>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Add a new project to your workspace</p>
                    </div>
                </div>
            </div>

            <!-- Form Container Card -->
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <Label for="title" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Title</Label>
                            <Input
                                id="title"
                                type="text"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                v-model="form.title"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="sm:col-span-2">
                            <Label for="description" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Description</Label>
                            <Textarea
                                id="description"
                                class="mt-1.5 block min-h-[100px] w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                v-model="form.description"
                            />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <Label for="priority" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Priority</Label>
                            <Select v-model="form.priority">
                                <SelectTrigger class="mt-1.5 rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950">
                                    <SelectValue placeholder="Select a priority" />
                                </SelectTrigger>
                                <SelectContent class="rounded-xl">
                                    <SelectGroup>
                                        <SelectLabel>Priorities</SelectLabel>
                                        <SelectItem v-for="priority in props.priorities" :key="priority.id" :value="String(priority.id)">
                                            {{ priority.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="form.errors.priority" />
                        </div>

                        <div>
                            <Label for="assigned_user_id" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Assigned to</Label>
                            <Select v-model="form.assigned_user_id">
                                <SelectTrigger class="mt-1.5 rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950">
                                    <SelectValue placeholder="Select a user" />
                                </SelectTrigger>
                                <SelectContent class="rounded-xl">
                                    <SelectGroup>
                                        <SelectLabel>Users</SelectLabel>
                                        <SelectItem v-for="user in props.users" :key="user.id" :value="String(user.id)">
                                            {{ user.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="form.errors.assigned_user_id" />
                        </div>

                        <div>
                            <Label for="parent_id" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Parent Project</Label>
                            <Select v-model="form.parent_id">
                                <SelectTrigger class="mt-1.5 rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950">
                                    <SelectValue placeholder="Select a project" />
                                </SelectTrigger>
                                <SelectContent class="rounded-xl">
                                    <SelectGroup>
                                        <SelectLabel>Projects</SelectLabel>
                                        <SelectItem v-for="project in props.projects" :key="project.id" :value="String(project.id)">
                                            {{ project.title }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="form.errors.parent_id" />
                        </div>

                        <div>
                            <Label for="tags" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Tags</Label>
                            <Multiselect
                                v-model="form.tags"
                                :options="availableTags"
                                :multiple="true"
                                :taggable="true"
                                @tag="addTag"
                                placeholder="Select or create tags"
                                label="name"
                                track-by="name"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                            />
                            <InputError class="mt-2" :message="form.errors.tags" />
                            <!-- Per-tag errors -->
                            <div v-for="(tag, index) in form.tags" :key="index">
                                <InputError :message="form.errors[`tags.${index}`]" />
                            </div>
                        </div>

                        <div>
                            <Label for="start_date" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Start Date</Label>
                            <Input
                                id="start_date"
                                type="date"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                v-model="form.start_date"
                            />
                            <InputError class="mt-2" :message="form.errors.start_date" />
                        </div>

                        <div>
                            <Label for="due_date" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Due Date</Label>
                            <Input
                                id="due_date"
                                type="date"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                v-model="form.due_date"
                            />
                            <InputError class="mt-2" :message="form.errors.due_date" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 border-t border-neutral-100 pt-5 dark:border-neutral-800/80">
                        <Button :disabled="form.processing" class="rounded-xl px-5">Create Project</Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
