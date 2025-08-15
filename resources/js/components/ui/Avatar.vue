<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  name: string
  size?: 'sm' | 'md' | 'lg'
}>(), {
  size: 'md'
})

const initials = computed(() => {
  return props.name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-8 h-8 text-sm'
    case 'lg': return 'w-16 h-16 text-xl'
    default: return 'w-12 h-12 text-base'
  }
})

// Generate a consistent color based on the name
const bgColor = computed(() => {
  const colors = [
    '#3b82f6', // blue
    '#ef4444', // red
    '#10b981', // emerald
    '#f59e0b', // amber
    '#8b5cf6', // violet
    '#06b6d4', // cyan
    '#84cc16', // lime
    '#f97316', // orange
  ]
  
  let hash = 0
  for (let i = 0; i < props.name.length; i++) {
    hash = props.name.charCodeAt(i) + ((hash << 5) - hash)
  }
  
  return colors[Math.abs(hash) % colors.length]
})
</script>

<template>
  <div 
    :class="[
      'rounded-full flex items-center justify-center font-semibold text-white',
      sizeClasses
    ]"
    :style="{ backgroundColor: bgColor }"
  >
    {{ initials }}
  </div>
</template>
