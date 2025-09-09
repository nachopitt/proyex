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
import { index, create, store } from '@/routes/projects';
import { BreadcrumbItem, Priority, User, Tag } from '@/types'
import { dashboard } from '@/routes'
import Multiselect from 'vue-multiselect'
import { ref } from 'vue';

interface Props {
  priorities: Priority[];
  users: User[];
  tags: Tag[];
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
    priority: '2',
    assigned_user_id: '',
    start_date: '',
    due_date: '',
    tags: [] as Tag[], // Explicitly type as Tag[]
})

// Create a local ref for tags
const availableTags = ref(props.tags);

function submit() {
    form.post(store().url)
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
                <Heading :title="'Create Project'" />
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
                                            v-for="priority in props.priorities"
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
                            <InputError class="mt-2" :message="form.errors.priority" />
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
                            <Button :disabled="form.processing">Create</Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
