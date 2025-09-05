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
import { BreadcrumbItem, Priority, Project } from '@/types'
import { dashboard } from '@/routes'

interface Props {
    project: Project;
    priorities: Priority[];
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
    priority: String(props.project.priority),
    start_date: props.project.start_date,
    due_date: props.project.due_date,
})

function submit() {
    form.put(update(props.project.id).url)
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container px-4 sm:px-8">
            <div class="py-8">
                <Heading :title="'Edit Project'" />
            </div>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
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
                        <SelectTrigger>
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
    </AppLayout>
</template>
