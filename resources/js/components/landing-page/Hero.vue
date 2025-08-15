<script setup lang="ts">
import HeroIllustration from '@/components/illustrations/HeroIllustration.vue';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/composables/useAppearance';
import { useTranslations } from '@/composables/useTranslations';
import { ArrowRight } from 'lucide-vue-next';
import { Motion } from 'motion-v';

const { t } = useTranslations();
const { isDark } = useAppearance();
</script>

<template>
    <section id="home" class="px-4 pt-24 pb-16 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <Motion :initial="{ opacity: 0, x: -50 }" :animate="{ opacity: 1, x: 0 }" :transition="{ duration: 0.8 }" class="space-y-8">
                    <div class="space-y-6">
                        <h1 class="text-4xl leading-tight font-bold sm:text-5xl lg:text-6xl">
                            {{ t('Never miss a dose.') }} <span class="text-primary">{{ t('Take control') }}</span> {{ t('of your health.') }}
                        </h1>
                        <p class="text-muted-foreground text-xl leading-relaxed">
                            {{
                                t(
                                    'ReMedi is your personal medication assistant. We help you follow your treatment, monitor your progress, and care for your loved ones — all in one place.',
                                )
                            }}
                        </p>
                    </div>

                    <Motion :initial="{ opacity: 0, y: 20 }" :animate="{ opacity: 1, y: 0 }" :transition="{ duration: 0.6, delay: 0.3 }">
                        <Button size="lg" class="bg-primary hover:bg-primary/90 text-primary-foreground px-8 py-4 text-lg">
                            {{ t('Start Free Now') }}
                            <ArrowRight class="ml-2 h-5 w-5" />
                        </Button>
                    </Motion>
                </Motion>

                <Motion :initial="{ opacity: 0, x: 50 }" :animate="{ opacity: 1, x: 0 }" :transition="{ duration: 0.8, delay: 0.2 }" class="relative">
                    <div
                        class="from-primary/10 to-primary/5 relative mx-auto aspect-[4/3] w-[min(92vw,520px)] overflow-hidden rounded-3xl bg-gradient-to-br sm:w-[min(88vw,600px)] md:w-[min(84vw,680px)] lg:aspect-auto lg:w-auto lg:p-8"
                    >
                        <HeroIllustration responsive wrapperFit="contain" :isDark="isDark" class="h-full w-full" />
                    </div>
                </Motion>
            </div>
        </div>
    </section>
</template>
