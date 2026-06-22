<script setup lang="ts">
import { onMounted, ref } from 'vue';

const version = ref<string>('');
const environment = ref<string>('');

onMounted(async () => {
    try {
        const response = await fetch('/api/version');
        const data = await response.json();
        version.value = data.version;
        environment.value = data.environment;
    } catch (error) {
        console.error('Failed to fetch version:', error);
        version.value = 'unknown';
        environment.value = 'production';
    }
});
</script>

<template>
    <div v-if="version" class="px-2 py-2 text-xs text-neutral-500 dark:text-neutral-400">
        <div class="flex items-center justify-between gap-2">
            <span class="truncate">{{ version }}</span>
            <span
                v-if="environment !== 'production'"
                class="inline-flex items-center rounded-full bg-yellow-50 px-1.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300"
            >
                {{ environment }}
            </span>
        </div>
    </div>
</template>
