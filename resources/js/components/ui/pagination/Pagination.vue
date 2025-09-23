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
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-500 border rounded dark:text-gray-600"
                    v-html="link.label"
                />
                <Link
                    v-else
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-200/50 focus:border-gray-400 focus:text-gray-800 dark:border-gray-800 dark:hover:bg-gray-800 dark:focus:border-gray-500 dark:focus:text-gray-200"
                    :class="{
                        'bg-gray-200/50 dark:bg-gray-800': link.active,
                    }"
                    :href="link.url"
                    preserve-scroll
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
