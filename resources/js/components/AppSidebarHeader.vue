<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import NotificationBell from '@/components/NotificationBell.vue';
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();
</script>

<template>
    <header
        class="border-sidebar-border/70 flex h-16 shrink-0 items-center gap-2 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex flex-1 items-center justify-between gap-2">
            <SidebarTrigger class="-ml-1" />
            <div v-if="breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
            <div v-else class="flex-1">
                <slot name="title" />
            </div>

            <div class="ml-auto flex items-center gap-2">
                <slot name="actions" />
                <NotificationBell />
                <LanguageSwitcher variant="ghost" size="default" :showLabel="true" />
                <ThemeSwitcher />
            </div>
        </div>

        <!--<div class="flex items-center gap-2">-->
        <!--    <SidebarTrigger class="-ml-1" />-->
        <!--    <template v-if="breadcrumbs.length > 0">-->
        <!--        <Breadcrumbs :breadcrumbs="breadcrumbs" />-->
        <!--    </template>-->
        <!--</div>-->
    </header>
</template>
