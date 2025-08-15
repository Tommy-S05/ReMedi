<script setup lang="ts">
import FeaturesIllustration from '@/components/illustrations/FeaturesIllustration.vue';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/composables/useAppearance';
import { useTranslations } from '@/composables/useTranslations';
import { AlertTriangle, ArrowRight, BarChart3, Calculator, FileText, Link as LinkIcon, MousePointer, Settings, Target, UserX } from 'lucide-vue-next';
import { Motion } from 'motion-v';

const { t } = useTranslations();
const { isDark } = useAppearance();
</script>

<template>
    <section id="advanced-features" class="bg-muted/30 px-4 py-20 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl space-y-16">
            <Motion
                :initial="{ opacity: 0, y: 30 }"
                :whileInView="{ opacity: 1, y: 0 }"
                :transition="{ duration: 0.6 }"
                :viewport="{ once: true }"
                class="text-center"
            >
                <div class="mb-4 inline-block">
                    <span class="text-primary text-sm font-semibold tracking-wide uppercase">
                        {{ t('Advanced Features') }}
                    </span>
                </div>
                <h2 class="mb-4 text-3xl font-bold lg:text-4xl">{{ t('Professional tools for complete care') }}.</h2>
                <p class="text-muted-foreground mx-auto max-w-3xl text-xl">
                    {{
                        t(
                            'ReMedi goes beyond basic reminders. Discover advanced features designed for healthcare professionals, caregivers, and users seeking maximum control.',
                        )
                    }}
                </p>
            </Motion>

            <div class="space-y-24">
                <!-- Detailed Adherence Reports -->
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <Motion
                        :initial="{ opacity: 0, x: -50 }"
                        :whileInView="{ opacity: 1, x: 0 }"
                        :transition="{ duration: 0.8 }"
                        :viewport="{ once: true }"
                        class="space-y-6 lg:order-1"
                    >
                        <div class="inline-block">
                            <span class="text-primary text-sm font-semibold tracking-wide uppercase">
                                {{ t('Professional Analysis') }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold">
                            {{ t('Detailed Adherence Reports') }}
                        </h3>
                        <p class="text-muted-foreground text-lg leading-relaxed">
                            {{
                                t(
                                    'Gain powerful insights into your treatment progress. ReMedi generates easy-to-understand reports that track your adherence over time, identify patterns, and help you optimize your health.',
                                )
                            }}
                        </p>

                        <!-- Enhanced features with specific icons -->
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <BarChart3 class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('Daily, weekly, and monthly adherence percentages') }}</h4>
                                    <p class="text-muted-foreground text-sm">
                                        {{ t('Visualize your progress with detailed charts and precise metrics') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <Target class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('Dose-level tracking for each medication') }}</h4>
                                    <p class="text-muted-foreground text-sm">
                                        {{ t('Granular analysis that identifies patterns for each individual medication') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <FileText class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('Printable, shareable PDF reports') }}</h4>
                                    <p class="text-muted-foreground text-sm">
                                        {{ t('Professional documents ready to share with your medical team') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <Motion
                            :initial="{ opacity: 0, y: 20 }"
                            :whileInView="{ opacity: 1, y: 0 }"
                            :transition="{ duration: 0.6, delay: 0.3 }"
                            :viewport="{ once: true }"
                        >
                            <Button class="bg-primary hover:bg-primary/90 text-primary-foreground">
                                {{ t('View Sample Reports') }}
                                <ArrowRight class="ml-2 h-4 w-4" />
                            </Button>
                        </Motion>
                    </Motion>

                    <Motion
                        :initial="{ opacity: 0, x: 50 }"
                        :whileInView="{ opacity: 1, x: 0 }"
                        :transition="{ duration: 0.8, delay: 0.2 }"
                        :viewport="{ once: true }"
                        class="lg:order-2"
                    >
                        <div class="h-auto w-full overflow-hidden rounded-lg md:h-[30rem] lg:h-auto">
                            <FeaturesIllustration responsive :isDark="isDark" feature="reports" />
                        </div>
                    </Motion>
                </div>

                <!-- Secure Sharing with Family and Caregivers -->
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <Motion
                        :initial="{ opacity: 0, x: 50 }"
                        :whileInView="{ opacity: 1, x: 0 }"
                        :transition="{ duration: 0.8, delay: 0.2 }"
                        :viewport="{ once: true }"
                        class="space-y-6 lg:order-2"
                    >
                        <div class="inline-block">
                            <span class="text-primary text-sm font-semibold tracking-wide uppercase"> {{ t('Collaborative Care') }} </span>
                        </div>
                        <h3 class="text-2xl font-bold">{{ t('Secure Sharing with Family and Caregivers') }}</h3>
                        <p class="text-muted-foreground text-lg leading-relaxed">
                            {{
                                t(
                                    'Give your loved ones peace of mind. ReMedi’s secure sharing lets you grant access to your schedule and adherence reports. You control who sees your information and what they can view, ensuring privacy and collaborative care.',
                                )
                            }}
                        </p>

                        <!-- Enhanced sharing features -->
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <LinkIcon class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('Invite caregivers via secure link') }}</h4>
                                    <p class="text-muted-foreground text-sm">{{ t('Share instant access with a unique encrypted link') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <Settings class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('Set granular permissions for each person') }}</h4>
                                    <p class="text-muted-foreground text-sm">
                                        {{ t('Fine-grained control over what information each caregiver can view') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <UserX class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('Revoke access at any time') }}</h4>
                                    <p class="text-muted-foreground text-sm">
                                        {{ t('Maintain full control with the ability to revoke access instantly') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <Motion
                            :initial="{ opacity: 0, y: 20 }"
                            :whileInView="{ opacity: 1, y: 0 }"
                            :transition="{ duration: 0.6, delay: 0.3 }"
                            :viewport="{ once: true }"
                        >
                            <Button class="bg-primary hover:bg-primary/90 text-primary-foreground">
                                {{ t('Explore Sharing Features') }}
                                <ArrowRight class="ml-2 h-4 w-4" />
                            </Button>
                        </Motion>
                    </Motion>

                    <Motion
                        :initial="{ opacity: 0, x: -50 }"
                        :whileInView="{ opacity: 1, x: 0 }"
                        :transition="{ duration: 0.8 }"
                        :viewport="{ once: true }"
                        class="lg:order-1"
                    >
                        <div class="h-auto w-full overflow-hidden rounded-lg md:h-[30rem] lg:h-auto">
                            <FeaturesIllustration responsive :isDark="isDark" feature="sharing" />
                        </div>
                    </Motion>
                </div>

                <!-- Effortless Inventory Management -->
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <Motion
                        :initial="{ opacity: 0, x: -50 }"
                        :whileInView="{ opacity: 1, x: 0 }"
                        :transition="{ duration: 0.8 }"
                        :viewport="{ once: true }"
                        class="space-y-6 lg:order-1"
                    >
                        <div class="inline-block">
                            <span class="text-primary text-sm font-semibold tracking-wide uppercase"> {{ t('Smart Management') }} </span>
                        </div>
                        <h3 class="text-2xl font-bold">{{ t('Effortless Inventory Management') }}</h3>
                        <p class="text-muted-foreground text-lg leading-relaxed">
                            {{
                                t(
                                    'Stay on top of your medication supply. ReMedi’s inventory management tracks your remaining doses and sends timely reminders to restock your prescriptions, so you never run out unexpectedly.',
                                )
                            }}
                        </p>

                        <!-- Enhanced inventory features -->
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <Calculator class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('Automatic pill or dose counting') }}</h4>
                                    <p class="text-muted-foreground text-sm">{{ t('Smart tracking that updates with every dose taken') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <AlertTriangle class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('Customizable low inventory alerts') }}</h4>
                                    <p class="text-muted-foreground text-sm">
                                        {{ t('Receive notifications when you need to restock, configurable to your needs') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="bg-primary/10 rounded-full p-2">
                                    <MousePointer class="text-primary h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-foreground font-semibold">{{ t('One-click restock logging') }}</h4>
                                    <p class="text-muted-foreground text-sm">
                                        {{ t('Easily update your inventory when you get new prescriptions') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <Motion
                            :initial="{ opacity: 0, y: 20 }"
                            :whileInView="{ opacity: 1, y: 0 }"
                            :transition="{ duration: 0.6, delay: 0.3 }"
                            :viewport="{ once: true }"
                        >
                            <Button class="bg-primary hover:bg-primary/90 text-primary-foreground">
                                {{ t('Discover Inventory Management') }}
                                <ArrowRight class="ml-2 h-4 w-4" />
                            </Button>
                        </Motion>
                    </Motion>

                    <Motion
                        :initial="{ opacity: 0, x: 50 }"
                        :whileInView="{ opacity: 1, x: 0 }"
                        :transition="{ duration: 0.8, delay: 0.2 }"
                        :viewport="{ once: true }"
                        class="lg:order-2"
                    >
                        <div class="h-auto w-full overflow-hidden rounded-lg md:h-[30rem] lg:h-auto">
                            <FeaturesIllustration responsive :isDark="isDark" feature="inventory-management" />
                        </div>
                    </Motion>
                </div>
            </div>
        </div>
    </section>
</template>
