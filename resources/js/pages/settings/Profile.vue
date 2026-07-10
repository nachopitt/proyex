<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="max-w-xl space-y-6">
                    <HeadingSmall title="Profile information" description="Update your name and email address" />

                    <Form v-bind="ProfileController.update.form()" class="space-y-6" v-slot="{ errors, processing, recentlySuccessful }">
                        <div class="grid gap-2">
                            <Label for="name" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Name</Label>
                            <Input
                                id="name"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                name="name"
                                :default-value="user.name"
                                required
                                autocomplete="name"
                                placeholder="Full name"
                            />
                            <InputError class="mt-2" :message="errors.name" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="first_name" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">First name</Label>
                                <Input
                                    id="first_name"
                                    class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                    name="first_name"
                                    :default-value="user.user_profile?.first_name"
                                    required
                                    placeholder="First name"
                                />
                                <InputError class="mt-2" :message="errors.first_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="last_name" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Last name</Label>
                                <Input
                                    id="last_name"
                                    class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                    name="last_name"
                                    :default-value="user.user_profile?.last_name"
                                    required
                                    placeholder="Last name"
                                />
                                <InputError class="mt-2" :message="errors.last_name" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="email" class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Email address</Label>
                            <Input
                                id="email"
                                type="email"
                                class="mt-1.5 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                                name="email"
                                :default-value="user.email"
                                required
                                autocomplete="username"
                                placeholder="Email address"
                            />
                            <InputError class="mt-2" :message="errors.email" />
                        </div>

                        <div v-if="mustVerifyEmail && !user.email_verified_at">
                            <p class="-mt-4 text-sm text-muted-foreground">
                                Your email address is unverified.
                                <Link
                                    :href="send()"
                                    as="button"
                                    class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                >
                                    Click here to resend the verification email.
                                </Link>
                            </p>

                            <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                                A new verification link has been sent to your email address.
                            </div>
                        </div>

                        <div class="flex items-center gap-4 border-t border-neutral-100 pt-5 dark:border-neutral-800/80">
                            <Button :disabled="processing" class="rounded-xl px-5">Save</Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-show="recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                            </Transition>
                        </div>
                    </Form>
                </div>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
