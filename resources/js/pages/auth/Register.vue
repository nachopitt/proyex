<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard, register as registerRoute } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Create User',
        href: registerRoute().url,
    },
];

const backUrl = computed(() => {
    return page.props.auth.user?.isAdmin ? '/admin/users' : dashboard().url;
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create User" />

        <div class="mx-auto max-w-3xl space-y-6 p-6 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-200 pb-5 dark:border-neutral-800">
                <div class="flex items-center gap-3">
                    <Link
                        :href="backUrl"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        <ArrowLeft class="size-4" />
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold text-neutral-950 dark:text-neutral-50">Create User</h1>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Enter details below to create a new user account</p>
                    </div>
                </div>
            </div>

            <!-- Form Container Card -->
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <Form
                    v-bind="RegisteredUserController.store.form()"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                >
                    <div class="space-y-4">
                        <div>
                            <Label for="name" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Name</Label>
                            <Input
                                id="name"
                                type="text"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="name"
                                name="name"
                                placeholder="Full name"
                            />
                            <InputError class="mt-2" :message="errors.name" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label for="first_name" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">First name</Label>
                                <Input
                                    id="first_name"
                                    type="text"
                                    class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                    required
                                    :tabindex="2"
                                    name="first_name"
                                    placeholder="First name"
                                />
                                <InputError class="mt-2" :message="errors.first_name" />
                            </div>
                            <div>
                                <Label for="last_name" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Last name</Label>
                                <Input
                                    id="last_name"
                                    type="text"
                                    class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                    required
                                    :tabindex="3"
                                    name="last_name"
                                    placeholder="Last name"
                                />
                                <InputError class="mt-2" :message="errors.last_name" />
                            </div>
                        </div>

                        <div>
                            <Label for="email" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Email address</Label>
                            <Input
                                id="email"
                                type="email"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                required
                                :tabindex="4"
                                autocomplete="email"
                                name="email"
                                placeholder="email@example.com"
                            />
                            <InputError class="mt-2" :message="errors.email" />
                        </div>

                        <div>
                            <Label for="password" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                required
                                :tabindex="5"
                                autocomplete="new-password"
                                name="password"
                                placeholder="Password"
                            />
                            <InputError class="mt-2" :message="errors.password" />
                        </div>

                        <div>
                            <Label for="password_confirmation" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Confirm password</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                required
                                :tabindex="6"
                                autocomplete="new-password"
                                name="password_confirmation"
                                placeholder="Confirm password"
                            />
                            <InputError class="mt-2" :message="errors.password_confirmation" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 border-t border-neutral-100 pt-5 dark:border-neutral-800/80">
                        <Button type="submit" class="rounded-xl px-5" tabindex="7" :disabled="processing">
                            <LoaderCircle v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
                            Create User
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
