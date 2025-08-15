<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { useTranslations } from '@/composables/useTranslations';
import { ArrowRight } from 'lucide-vue-next';
import { Motion } from 'motion-v';

const { t } = useTranslations();
</script>

<template>
    <section id="how-to-start" class="bg-muted/50 px-4 py-20 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl">
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6 }"
                :viewport="{ once: true }"
                class="mb-16 text-center"
            >
                <h2 class="mb-4 text-3xl font-bold lg:text-4xl">{{ t('Getting started is very easy') }}</h2>
                <p class="text-muted-foreground mx-auto max-w-3xl text-xl">
                    {{ t('Follow these three simple steps to take control of your treatment.') }}
                </p>
            </Motion>

            <div class="mx-auto grid max-w-6xl gap-8 md:grid-cols-3">
                <!-- Step 1 -->
                <Motion
                    :initial="{ opacity: 0, y: 30 }"
                    :whileInView="{ opacity: 1, y: 0 }"
                    :transition="{ duration: 0.6, delay: 0.1 }"
                    :viewport="{ once: true }"
                >
                    <Card class="group h-full p-8 text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <div class="mb-6">
                            <div
                                class="bg-primary mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full transition-transform duration-300 group-hover:scale-110"
                            >
                                <span class="text-primary-foreground text-2xl font-bold">1</span>
                            </div>
                            <h3 class="mb-4 text-xl font-bold">{{ t('Register your medications') }}</h3>
                            <p class="text-muted-foreground leading-relaxed">
                                {{ t('Add all your medications, vitamins, and supplements to the app quickly and easily.') }}
                            </p>
                        </div>
                    </Card>
                </Motion>

                <!-- Step 2 -->
                <Motion
                    :initial="{ opacity: 0, y: 30 }"
                    :whileInView="{ opacity: 1, y: 0 }"
                    :transition="{ duration: 0.6, delay: 0.2 }"
                    :viewport="{ once: true }"
                >
                    <Card class="group h-full p-8 text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <div class="mb-6">
                            <div
                                class="bg-primary mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full transition-transform duration-300 group-hover:scale-110"
                            >
                                <span class="text-primary-foreground text-2xl font-bold">2</span>
                            </div>
                            <h3 class="mb-4 text-xl font-bold">{{ t('Set your schedules') }}</h3>
                            <p class="text-muted-foreground leading-relaxed">
                                {{ t('Set doses, frequencies, and times for each medication according to your prescription.') }}
                            </p>
                        </div>
                    </Card>
                </Motion>

                <!-- Step 3 -->
                <Motion
                    :initial="{ opacity: 0, y: 30 }"
                    :whileInView="{ opacity: 1, y: 0 }"
                    :transition="{ duration: 0.6, delay: 0.3 }"
                    :viewport="{ once: true }"
                >
                    <Card class="group h-full p-8 text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <div class="mb-6">
                            <div
                                class="bg-primary mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full transition-transform duration-300 group-hover:scale-110"
                            >
                                <span class="text-primary-foreground text-2xl font-bold">3</span>
                            </div>
                            <h3 class="mb-4 text-xl font-bold">{{ t('Receive reminders') }}</h3>
                            <p class="text-muted-foreground leading-relaxed">
                                {{ t("You'll receive smart notifications on your phone so you never miss a dose again.") }}
                            </p>
                        </div>
                    </Card>
                </Motion>
            </div>

            <!-- CTA -->
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6, delay: 0.4 }"
                :viewport="{ once: true }"
                class="mt-12 text-center"
            >
                <Button size="lg" class="bg-primary hover:bg-primary/90 text-primary-foreground px-8 py-4 text-lg">
                    {{ t('Start Free Now') }}
                    <ArrowRight class="ml-2 h-5 w-5" />
                </Button>
            </Motion>
        </div>
    </section>
</template>
