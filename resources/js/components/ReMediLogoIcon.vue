<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
  isDark?: boolean
  size?: number
}

const props = withDefaults(defineProps<Props>(), {
  isDark: false,
  size: 32
})

const imageError = ref(false)
const isDark = computed(() => props.isDark)

const logoSrc = computed(() => {
  if (imageError.value) {
    // Fallback to a simple text logo if image fails to load
    return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHRleHQgeD0iNTAiIHk9IjUwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiMxRTQwQUYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjNlbSI+UmVNZWRpPC90ZXh0Pjwvc3ZnPg=='
  }
  
  return '/assets/logo_icon.png'
})

const logoAlt = 'ReMedi - Recordatorios de Medicamentos'

const logoClasses = computed(() => [
  'transition-all duration-300 ease-in-out',
  'hover:scale-105',
  'object-contain',
  'select-none',
  {
    'drop-shadow-sm hover:drop-shadow-md': !isDark,
    'brightness-110 hover:brightness-125': isDark,
  }
])

const handleImageError = () => {
  imageError.value = true
}
</script>

<template>
  <img 
    :src="logoSrc"
    :alt="logoAlt"
    :width="size"
    :height="size"
    :class="logoClasses"
    loading="lazy"
    @error="handleImageError"
  />
</template>
