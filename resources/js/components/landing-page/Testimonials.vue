<script setup lang="ts">
import Avatar from '@/components/ui/Avatar.vue';
import { Card } from '@/components/ui/card';
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import { useTranslations } from '@/composables/useTranslations';
import { expandedTestimonials } from '@/utils/welcome-data';
import Autoplay from 'embla-carousel-autoplay';
import { Star } from 'lucide-vue-next';
import { Motion } from 'motion-v';

const { t } = useTranslations();

const pluginCarousel = Autoplay({
    delay: 2000,
    stopOnMouseEnter: true,
    stopOnInteraction: false,
});
</script>

<template>
    <section id="testimonials" class="overflow-x-hidden px-4 py-20 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl space-y-12">
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6 }"
                :viewport="{ once: true }"
                class="text-center"
            >
                <h2 class="mb-4 text-3xl font-bold lg:text-4xl">{{ t('Trusted by thousands of users.') }}</h2>
                <p class="text-muted-foreground mx-auto max-w-3xl text-xl">
                    {{ t('Discover how ReMedi is transforming the lives of people like you around the world.') }}
                </p>
            </Motion>

            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6, delay: 0.2 }"
                :viewport="{ once: true }"
            >
                <div class="px-14">
                    <Carousel
                        class="relative mx-auto w-full"
                        :plugins="[pluginCarousel]"
                        :opts="{
                            align: 'start',
                            loop: true,
                        }"
                    >
                        <CarouselContent class="-ml-2 md:-ml-4">
                            <CarouselItem
                                v-for="testimonial in expandedTestimonials"
                                :key="testimonial.id"
                                class="pt-4 pl-2 md:basis-1/2 md:pl-4 lg:basis-1/3"
                            >
                                <Card
                                    class="group hover:shadow-primary/20 flex h-full flex-col p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                                >
                                    <div class="flex-grow space-y-4">
                                        <div class="flex items-center">
                                            <Star v-for="i in testimonial.rating" :key="i" class="h-5 w-5 fill-current text-yellow-400" />
                                        </div>
                                        <p class="text-muted-foreground mb-6 leading-relaxed italic">"{{ t(testimonial.content) }}"</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <Avatar :name="testimonial.name" size="md" />
                                        <div>
                                            <p class="font-semibold">{{ testimonial.name }}</p>
                                            <p class="text-muted-foreground text-sm">{{ t(testimonial.role) }}</p>
                                            <p class="text-primary text-xs">{{ t(testimonial.location) }}</p>
                                        </div>
                                    </div>
                                </Card>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious class="hidden md:flex" />
                        <CarouselNext class="hidden md:flex" />
                    </Carousel>
                </div>
            </Motion>
        </div>
    </section>
</template>
