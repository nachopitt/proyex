<script setup lang="ts">
import { SelectViewport } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: string
}>()
</script>

<template>
  <SelectViewport
    :class="cn('-mx-1 my-1 h-full max-h-[200px] w-full', props.class)"
  >
    <slot />
  </SelectViewport>
</template>
