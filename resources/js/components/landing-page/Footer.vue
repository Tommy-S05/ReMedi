<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { useTranslations } from '@/composables/useTranslations';
import ReMediLogoIcon from '../ReMediLogoIcon.vue';

const { t } = useTranslations();
const { isDark } = useAppearance();
const currentYear = new Date().getFullYear();
</script>

<template>
    <footer id="contact" class="bg-muted px-4 py-16 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-8 md:grid-cols-4">
                <div class="md:col-span-2">
                    <div class="mb-4 flex items-center space-x-3">
                        <ReMediLogoIcon :isDark="isDark" :size="32" />
                        <span class="text-xl font-bold">ReMedi</span>
                    </div>
                    <p class="text-muted-foreground mb-6 max-w-md">
                        {{ t('Your ultimate personal medication assistant. Manage your treatment with confidence and ease.') }}
                    </p>
                </div>

                <div>
                    <h3 class="mb-4 font-semibold">{{ t('Legal') }}</h3>
                    <ul class="text-muted-foreground space-y-2">
                        <li>
                            <a href="#" class="hover:text-foreground transition-colors">{{ t('Privacy Policy') }}</a>
                        </li>
                        <li>
                            <a href="#" class="hover:text-foreground transition-colors">{{ t('Terms of Service') }}</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="mb-4 font-semibold">{{ t('Contact') }}</h3>
                    <ul class="text-muted-foreground space-y-2">
                        <li>
                            <a href="#" class="hover:text-foreground transition-colors">{{ t('Support') }}</a>
                        </li>
                        <li>
                            <a href="#" class="hover:text-foreground transition-colors">{{ t('Contact Us') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-border mt-12 border-t pt-8 text-center">
                <p class="text-muted-foreground text-sm">© {{ currentYear }} ReMedi. {{ t('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>
</template>
