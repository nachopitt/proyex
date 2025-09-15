<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import { Textarea } from '@/components/ui/textarea'
import { index, show } from '@/routes/projects';
import { edit as editUpdate, update as updateUpdate } from '@/routes/updates';
import { BreadcrumbItem, ProjectUpdate, Status } from '@/types'
import { dashboard } from '@/routes'
import DeleteProjectUpdate from '@/components/DeleteProjectUpdate.vue'

interface Props {
    projectUpdate: ProjectUpdate;
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
        title: props.projectUpdate.project.title.slice(0, 20) + "...",
        href: show(props.projectUpdate.project.id).url,
    },
    {
        title: 'Edit Project Update',
        href: editUpdate(props.projectUpdate.id).url,
    },
];

const form = useForm({
    description: props.projectUpdate.description,
})

function submit() {
    form.put(updateUpdate(props.projectUpdate.id).url)
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container px-4 py-6 sm:px-8">
            <div class="mb-8 space-y-0.5">
                <Heading :title="'Edit Project Update'" />
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

                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing">Update</Button>
                        </div>
                    </form>
                </div>
                <DeleteProjectUpdate :project-update="projectUpdate"/>
            </div>
        </div>
    </AppLayout>
</template>
