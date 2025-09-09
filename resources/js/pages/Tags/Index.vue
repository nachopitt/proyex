<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Tag } from '@/types';
import { index, create, destroy, edit } from '@/routes/tags';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button'
import { ref, computed } from 'vue'
import { Input } from '@/components/ui/input'

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
        links: any[];
    };
}

const props = defineProps<Props>();

const search = ref('')

const filteredTags = computed(() => {
  return props.tags.data.filter(tag =>
    tag.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

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
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <Input v-model="search" placeholder="Search tags..." class="max-w-sm" />
                            <Link :href="create().url">
                                <Button>Create Tag</Button>
                            </Link>
                        </div>

                        <div v-if="filteredTags.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                            <div v-for="tag in filteredTags" :key="tag.id" class="border rounded-lg p-4 bg-gray-50 dark:bg-[#161615] flex flex-col justify-between">
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

                        <div class="mt-4 flex justify-center">
                            <div class="flex space-x-1">
                                <template v-for="(link, index) in props.tags.links" :key="index">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            'px-3 py-1 border rounded-md',
                                            { 'font-bold bg-gray-50 dark:bg-[#161615]': link.active },
                                            { 'hover:bg-gray-50 hover:dark:bg-[#161615]': link.url },
                                        ]"
                                    >
                                        <span v-html="link.label"></span>
                                    </Link>
                                    <span
                                        v-else
                                        v-html="link.label"
                                        class="px-3 py-1 border rounded-md cursor-not-allowed"
                                    />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
