<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { edit, index, update } from '@/routes/tags';
import { BreadcrumbItem, Tag } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

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
});

function submit() {
    form.put(update(props.tag.id).url);
}
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
                        <h1 class="text-xl font-bold text-neutral-950 dark:text-neutral-50">Edit Tag</h1>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Update tag details</p>
                    </div>
                </div>
            </div>

            <!-- Form Container Card -->
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <Label for="name" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Name</Label>
                        <Input
                            id="name"
                            type="text"
                            class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                            v-model="form.name"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="flex items-center gap-4 border-t border-neutral-100 pt-5 dark:border-neutral-800/80">
                        <Button :disabled="form.processing" class="rounded-xl px-5">Update Tag</Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
