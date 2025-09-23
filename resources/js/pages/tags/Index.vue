<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, PaginationLink, Tag } from '@/types';
import { index, create, destroy, edit } from '@/routes/tags';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button'
import { ref, watch } from 'vue'
import { debounce, pickBy } from 'lodash';
import { Input } from '@/components/ui/input'
import Pagination from '@/components/ui/pagination/Pagination.vue';

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

const search = ref(props.filters.search || '')

watch(search, debounce(() => {
    const query = pickBy({
        search: search.value,
    });

    router.get(index().url, query, {
        preserveState: true,
        replace: true
    });
}, 300));

const deleteTag = (id: number) => {
  if (confirm('Are you sure you want to delete this tag?')) {
    router.delete(destroy(id).url)
  }
}
</script>

<template>
    <Head title="Tags" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg p-6 pt-0">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <h2 class="text-2xl font-bold mb-4 mt-6">Tags</h2>
                        <Link :href="create().url">
                            <Button>Create Tag</Button>
                        </Link>
                    </div>

                    <div class="flex items-center space-x-2 flex-wrap">
                        <Input v-model="search" placeholder="Search tags..." class="max-w-sm" />
                    </div>

                    <Pagination :links="props.tags.links" class="mt-6"/>

                    <div v-if="props.tags.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                        <div v-for="tag in props.tags.data" :key="tag.id" class="border rounded-lg p-4 bg-gray-50 dark:bg-[#161615] flex flex-col justify-between">
                            <div>
                                <h4 class="text-md font-semibold">
                                    {{ tag.name }}
                                </h4>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <Link :href="edit(tag.id).url">
                                    <Button variant="outline" size="sm">Edit</Button>
                                </Link>
                                <Button variant="destructive" size="sm" @click="deleteTag(tag.id)">
                                    Delete
                                </Button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12">
                        <h3 class="text-lg font-medium">No tags found.</h3>
                        <p class="text-sm text-muted-foreground">Adjust your search or create a new tag!</p>
                    </div>

                    <Pagination :links="props.tags.links" class="mt-6" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
