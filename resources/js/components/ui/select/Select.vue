<script setup lang="ts">
import { SelectRoot } from 'reka-ui'

const props = defineProps<{
  modelValue?: string
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string): void
}>()
</script>

<template>
  <SelectRoot v-bind="props" @update:modelValue="emits('update:modelValue', $event)">
    <slot />
  </SelectRoot>
</template>
