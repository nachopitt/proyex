<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard, register as registerRoute } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create User" />

        <div class="container px-4 py-6 sm:px-8">
            <div class="mb-8 space-y-0.5">
                <Heading title="Create User" description="Enter details below to create a new user account" />
            </div>

            <div class="max-w-xl space-y-12">
                <div class="flex flex-col space-y-6">
                    <Form
                        v-bind="RegisteredUserController.store.form()"
                        :reset-on-success="['password', 'password_confirmation']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <div class="space-y-4">
                            <div>
                                <Label for="name">Name</Label>
                                <Input id="name" type="text" class="mt-1 block w-full" required autofocus :tabindex="1" autocomplete="name" name="name" placeholder="Full name" />
                                <InputError class="mt-2" :message="errors.name" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label for="first_name">First name</Label>
                                    <Input id="first_name" type="text" class="mt-1 block w-full" required :tabindex="2" name="first_name" placeholder="First name" />
                                    <InputError class="mt-2" :message="errors.first_name" />
                                </div>
                                <div>
                                    <Label for="last_name">Last name</Label>
                                    <Input id="last_name" type="text" class="mt-1 block w-full" required :tabindex="3" name="last_name" placeholder="Last name" />
                                    <InputError class="mt-2" :message="errors.last_name" />
                                </div>
                            </div>

                            <div>
                                <Label for="email">Email address</Label>
                                <Input id="email" type="email" class="mt-1 block w-full" required :tabindex="4" autocomplete="email" name="email" placeholder="email@example.com" />
                                <InputError class="mt-2" :message="errors.email" />
                            </div>

                            <div>
                                <Label for="password">Password</Label>
                                <Input id="password" type="password" class="mt-1 block w-full" required :tabindex="5" autocomplete="new-password" name="password" placeholder="Password" />
                                <InputError class="mt-2" :message="errors.password" />
                            </div>

                            <div>
                                <Label for="password_confirmation">Confirm password</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                    :tabindex="6"
                                    autocomplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Confirm password"
                                />
                                <InputError class="mt-2" :message="errors.password_confirmation" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" class="mt-2" tabindex="7" :disabled="processing">
                                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                                Create User
                            </Button>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
