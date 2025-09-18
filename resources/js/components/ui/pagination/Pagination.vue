<script setup lang="ts">
import { PaginationLink } from '@/types';
import { Link } from '@inertiajs/vue3';

interface Props {
    links: PaginationLink[];
}

const { links } = defineProps<Props>();
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded dark:text-gray-500"
                    v-html="link.label"
                />
                <Link
                    v-else
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-100 focus:border-indigo-500 focus:text-indigo-500 dark:border-gray-700 dark:hover:bg-gray-700"
                    :class="{
                        'bg-gray-100 dark:bg-gray-700': link.active,
                    }"
                    :href="link.url"
                    preserve-scroll
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
