<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { create, destroy, edit, index } from '@/routes/tags';
import { BreadcrumbItem, PaginationLink, Tag } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { debounce, pickBy } from 'lodash';
import { Plus, Tag as TagIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Tags',
        href: index().url,
    },
];

interface Props {
    tags: {
        data: Tag[];
        links: PaginationLink[];
    };
    filters: {
        search: string | null;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

watch(
    search,
    debounce(() => {
        const query = pickBy({
            search: search.value,
        });

        router.get(index().url, query, {
            preserveState: true,
            replace: true,
        });
    }, 300),
);

const deleteTag = (id: number) => {
    if (confirm('Are you sure you want to delete this tag?')) {
        router.delete(destroy(id).url);
    }
};
</script>

<template>
    <Head title="Tags" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl space-y-6 p-6 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-200 pb-5 dark:border-neutral-800">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-50">Tags</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Manage tags used for categorizing projects</p>
                </div>
                <Link :href="create().url">
                    <Button
                        class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 text-white hover:bg-neutral-800 dark:bg-neutral-50 dark:text-neutral-950 dark:hover:bg-neutral-200"
                    >
                        <Plus class="size-4" />
                        Create Tag
                    </Button>
                </Link>
            </div>

            <!-- Search/Filters Panel -->
            <div
                class="flex flex-wrap items-center gap-3 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="min-w-[240px] flex-1">
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Search tags..."
                        class="w-full rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-950"
                    />
                </div>
            </div>

            <!-- Pagination Top -->
            <Pagination :links="props.tags.links" class="mt-2" />

            <!-- Tags Grid -->
            <div v-if="props.tags.data.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="tag in props.tags.data"
                    :key="tag.id"
                    class="group flex flex-col justify-between rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-100 px-3 py-1.5 text-sm font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
                        >
                            <TagIcon class="size-4 text-neutral-400" />
                            {{ tag.name }}
                        </span>
                    </div>
                    <div class="mt-5 flex justify-end gap-2 border-t border-neutral-100 pt-3 dark:border-neutral-800/80">
                        <Link :href="edit(tag.id).url">
                            <Button variant="outline" size="sm" class="rounded-xl px-3 py-1.5 text-xs font-semibold"> Edit </Button>
                        </Link>
                        <Button variant="destructive" size="sm" class="rounded-xl px-3 py-1.5 text-xs font-semibold" @click="deleteTag(tag.id)">
                            Delete
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center justify-center rounded-2xl border border-neutral-200 bg-white py-16 text-center dark:border-neutral-800 dark:bg-neutral-900"
            >
                <div class="mb-3 rounded-full bg-neutral-50 p-3 text-neutral-400 dark:bg-neutral-800 dark:text-neutral-500">
                    <TagIcon class="size-6" />
                </div>
                <h3 class="text-lg font-bold text-neutral-950 dark:text-neutral-50">No tags found</h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Try adjusting your search or create a new tag.</p>
            </div>

            <!-- Pagination Bottom -->
            <Pagination :links="props.tags.links" class="mt-6" />
        </div>
    </AppLayout>
</template>
