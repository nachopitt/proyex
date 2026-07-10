<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const passwordInput = ref<InstanceType<typeof Input> | null>(null);
</script>

<template>
    <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
        <div class="max-w-xl space-y-6">
            <HeadingSmall title="Delete account" description="Delete your account and all of its resources" />
            <div class="border-red-150 space-y-4 rounded-xl border bg-red-50 p-5 dark:border-red-500/10 dark:bg-red-950/20">
                <div class="text-red-750 relative space-y-1 dark:text-red-300">
                    <p class="font-bold">Warning</p>
                    <p class="text-sm">Please proceed with caution, this cannot be undone.</p>
                </div>
                <Dialog>
                    <DialogTrigger as-child>
                        <Button variant="destructive" class="rounded-xl px-5">Delete account</Button>
                    </DialogTrigger>
                    <DialogContent class="rounded-2xl">
                        <Form
                            v-bind="ProfileController.destroy.form()"
                            reset-on-success
                            @error="() => passwordInput?.$el?.focus()"
                            :options="{
                                preserveScroll: true,
                            }"
                            class="space-y-6"
                            v-slot="{ errors, processing, reset, clearErrors }"
                        >
                            <DialogHeader class="space-y-3">
                                <DialogTitle class="text-lg font-bold">Are you sure you want to delete your account?</DialogTitle>
                                <DialogDescription class="text-sm text-neutral-500 dark:text-neutral-400">
                                    Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your
                                    password to confirm you would like to permanently delete your account.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="password" class="sr-only">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    ref="passwordInput"
                                    placeholder="Password"
                                    class="rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <DialogFooter class="gap-2 border-t border-neutral-100 pt-5 dark:border-neutral-800/80">
                                <DialogClose as-child>
                                    <Button
                                        variant="secondary"
                                        class="rounded-xl px-5"
                                        @click="
                                            () => {
                                                clearErrors();
                                                reset();
                                            }
                                        "
                                    >
                                        Cancel
                                    </Button>
                                </DialogClose>

                                <Button type="submit" variant="destructive" :disabled="processing" class="rounded-xl px-5"> Delete account </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    </div>
</template>
