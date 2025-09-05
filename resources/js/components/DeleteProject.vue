<script setup lang="ts">
import ProjectController from '@/actions/App/Http/Controllers/ProjectController';
import { Form } from '@inertiajs/vue3';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Project } from '@/types';

interface Props {
    project: Project;
}

const props = defineProps<Props>();
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall title="Delete project" description="Delete the project all of its resources" />
        <div class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">Warning</p>
                <p class="text-sm">Please proceed with caution, this cannot be undone.</p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive">Delete project</Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="ProjectController.destroy.form(props.project.id)"
                        reset-on-success
                        :options="{
                            preserveScroll: true,
                        }"
                        class="space-y-6"
                        v-slot="{ processing, reset, }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle>Are you sure you want to delete the project?</DialogTitle>
                            <DialogDescription>
                                Once the project is deleted, all of its resources and data will also be permanently deleted.
                            </DialogDescription>
                        </DialogHeader>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button
                                    variant="secondary"
                                    @click="
                                        () => {
                                            reset();
                                        }
                                    "
                                >
                                    Cancel
                                </Button>
                            </DialogClose>

                            <Button type="submit" variant="destructive" :disabled="processing"> Delete project </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
