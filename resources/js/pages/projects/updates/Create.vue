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
import { index, show } from '@/routes/projects';
import { create as createUpdate, store as storeUpdate } from '@/routes/projects/updates';
import { BreadcrumbItem, Project, Status } from '@/types'
import { dashboard } from '@/routes'

interface Props {
    project: Project;
    statuses: Status[];
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
        title: 'Create Project Update',
        href: createUpdate(props.project.id).url,
    },
];

const form = useForm({
    description: '',
    status: '1',
    progress_percentage: '',
})

function submit() {
    form.post(storeUpdate(props.project.id).url)
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container px-4 py-6 sm:px-8">
            <div class="mb-8 space-y-0.5">
                <Heading :title="'Create Project Update'" />
            </div>
            <div class="max-w-xl space-y-12">
                <div class="flex flex-col space-y-6">
                    <form @submit.prevent="submit" class="space-y-6">
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
                            <Label for="status">Status</Label>
                            <Select v-model="form.status">
                                <SelectTrigger class="mt-1">
                                    <SelectValue placeholder="Select a status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Statuses</SelectLabel>
                                        <SelectItem
                                            v-for="status in statuses"
                                            :key="status.id"
                                            :value="String(status.id)"
                                        >
                                            {{ status.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>

                        <div>
                            <Label for="progress_percentage">Progress</Label>
                            <Input
                                id="progress_percentage"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.progress_percentage"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.progress_percentage" />
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
