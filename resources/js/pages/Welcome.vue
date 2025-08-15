<script setup lang="ts">
import AdvancedFeatures from '@/components/landing-page/AdvancedFeatures.vue';
import Benefits from '@/components/landing-page/Benefits.vue';
import Faq from '@/components/landing-page/Faq.vue';
import Features from '@/components/landing-page/Features.vue';
import FinalCTA from '@/components/landing-page/FinalCTA.vue';
import Footer from '@/components/landing-page/Footer.vue';
import Hero from '@/components/landing-page/Hero.vue';
import HowToStart from '@/components/landing-page/HowToStart.vue';
import Navbar from '@/components/landing-page/Navbar.vue';
import Pricing from '@/components/landing-page/Pricing.vue';
import Testimonials from '@/components/landing-page/Testimonials.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head } from '@inertiajs/vue3';
import { benefits } from '@/utils/welcome-data';
import { onMounted, onUnmounted, ref } from 'vue';

const { t } = useTranslations();
const isScrolled = ref(false);
const activeSection = ref('inicio');

// Scroll spy functionality
const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;

    // Simple scroll spy
    const sections = ['home', 'how-to-start', 'features', 'benefits', 'advanced-features', 'pricing', 'faq', 'testimonials', 'contact'];
    const scrollPosition = window.scrollY + 100;

    for (const section of sections) {
        const element = document.getElementById(section);
        if (element) {
            const offsetTop = element.offsetTop;
            const offsetBottom = offsetTop + element.offsetHeight;

            if (scrollPosition >= offsetTop && scrollPosition < offsetBottom) {
                activeSection.value = section;
                break;
            }
        }
    }
};

// Initialize scroll listener
onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Initial call
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <Head :title="t('Welcome')" />

    <div class="bg-background text-foreground min-h-screen overflow-x-hidden transition-colors duration-300">
        <!-- Navbar -->
        <Navbar />

        <!-- Hero Section -->
        <Hero />

        <!-- How to Start Section -->
        <HowToStart />

        <!-- Features Section -->
        <Features />

        <!-- Benefits Showcase -->
        <Benefits :benefits="benefits" />

        <!-- Advanced Features Section -->
        <AdvancedFeatures />

        <!-- Pricing Section -->
        <Pricing />

        <!-- FAQ Section -->
        <Faq />

        <!-- Testimonials Carousel Section -->
        <Testimonials />

        <!-- Final CTA -->
        <FinalCTA />

        <!-- Footer -->
        <Footer />
    </div>
</template>
