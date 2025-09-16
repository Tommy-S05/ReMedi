<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useTranslations } from '@/composables/useTranslations';
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { AlertCircle, Home } from 'lucide-vue-next';

defineProps<{
    message: string;
}>();

const { t } = useTranslations();
</script>

<template>
    <Head :title="t('Invalid Invitation')" />
    <AuthCardLayout>
        <div class="flex flex-col items-center p-4 text-center">
            <AlertCircle class="text-destructive mb-4 h-16 w-16" />
            <h1 class="mb-2 text-2xl font-semibold">
                {{ t('Invalid Invitation Link') }}
            </h1>
            <p class="text-muted-foreground mb-6">
                {{ t(message) }}
            </p>
            <Link :href="route('dashboard')">
                <Button>
                    <Home class="mr-2 h-4 w-4" />
                    {{ t('Go to Dashboard') }}
                </Button>
            </Link>
        </div>
    </AuthCardLayout>
</template>
