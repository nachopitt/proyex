<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    links: any[]; // You might want to define a more specific type for pagination links
}

const props = defineProps<Props>();
</script>

<template>
    <div class="mt-4 flex justify-center">
        <div class="flex space-x-1">
            <template v-for="(link, index) in props.links" :key="index">
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
</template>
