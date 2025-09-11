<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import { index, edit, update } from '@/routes/tags';
import { BreadcrumbItem, Tag } from '@/types'
import { dashboard } from '@/routes'

interface Props {
    tag: Tag;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Tags',
        href: index().url,
    },
    {
        title: 'Edit Tag',
        href: edit(props.tag.id).url,
    },
];

const form = useForm({
    name: props.tag.name,
})

function submit() {
    form.put(update(props.tag.id).url)
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container px-4 py-6 sm:px-8">
            <div class="mb-8 space-y-0.5">
                <Heading :title="'Edit Tag'" />
            </div>
            <div class="max-w-xl space-y-12">
                <div class="flex flex-col space-y-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing">Update</Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
