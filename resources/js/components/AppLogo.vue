<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useSidebar } from "@/components/ui/sidebar";

const {
    open,
    openMobile,
} = useSidebar()
</script>

<template>
    <div v-if="open || openMobile" class="flex w-full size-16 items-center justify-center rounded-md">
        <AppLogoIcon class-name="h-16" />
    </div>
    <div v-else class="flex w-full size-8 items-center justify-center rounded-md">
        <img src="/assets/logo_icon.png" alt="Logo" class="w-auto h-6" />
    </div>
<!--    <div class="ml-1 grid flex-1 text-left text-sm">-->
<!--        <span class="mb-0.5 truncate font-semibold leading-none">ReMedi</span>-->
<!--    </div>-->
</template>
