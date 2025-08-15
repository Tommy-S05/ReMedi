<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import { Motion } from 'motion-v';

const { t } = useTranslations();
</script>

<template>
    <section class="bg-primary px-4 py-20 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-4xl text-center">
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6 }"
                :viewport="{ once: true }"
                class="space-y-8"
            >
                <h2 class="text-primary-foreground mb-6 text-3xl font-bold lg:text-4xl">{{ t('Ready to simplify your health routine?') }}</h2>
                <p class="text-primary-foreground/90 mx-auto mb-8 max-w-2xl text-xl">
                    {{ t('Create your free account in seconds and enjoy the peace of mind of having everything under control.') }}
                </p>
                <Button
                    size="lg"
                    variant="secondary"
                    asChild
                    class="bg-background text-foreground hover:bg-background/90 px-8 py-4 text-lg font-semibold"
                >
                    <Link :href="route('register')">
                        {{ t('Start Free Now') }}
                        <ArrowRight class="ml-2 h-5 w-5" />
                    </Link>
                </Button>
            </Motion>
        </div>
    </section>
</template>
