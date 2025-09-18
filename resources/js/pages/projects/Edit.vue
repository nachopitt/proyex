<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import { index, show, edit, update } from '@/routes/projects';
import { BreadcrumbItem, Priority, Project, User, Tag, Status } from '@/types'
import { dashboard } from '@/routes'
import DeleteProject from '@/components/DeleteProject.vue'
import Multiselect from 'vue-multiselect'
import { ref } from 'vue';


interface Props {
    project: Project;
    priorities: Priority[];
    statuses: Status[];
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
        title: props.project.title.slice(0, 20) + "...",
        href: show(props.project.id).url,
    },
    {
        title: 'Edit Project',
        href: edit(props.project.id).url,
    },
];

const form = useForm({
    title: props.project.title,
    description: props.project.description,
    priority: props.project.priority,
    current_status: props.project.current_status,
    current_progress_percentage: props.project.current_progress_percentage,
    assigned_user_id: String(props.project.assigned_user_id),
    start_date: props.project.start_date,
    due_date: props.project.due_date,
    tags: props.project.tags, // Initialize with Tag objects
    parent_id: props.project.parent_id?.toString() ?? null,
})

// Create a local ref for tags
const availableTags = ref(props.tags);

function submit() {
    form.transform(data => ({
        ...data,
        tags: data.tags.map(tag => tag.name),
    })).put(update(props.project.id).url)
}

// Custom tag handler
const addTag = (newTagName: string) => {
    // Check if tag already exists to prevent duplicates
    if (!availableTags.value.some(tag => tag.name === newTagName)) {
        const newTag: Tag = { id: Date.now() * -1, name: newTagName }; // Temporary negative ID
        availableTags.value.push(newTag);
        form.tags.push(newTag);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container px-4 py-6 sm:px-8">
            <div class="mb-8 space-y-0.5">
                <Heading :title="'Edit Project'" />
            </div>
            <div class="max-w-xl space-y-12">
                <div class="flex flex-col space-y-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <Label for="title">Title</Label>
                            <Input
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                class="mt-1 block w-full"
                                v-model="form.description"
                            />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <Label for="priority">Priority</Label>
                            <Select v-model="form.priority">
                                <SelectTrigger class="mt-1">
                                    <SelectValue placeholder="Select a priority" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Priorities</SelectLabel>
                                        <SelectItem
                                            v-for="priority in priorities"
                                            :key="priority.id"
                                            :value="String(priority.id)"
                                        >
                                            {{ priority.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="form.errors.priority" />
                        </div>

                        <div>
                            <Label for="assigned_user_id">Assigned to</Label>
                            <Select v-model="form.assigned_user_id">
                                <SelectTrigger class="mt-1">
                                    <SelectValue placeholder="Select a user" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Users</SelectLabel>
                                        <SelectItem
                                            v-for="user in props.users"
                                            :key="user.id"
                                            :value="String(user.id)"
                                        >
                                            {{ user.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="form.errors.assigned_user_id" />
                        </div>

                        <div>
                            <Label for="parent_id">Parent Project</Label>
                            <Select v-model="form.parent_id">
                                <SelectTrigger class="mt-1">
                                    <SelectValue placeholder="Select a project" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Projects</SelectLabel>
                                        <SelectItem
                                            v-for="project in props.projects"
                                            :key="project.id"
                                            :value="String(project.id)"
                                        >
                                            {{ project.title }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="form.errors.parent_id" />
                        </div>

                        <div>
                            <Label for="tags">Tags</Label>
                            <Multiselect
                                v-model="form.tags"
                                :options="availableTags"
                                :multiple="true"
                                :taggable="true"
                                @tag="addTag"
                                placeholder="Select or create tags"
                                label="name"
                                track-by="name"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.tags" />
                            <!-- Per-tag errors -->
                            <div v-for="(tag, index) in form.tags" :key="index">
                                <InputError :message="form.errors[`tags.${index}`]" />
                            </div>
                        </div>

                        <div>
                            <Label for="start_date">Start Date</Label>
                            <Input
                                id="start_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.start_date"
                            />
                            <InputError class="mt-2" :message="form.errors.start_date" />
                        </div>

                        <div>
                            <Label for="due_date">Due Date</Label>
                            <Input
                                id="due_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.due_date"
                            />
                            <InputError class="mt-2" :message="form.errors.due_date" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing">Update</Button>
                        </div>
                    </form>
                </div>
                <DeleteProject :project="project" />
            </div>
        </div>
    </AppLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
