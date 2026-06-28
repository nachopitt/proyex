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
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden p-6 pt-0 shadow-xl sm:rounded-lg">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h2 class="mt-6 mb-4 text-2xl font-bold">Tags</h2>
                        <Link :href="create().url">
                            <Button>Create Tag</Button>
                        </Link>
                    </div>

                    <div class="flex flex-wrap items-center space-x-2">
                        <Input v-model="search" placeholder="Search tags..." class="max-w-sm" />
                    </div>

                    <Pagination :links="props.tags.links" class="mt-6" />

                    <div v-if="props.tags.data.length > 0" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="tag in props.tags.data"
                            :key="tag.id"
                            class="flex flex-col justify-between rounded-lg border bg-gray-50 p-4 dark:bg-[#161615]"
                        >
                            <div>
                                <h4 class="text-md font-semibold">
                                    {{ tag.name }}
                                </h4>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <Link :href="edit(tag.id).url">
                                    <Button variant="outline" size="sm">Edit</Button>
                                </Link>
                                <Button variant="destructive" size="sm" @click="deleteTag(tag.id)"> Delete </Button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-12 text-center">
                        <h3 class="text-lg font-medium">No tags found.</h3>
                        <p class="text-sm text-muted-foreground">Adjust your search or create a new tag!</p>
                    </div>

                    <Pagination :links="props.tags.links" class="mt-6" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
