<script setup lang="ts">
import Pagination from '@/components/ui/pagination/Pagination.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard, register as registerRoute } from '@/routes';
import { BreadcrumbItem, PaginationLink, User } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertCircle, Search, Shield, ShieldAlert, ShieldCheck, UserCheck, UserPlus, UserX } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Props {
    users: {
        data: User[];
        links: PaginationLink[];
    };
    filters: {
        search?: string;
        active?: string;
        role?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');
const activeFilter = ref(props.filters.active || '');
const errorMessage = ref('');
const successMessage = ref('');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Admin Panel',
        href: '/admin/users',
    },
];

// Self-contained debounce function to prevent external dependency issues
function debounce<T extends (...args: any[]) => any>(fn: T, delay: number) {
    let timeoutId: ReturnType<typeof setTimeout> | null = null;
    return (...args: Parameters<T>) => {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(() => {
            fn(...args);
        }, delay);
    };
}

const applyFilters = debounce(() => {
    router.get(
        '/admin/users',
        {
            search: search.value,
            role: roleFilter.value,
            active: activeFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 300);

watch([search, roleFilter, activeFilter], () => {
    applyFilters();
});

const getUserRole = (user: User) => {
    if (user.user_roles && user.user_roles.length > 0) {
        return user.user_roles[0].role;
    }
    return 'user';
};

const handleRoleChange = (user: User, event: Event) => {
    const target = event.target as HTMLSelectElement;
    if (target) {
        updateUserRole(user, target.value);
    }
};

const isActive = (user: User) => {
    return user.user_profile ? user.user_profile.active : false;
};

const updateUserRole = (user: User, newRole: string) => {
    errorMessage.value = '';
    successMessage.value = '';
    router.put(
        `/admin/users/${user.id}`,
        {
            role: newRole,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                successMessage.value = 'User role updated successfully.';
            },
            onError: (errors: any) => {
                errorMessage.value = errors.error || 'Failed to update user role.';
            },
        },
    );
};

const toggleActive = (user: User) => {
    errorMessage.value = '';
    successMessage.value = '';
    const currentActive = isActive(user);
    router.put(
        `/admin/users/${user.id}`,
        {
            active: !currentActive,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                successMessage.value = 'User active status updated successfully.';
            },
            onError: (errors: any) => {
                errorMessage.value = errors.error || 'Failed to update user active status.';
            },
        },
    );
};
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl space-y-6 p-6 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-200 pb-5 dark:border-neutral-800">
                <div>
                    <h1 class="flex items-center gap-2 text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-50">
                        <ShieldAlert class="size-8 text-neutral-900 dark:text-neutral-50" />
                        Admin Panel: Users
                    </h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">View, search, promote/demote and activate/deactivate user accounts.</p>
                </div>
                <Link :href="registerRoute().url">
                    <Button
                        class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 text-white hover:bg-neutral-800 dark:bg-neutral-50 dark:text-neutral-950 dark:hover:bg-neutral-200"
                    >
                        <UserPlus class="size-4" />
                        Create User
                    </Button>
                </Link>
            </div>

            <!-- Toast Messages -->
            <div v-if="errorMessage || successMessage" class="space-y-2">
                <div
                    v-if="errorMessage"
                    class="flex animate-in items-center gap-2 rounded-lg border border-rose-500/30 bg-rose-500/15 px-4 py-3 text-sm text-rose-600 shadow-sm duration-300 fade-in slide-in-from-top-2 dark:text-rose-400"
                >
                    <AlertCircle class="size-4" />
                    <span>{{ errorMessage }}</span>
                </div>
                <div
                    v-if="successMessage"
                    class="flex animate-in items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/15 px-4 py-3 text-sm text-emerald-600 shadow-sm duration-300 fade-in slide-in-from-top-2 dark:text-emerald-400"
                >
                    <ShieldCheck class="size-4" />
                    <span>{{ successMessage }}</span>
                </div>
            </div>

            <!-- Filters & Search Panel -->
            <div
                class="flex flex-wrap items-center gap-3 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="relative min-w-[240px] flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400 dark:text-neutral-500">
                        <Search class="size-4" />
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by name, email..."
                        class="w-full rounded-xl border border-neutral-200 bg-white py-2 pr-4 pl-10 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-100"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <select
                        v-model="roleFilter"
                        class="h-9 rounded-xl border border-neutral-200 bg-white px-3 text-sm text-neutral-700 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-300"
                    >
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <select
                        v-model="activeFilter"
                        class="h-9 rounded-xl border border-neutral-200 bg-white px-3 text-sm text-neutral-700 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-300"
                    >
                        <option value="">All Statuses</option>
                        <option value="true">Active</option>
                        <option value="false">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] border-collapse text-left">
                        <thead>
                            <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900/50">
                                <th class="px-6 py-4 text-xs font-bold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">
                                    User Details
                                </th>
                                <th class="px-6 py-4 text-xs font-bold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Role</th>
                                <th class="px-6 py-4 text-xs font-bold font-semibold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">
                                    Active Status
                                </th>
                                <th class="px-6 py-4 text-xs font-bold tracking-wider text-neutral-500 uppercase dark:text-neutral-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                            <tr
                                v-for="u in props.users.data"
                                :key="u.id"
                                class="group transition-colors duration-150 hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30"
                            >
                                <!-- User info (Name & Email) -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-10 items-center justify-center rounded-xl bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                        >
                                            <span class="text-sm font-bold uppercase">{{ u.name.charAt(0) }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-neutral-900 dark:text-neutral-100">
                                                {{ u.user_profile?.first_name }} {{ u.user_profile?.last_name }}
                                            </span>
                                            <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                                {{ u.email }} (username: {{ u.name }})
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role Dropdown -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <Shield class="size-4" :class="getUserRole(u) === 'admin' ? 'text-amber-500' : 'text-neutral-400'" />
                                        <select
                                            :value="getUserRole(u)"
                                            @change="handleRoleChange(u, $event)"
                                            class="rounded-lg border border-neutral-200 bg-white px-2 py-1.5 text-xs font-semibold focus:border-blue-500 focus:outline-none dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-200"
                                        >
                                            <option value="admin">Admin</option>
                                            <option value="user">User</option>
                                        </select>
                                    </div>
                                </td>

                                <!-- Active Status (Toggle Switch) -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Custom Switch Button -->
                                        <button
                                            @click="toggleActive(u)"
                                            type="button"
                                            :class="isActive(u) ? 'bg-emerald-600' : 'bg-neutral-200 dark:bg-neutral-800'"
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                        >
                                            <span
                                                :class="isActive(u) ? 'translate-x-4' : 'translate-x-0'"
                                                class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            />
                                        </button>

                                        <span
                                            class="flex items-center gap-1 text-xs font-semibold"
                                            :class="isActive(u) ? 'text-emerald-600 dark:text-emerald-400' : 'text-neutral-500'"
                                        >
                                            <component :is="isActive(u) ? UserCheck : UserX" class="size-3.5" />
                                            {{ isActive(u) ? 'Active' : 'Suspended' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Inline quick action options -->
                                <td class="px-6 py-4">
                                    <span class="text-xs text-neutral-400 dark:text-neutral-500">
                                        Registered: {{ new Date(u.created_at).toLocaleDateString() }}
                                    </span>
                                </td>
                            </tr>

                            <!-- Empty state table row -->
                            <tr v-if="props.users.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    No users found matching the selected criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Bottom -->
            <div class="mt-4">
                <Pagination :links="props.users.links" />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Any view adjustments if needed */
</style>
