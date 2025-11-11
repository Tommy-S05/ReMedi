<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { useTranslations } from '@/composables/useTranslations';
import { router } from '@inertiajs/vue3';
import { CheckIcon, Globe } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    variant?: 'default' | 'ghost' | 'outline' | 'secondary' | 'destructive' | 'link';
    size?: 'default' | 'sm' | 'lg' | 'icon';
    showLabel?: boolean;
}

withDefaults(defineProps<Props>(), {
    variant: 'ghost',
    size: 'default',
    showLabel: true,
});

const { currentLocale } = useTranslations();

const languages = [
    {
        code: 'en',
        name: 'English',
        flag: '🇺🇸',
    },
    {
        code: 'es',
        name: 'Español',
        flag: '🇪🇸',
    },
];

const currentLanguage = computed(() => languages.find((lang) => lang.code === currentLocale.value) || languages[0]);

const changeLanguage = (locale: string) => {
    router.post(
        route('language.change', { locale }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger :as-child="true">
            <Button :variant="variant" :size="size" class="flex items-center gap-2">
                <Globe class="h-4 w-4" />
                <span v-if="showLabel && size !== 'icon'" class="hidden sm:inline"> {{ currentLanguage.flag }} {{ currentLanguage.name }} </span>
                <span v-else-if="size !== 'icon'" class="sm:hidden">
                    {{ currentLanguage.flag }}
                </span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-36">
            <DropdownMenuItem
                v-for="language in languages"
                :key="language.code"
                @click="changeLanguage(language.code)"
                :disabled="language.code === currentLocale"
                :class="{ 'bg-accent': language.code === currentLocale }"
                class="cursor-pointer"
            >
                <span class="flex w-full items-center justify-between gap-2">
                    <span class="flex items-center gap-2">
                        <span>{{ language.flag }}</span>
                        <span>{{ language.name }}</span>
                    </span>
                    <CheckIcon v-if="language.code === currentLocale" class="text-primary h-4 w-4" />
                </span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
