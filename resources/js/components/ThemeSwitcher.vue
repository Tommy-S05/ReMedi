<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { Contrast, Moon, Sun } from 'lucide-vue-next';
import { computed, type Component } from 'vue';
import Button from './ui/button/Button.vue';

type Theme = 'light' | 'dark' | 'system';

const { appearance: currentTheme, updateAppearance: setNextTheme } = useAppearance();

interface ThemeConfig {
    value: Theme;
    Icon: Component;
    label: string;
}

const themes: readonly ThemeConfig[] = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Contrast, label: 'System' },
];

/**
 * Propiedad computada que encuentra la configuración del tema actual.
 */
const currentThemeConfig = computed<ThemeConfig>(() => {
    return themes.find((theme) => theme.value === currentTheme.value) || themes[0];
});

/**
 * Función que se ejecuta al hacer clic en el botón.
 * Calcula el siguiente tema y llama directamente a `setNextTheme`.
 */
const toggleTheme = () => {
    const currentIndex = themes.findIndex((theme) => theme.value === currentTheme.value);
    const nextIndex = (currentIndex + 1) % themes.length;
    const nextThemeValue = themes[nextIndex].value;

    setNextTheme(nextThemeValue);
};
</script>

<template>
    <Button
        variant="ghost"
        size="icon"
        class="ml-2"
        @click="toggleTheme"
        :aria-label="`Cambiar a siguiente tema. Actual: ${currentThemeConfig.label}`"
    >
        <component :is="currentThemeConfig.Icon" class="h-5 w-5" />
    </Button>
</template>
