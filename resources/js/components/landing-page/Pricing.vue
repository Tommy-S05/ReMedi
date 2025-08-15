<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Check, HelpCircle, Mail } from 'lucide-vue-next';
import { Motion } from 'motion-v';

const { t } = useTranslations();
</script>

<template>
    <section id="pricing" class="px-4 py-20 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-xl lg:max-w-7xl">
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6 }"
                :viewport="{ once: true }"
                class="mb-16 text-center"
            >
                <div class="mb-4 inline-block">
                    <span class="text-primary text-sm font-semibold tracking-wide uppercase">
                        {{ t('Pricing') }}
                    </span>
                </div>
                <h2 class="mb-4 text-3xl font-bold lg:text-4xl">{{ t('Choose the perfect plan for you') }}</h2>
                <p class="text-muted-foreground mx-auto max-w-3xl text-xl">
                    {{ t('From individual users to full medical teams. ReMedi adapts to your needs.') }}
                </p>
            </Motion>

            <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-3">
                <!-- Basic Plan -->
                <Motion
                    :initial="{ opacity: 0, y: 30 }"
                    :whileInView="{ opacity: 1, y: 0 }"
                    :transition="{ duration: 0.6, delay: 0.1 }"
                    :viewport="{ once: true }"
                >
                    <Card class="relative h-full p-8 transition-all duration-300 hover:shadow-xl">
                        <div class="mb-6 space-y-2 text-center">
                            <h3 class="text-2xl font-bold">{{ t('Basic') }}</h3>
                            <div class="text-primary text-4xl font-bold">{{ t('Free') }}</div>
                            <p class="text-muted-foreground">{{ t('For Lifetime') }}</p>
                        </div>

                        <ul class="mb-8 space-y-3">
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Up to 5 medications') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Basic reminders') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Simple adherence tracking') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Basic dashboard') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Email support') }}</span>
                            </li>
                        </ul>

                        <Link :href="route('register')">
                            <Button class="bg-primary hover:bg-primary/90 text-primary-foreground w-full">
                                {{ t('Start Free Now') }}
                                <ArrowRight class="ml-2 h-4 w-4" />
                            </Button>
                        </Link>
                    </Card>
                </Motion>

                <!-- Pro Plan -->
                <Motion
                    :initial="{ opacity: 0, y: 30 }"
                    :whileInView="{ opacity: 1, y: 0 }"
                    :transition="{ duration: 0.6, delay: 0.2 }"
                    :viewport="{ once: true }"
                >
                    <Card class="border-primary/50 relative h-full p-8 transition-all duration-300 hover:shadow-xl">
                        <!-- Popular badge -->
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span class="bg-primary text-primary-foreground rounded-full px-4 py-1 text-sm font-semibold">
                                {{ t('Most Popular') }}
                            </span>
                        </div>

                        <div class="mb-6 space-y-2 text-center">
                            <h3 class="text-2xl font-bold">{{ t('Pro') }}</h3>
                            <div class="text-primary text-4xl font-bold">$9.99</div>
                            <p class="text-muted-foreground">{{ t('per month') }}</p>
                        </div>

                        <ul class="mb-8 space-y-3">
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Unlimited medications') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Advanced smart reminders') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Detailed PDF reports') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Share with up to 5 caregivers') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Smart inventory management') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Advanced dashboard with widgets') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Priority support') }}</span>
                            </li>
                        </ul>

                        <Button class="bg-primary hover:bg-primary/90 text-primary-foreground w-full">
                            {{ t('Start Free Trial') }}
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Button>

                        <p class="text-muted-foreground mt-3 text-center text-xs">{{ t('14-day free trial, then $9.99/month') }}</p>
                    </Card>
                </Motion>

                <!-- Profesional Plan -->
                <Motion
                    :initial="{ opacity: 0, y: 30 }"
                    :whileInView="{ opacity: 1, y: 0 }"
                    :transition="{ duration: 0.6, delay: 0.3 }"
                    :viewport="{ once: true }"
                >
                    <Card class="relative h-full p-8 transition-all duration-300 hover:shadow-xl">
                        <div class="mb-6 space-y-2 text-center">
                            <h3 class="text-2xl font-bold">{{ t('Professional') }}</h3>
                            <div class="text-primary text-4xl font-bold">$29.99</div>
                            <p class="text-muted-foreground">{{ t('per month') }}</p>
                        </div>

                        <ul class="mb-8 space-y-3">
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Everything in Pro plan') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Multi-patient management') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('API for integration with medical systems') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Advanced custom reports') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('HIPAA compliance') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Dedicated phone support') }}</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <Check class="text-primary mt-0.5 h-5 w-5 flex-shrink-0" />
                                <span>{{ t('Personalized training') }}</span>
                            </li>
                        </ul>

                        <Button variant="outline" class="border-primary text-primary hover:bg-primary hover:text-primary-foreground w-full">
                            {{ t('Contact Sales') }}
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Button>
                    </Card>
                </Motion>
            </div>

            <!-- FAQ Pricing -->
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6, delay: 0.4 }"
                :viewport="{ once: true }"
                class="mt-16 text-center"
            >
                <div class="bg-muted/50 mx-auto max-w-4xl rounded-lg p-8">
                    <h3 class="mb-4 text-xl font-semibold">{{ t('Questions about pricing?') }}</h3>
                    <p class="text-muted-foreground mb-6">
                        {{ t('All plans include free updates and technical support. You can change or cancel your plan at any time.') }}
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <Button variant="outline">
                            <a href="#faq" class="flex items-center">
                                <HelpCircle class="mr-2 h-4 w-4" />
                                {{ t('View Pricing FAQ') }}
                            </a>
                        </Button>
                        <Button variant="outline">
                            <Mail class="mr-2 h-4 w-4" />
                            {{ t('Contact Support') }}
                        </Button>
                    </div>
                </div>
            </Motion>
        </div>
    </section>
</template>
