<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { useTranslations } from '@/composables/useTranslations';
import { Motion } from 'motion-v';
import { defineAsyncComponent } from 'vue';

const { t } = useTranslations();
const { isDark } = useAppearance();

interface Benefit {
    key: string;
    illustration: string;
    title: string;
    description: string;
}

const props = defineProps<{
    benefits: Benefit[];
}>();

const getIllustrationComponent = (name: string) => {
    return defineAsyncComponent(() => import(`@/components/illustrations/${name}.vue`));
};
</script>

<template>
    <section id="benefits" class="px-4 py-20 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl">
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6 }"
                :viewport="{ once: true }"
                class="mb-16 text-center"
            >
                <h2 class="mb-4 text-3xl font-bold lg:text-4xl">{{ t('An interface designed for your peace of mind.') }}</h2>
            </Motion>

            <div class="space-y-24">
                <div
                    v-for="(benefit, index) in props.benefits"
                    :key="benefit.key"
                    class="grid items-center gap-12 lg:grid-cols-2"
                    :class="{ 'lg:flex-row-reverse': index % 2 !== 0 }"
                >
                    <Motion
                        :initial="{ opacity: 0, x: index % 2 === 0 ? -50 : 50 }"
                        :whileInView="{ opacity: 1, x: 0 }"
                        :transition="{ duration: 0.8 }"
                        :viewport="{ once: true }"
                        :class="[index % 2 === 0 ? 'order-2 lg:order-1' : 'order-2']"
                    >
                        <component :is="getIllustrationComponent(benefit.illustration)" responsive :isDark="isDark" />
                    </Motion>
                    <Motion
                        :initial="{ opacity: 0, x: index % 2 === 0 ? 50 : -50 }"
                        :whileInView="{ opacity: 1, x: 0 }"
                        :transition="{ duration: 0.8, delay: 0.2 }"
                        :viewport="{ once: true }"
                        :class="[index % 2 === 0 ? 'order-1 lg:order-2' : 'order-1']"
                    >
                        <h3 class="text-2xl font-bold">{{ t(benefit.title) }}</h3>
                        <p class="text-muted-foreground text-lg leading-relaxed">
                            {{ t(benefit.description) }}
                        </p>
                    </Motion>
                </div>
            </div>
        </div>
    </section>
</template>
