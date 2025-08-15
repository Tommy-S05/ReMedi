<script setup lang="ts">
import FeaturesIllustration from '@/components/illustrations/FeaturesIllustration.vue';
import { Card } from '@/components/ui/card';
import { useAppearance } from '@/composables/useAppearance';
import { useTranslations } from '@/composables/useTranslations';
import { enhancedFeatures } from '@/utils/welcome-data';
import { Check } from 'lucide-vue-next';
import { Motion } from 'motion-v';

const { t } = useTranslations();
const { isDark } = useAppearance();
</script>

<template>
    <section id="features" class="bg-muted/30 px-4 py-20 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl space-y-16">
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6 }"
                :viewport="{ once: true }"
                class="text-center"
            >
                <h2 class="mb-4 text-3xl font-bold lg:text-4xl">{{ t('A smarter way to manage your health') }}</h2>
                <p class="text-muted-foreground mx-auto max-w-3xl text-xl">
                    {{ t('ReMedi offers a complete set of tools to help you manage your health effectively and live a healthier life.') }}
                </p>
            </Motion>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <Motion
                    v-for="(feature, index) in enhancedFeatures"
                    :key="index"
                    :initial="{ opacity: 0, y: 30 }"
                    :whileInView="{ opacity: 1, y: 0 }"
                    :transition="{ duration: 0.6, delay: index * 0.1 }"
                    :viewport="{ once: true }"
                >
                    <Card
                        class="group hover:shadow-primary/20 h-full cursor-pointer p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg"
                    >
                        <div class="h-auto w-full rounded-lg">
                            <FeaturesIllustration responsive :isDark="isDark" :feature="feature.illustrationType" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold">{{ t(feature.title) }}</h3>
                        <p class="text-muted-foreground mb-4 leading-relaxed">{{ t(feature.description) }}</p>

                        <!-- Enhanced feature details -->
                        <ul v-if="feature.details" class="space-y-2">
                            <li v-for="detail in feature.details" :key="detail" class="text-muted-foreground flex items-start space-x-2 text-sm">
                                <Check class="text-primary mt-0.5 h-4 w-4 flex-shrink-0" />
                                <span>{{ t(detail) }}</span>
                            </li>
                        </ul>
                    </Card>
                </Motion>
            </div>
        </div>
    </section>
</template>
