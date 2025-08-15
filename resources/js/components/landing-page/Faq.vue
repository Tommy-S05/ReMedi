<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { useTranslations } from '@/composables/useTranslations';
import { faqs } from '@/utils/welcome-data';
import { Motion } from 'motion-v';

const { t } = useTranslations();
</script>

<template>
    <section id="faq" class="bg-muted/30 px-4 py-20 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-4xl">
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6 }"
                :viewport="{ once: true }"
                class="mb-16 text-center"
            >
                <h2 class="mb-4 text-3xl font-bold lg:text-4xl">{{ t('Have questions? We have answers.') }}</h2>
            </Motion>

            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6, delay: 0.2 }"
                :viewport="{ once: true }"
            >
                <Accordion type="single" collapsible class="space-y-4">
                    <AccordionItem
                        v-for="(faq, index) in faqs"
                        :key="index"
                        :value="`item-${index}`"
                        class="border-border rounded-lg border px-6 last:border-b"
                    >
                        <AccordionTrigger class="cursor-pointer text-left font-semibold">
                            {{ t(faq.question) }}
                        </AccordionTrigger>
                        <AccordionContent class="text-muted-foreground px-3 leading-relaxed">
                            {{ t(faq.answer) }}
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </Motion>
        </div>
    </section>
</template>
