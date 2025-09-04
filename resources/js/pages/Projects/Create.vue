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
import { store } from '@/routes/projects';
import { Priority } from '@/types'

interface Props {
  priorities: Priority;
}

const props = defineProps<Props>();

const form = useForm({
    title: '',
    description: '',
    priority: '2',
    start_date: '',
    due_date: '',
})

function submit() {
    form.post(store().url)
}
</script>

<template>
    <AppLayout>
        <div class="container px-4 sm:px-8">
            <div class="py-8">
                <Heading :title="'Create Project'" />
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
                    <Button :disabled="form.processing">Create</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
