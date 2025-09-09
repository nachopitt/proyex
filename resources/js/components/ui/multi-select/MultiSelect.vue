<script setup lang="ts">
import { computed } from 'vue';
import Multiselect from 'vue-multiselect';

interface Tag {
    id: number;
    name: string;
}

const props = defineProps<{
    modelValue: Tag[];
    options: Tag[];
    placeholder?: string;
    label?: string;
    trackBy?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const selectedTags = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <Multiselect
            v-model="selectedTags"
            :options="options"
            :multiple="true"
            :close-on-select="false"
            :clear-on-select="false"
            :preserve-search="true"
            :placeholder="placeholder || 'Select tags'"
            label="name"
            :track-by="trackBy || 'id'"
        >
            <template v-slot:tag="{ option, remove }">
                <span class="multiselect__tag">
                    <span>{{ option.name }}</span>
                    <i class="multiselect__tag-icon" @click="remove(option)"></i>
                </span>
            </template>
            <template v-slot:option="{ option }">
                <span>{{ option.name }}</span>
            </template>
            <template v-slot:noOptions>
                <span>No tags found. Consider creating new ones.</span>
            </template>
        </Multiselect>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
