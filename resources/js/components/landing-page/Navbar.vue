<script setup lang="ts">
import ReMediLogoIcon from '@/components/ReMediLogoIcon.vue';
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';
import { Button } from '@/components/ui/button';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
} from '@/components/ui/navigation-menu';
import { useAppearance } from '@/composables/useAppearance';
import { useTranslations } from '@/composables/useTranslations';
import { SharedData } from '@/types';
import { productLinks, productLinksMobile, resourceLinks, resourceLinksMobile } from '@/utils/welcome-data';
import { Link, usePage } from '@inertiajs/vue3';
import { BarChart3, ChevronDown, DollarSign, LogIn, Mail, Menu, UserPlus, X } from 'lucide-vue-next';
import { Motion } from 'motion-v';
import { onMounted, onUnmounted, ref } from 'vue';

const { t } = useTranslations();
const { isDark } = useAppearance();

const pageProps = usePage<SharedData>().props;

const isMobileMenuOpen = ref(false);
const isTabletMenuOpen = ref(false);
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

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const toggleTabletMenu = () => {
    isTabletMenuOpen.value = !isTabletMenuOpen.value;
};

// Initialize scroll listener
onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Initial call
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

const closeTabletMenu = () => {
    isTabletMenuOpen.value = false;
};
</script>

<template>
    <nav
        class="bg-background/80 border-border fixed top-0 z-50 w-full border-b backdrop-blur-md transition-all duration-300"
        :class="{ 'bg-background/95 shadow-lg': isScrolled }"
    >
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <a href="#home" class="group flex items-center space-x-3">
                        <div class="transition-transform duration-300 group-hover:scale-110">
                            <ReMediLogoIcon :isDark="isDark" :size="32" />
                        </div>
                        <span class="group-hover:text-primary text-xl font-bold transition-colors duration-300">ReMedi</span>
                    </a>
                </div>

                <!-- Desktop Navigation with Navigation Menu -->
                <div class="hidden items-center space-x-1 lg:flex">
                    <NavigationMenu>
                        <NavigationMenuList class="space-x-1">
                            <!-- Product Mega Menu -->
                            <NavigationMenuItem>
                                <NavigationMenuTrigger
                                    class="text-muted-foreground hover:text-foreground data-[state=open]:text-foreground cursor-pointer transition-colors duration-200"
                                >
                                    {{ t('Product') }}
                                </NavigationMenuTrigger>
                                <NavigationMenuContent>
                                    <div
                                        class="bg-popover/95 border-border grid w-[600px] gap-3 rounded-lg border p-6 shadow-lg backdrop-blur-md md:grid-cols-2"
                                    >
                                        <div v-for="section in productLinks" :key="section.key" class="space-y-3">
                                            <h4 class="text-foreground mb-3 text-sm font-semibold">
                                                {{ t(section.label) }}
                                            </h4>
                                            <NavigationMenuLink v-for="link in section.links" :key="link.href" asChild>
                                                <a
                                                    :href="link.href"
                                                    class="group hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground block space-y-1 rounded-md p-3 leading-none no-underline transition-colors outline-none select-none"
                                                    :class="{ 'text-primary bg-primary/10': activeSection === link.section }"
                                                >
                                                    <div class="flex items-center space-x-2">
                                                        <component :is="link.icon" class="text-primary h-4 w-4" />
                                                        <div class="text-sm leading-none font-medium">
                                                            {{ t(link.label) }}
                                                        </div>
                                                    </div>
                                                    <p v-if="link.description" class="text-muted-foreground line-clamp-2 text-sm leading-snug">
                                                        {{ t(link.description) }}
                                                    </p>
                                                </a>
                                            </NavigationMenuLink>
                                        </div>
                                    </div>
                                </NavigationMenuContent>
                            </NavigationMenuItem>

                            <!-- Resources Menu -->
                            <NavigationMenuItem>
                                <NavigationMenuTrigger
                                    class="text-muted-foreground hover:text-foreground data-[state=open]:text-foreground cursor-pointer transition-colors duration-200"
                                >
                                    {{ t('Resources') }}
                                </NavigationMenuTrigger>
                                <NavigationMenuContent>
                                    <div class="bg-popover/95 border-border grid w-[400px] gap-3 rounded-lg border p-6 shadow-lg backdrop-blur-md">
                                        <div class="space-y-3">
                                            <h4 class="text-foreground mb-3 text-sm font-semibold">{{ t('Learn More') }}</h4>
                                            <NavigationMenuLink v-for="link in resourceLinks" :key="link.href" asChild>
                                                <a
                                                    :href="link.href"
                                                    class="group hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground block space-y-1 rounded-md p-3 leading-none no-underline transition-colors outline-none select-none"
                                                    :class="{ 'text-primary bg-primary/10': activeSection === link.section }"
                                                >
                                                    <div class="flex items-center space-x-2">
                                                        <component :is="link.icon" class="text-primary h-4 w-4" />
                                                        <div class="text-sm leading-none font-medium">{{ t(link.label) }}</div>
                                                    </div>
                                                    <p v-if="link.description" class="text-muted-foreground line-clamp-2 text-sm leading-snug">
                                                        {{ t(link.description) }}
                                                    </p>
                                                </a>
                                            </NavigationMenuLink>
                                        </div>
                                    </div>
                                </NavigationMenuContent>
                            </NavigationMenuItem>

                            <!-- Pricing - Direct Link -->
                            <NavigationMenuItem>
                                <NavigationMenuLink asChild>
                                    <a
                                        href="#pricing"
                                        class="group bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground data-[active]:bg-accent/50 data-[state=open]:bg-accent/50 inline-flex h-10 w-max items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors focus:outline-none disabled:pointer-events-none disabled:opacity-50"
                                        :class="{ 'text-primary bg-primary/10': activeSection === 'pricing' }"
                                    >
                                        {{ t('Pricing') }}
                                    </a>
                                </NavigationMenuLink>
                            </NavigationMenuItem>

                            <!-- Contact - Direct Link -->
                            <NavigationMenuItem>
                                <NavigationMenuLink asChild>
                                    <a
                                        href="#contact"
                                        class="group bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground data-[active]:bg-accent/50 data-[state=open]:bg-accent/50 inline-flex h-10 w-max items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors focus:outline-none disabled:pointer-events-none disabled:opacity-50"
                                        :class="{ 'text-primary bg-primary/10': activeSection === 'contact' }"
                                    >
                                        {{ t('Contact') }}
                                    </a>
                                </NavigationMenuLink>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>

                    <!-- CTAs -->
                    <div class="border-border ml-6 flex items-center space-x-3 border-l pl-6">
                        <template v-if="pageProps.auth.user">
                            <Link :href="route('dashboard')">
                                <Button variant="ghost" class="text-muted-foreground hover:text-foreground" aria-label="Ir al Dashboard">
                                    <BarChart3 class="mr-2 h-4 w-4" />
                                    {{ t('Dashboard') }}
                                </Button>
                            </Link>
                        </template>
                        <template v-else>
                            <Button variant="ghost" class="text-muted-foreground hover:text-foreground" asChild aria-label="Iniciar sesión en ReMedi">
                                <Link :href="route('login')">
                                    <LogIn class="mr-2 h-4 w-4" />
                                    {{ t('Login') }}
                                </Link>
                            </Button>
                            <Button
                                class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg transition-all duration-300 hover:shadow-xl"
                                asChild
                                aria-label="Registrarse gratis en ReMedi"
                            >
                                <Link :href="route('register')">
                                    <UserPlus class="mr-2 h-4 w-4" />
                                    {{ t('Register') }}
                                </Link>
                            </Button>
                        </template>
                        <!-- <Button
                                variant="outline"
                                size="sm"
                                class="text-muted-foreground hover:text-foreground border-border hover:border-primary/50"
                                aria-label="Ver demostración de ReMedi"
                            >
                                <Play class="mr-2 h-4 w-4" />
                                {{ t('Watch Demo') }}
                            </Button> -->
                        <ThemeSwitcher />
                    </div>
                </div>

                <!-- Tablet Navigation -->
                <div class="hidden items-center space-x-4">
                    <NavigationMenu>
                        <NavigationMenuList>
                            <NavigationMenuItem>
                                <NavigationMenuTrigger
                                    class="text-muted-foreground hover:text-foreground data-[state=open]:text-foreground transition-colors duration-200"
                                >
                                    {{ t('Menu') }}
                                </NavigationMenuTrigger>
                                <NavigationMenuContent>
                                    <ul
                                        class="bg-popover/95 border-border grid w-[350px] grid-cols-1 gap-3 rounded-lg border p-6 shadow-lg backdrop-blur-md"
                                    >
                                        <!-- Product Links -->
                                        <div v-for="section in productLinks" :key="section.key" class="col-span-2 grid grid-cols-2 space-y-3">
                                            <h4 class="text-foreground col-span-2 mb-2 text-sm font-semibold">
                                                {{ t(section.label) }}
                                            </h4>
                                            <li v-for="link in section.links" :key="link.href" class="col-span-1">
                                                <NavigationMenuLink asChild>
                                                    <a
                                                        :href="link.href"
                                                        class="group hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground flex items-center space-x-3 rounded-md p-2 no-underline transition-colors outline-none select-none"
                                                        :class="{ 'text-primary bg-primary/10': activeSection === link.section }"
                                                    >
                                                        <component :is="link.icon" class="text-primary h-4 w-4" />
                                                        <span class="text-sm font-medium">{{ t(link.label) }}</span>
                                                    </a>
                                                </NavigationMenuLink>
                                            </li>
                                        </div>

                                        <!-- Separator -->
                                        <div class="border-border col-span-2 my-1 border-t"></div>

                                        <!-- Resource Links -->
                                        <div class="col-span-2 grid grid-cols-2 space-y-3">
                                            <h4 class="text-foreground col-span-2 mb-2 text-sm font-semibold">
                                                {{ t('Learn More') }}
                                            </h4>
                                            <li v-for="link in resourceLinks" :key="link.href" class="col-span-1">
                                                <NavigationMenuLink asChild>
                                                    <a
                                                        :href="link.href"
                                                        class="group hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground flex items-center space-x-3 rounded-md p-2 no-underline transition-colors outline-none select-none"
                                                        :class="{ 'text-primary bg-primary/10': activeSection === link.section }"
                                                    >
                                                        <component :is="link.icon" class="text-primary h-4 w-4" />
                                                        <span class="text-sm font-medium">{{ t(link.label) }}</span>
                                                    </a>
                                                </NavigationMenuLink>
                                            </li>
                                        </div>

                                        <!-- Separator -->
                                        <div class="border-border col-span-2 my-1 border-t"></div>

                                        <!-- Other Links -->
                                        <div class="col-span-2 grid grid-cols-2 space-y-3">
                                            <h4 class="text-foreground col-span-2 mb-2 text-sm font-semibold">
                                                {{ t('Other') }}
                                            </h4>
                                            <li class="col-span-1">
                                                <NavigationMenuLink asChild>
                                                    <a
                                                        href="#pricing"
                                                        class="group hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground flex items-center justify-center space-x-3 rounded-md p-2 no-underline transition-colors outline-none select-none"
                                                        :class="{ 'text-primary bg-primary/10': activeSection === 'pricing' }"
                                                    >
                                                        <DollarSign class="text-primary h-4 w-4" />
                                                        <span class="text-sm font-medium">{{ t('Pricing') }}</span>
                                                    </a>
                                                </NavigationMenuLink>
                                            </li>
                                            <li class="col-span-1">
                                                <NavigationMenuLink asChild>
                                                    <a
                                                        href="#contact"
                                                        class="group hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground flex items-center space-x-3 rounded-md p-2 no-underline transition-colors outline-none select-none"
                                                        :class="{ 'text-primary bg-primary/10': activeSection === 'contact' }"
                                                    >
                                                        <Mail class="text-primary h-4 w-4" />
                                                        <span class="text-sm font-medium">{{ t('Contact') }}</span>
                                                    </a>
                                                </NavigationMenuLink>
                                            </li>
                                        </div>
                                    </ul>
                                </NavigationMenuContent>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>

                    <template v-if="pageProps.auth.user">
                        <Link :href="route('dashboard')">
                            <Button variant="ghost" size="sm" class="text-muted-foreground hover:text-foreground">
                                {{ t('Dashboard') }}
                            </Button>
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')">
                            <Button variant="ghost" size="sm" class="text-muted-foreground hover:text-foreground"> {{ t('Login') }} </Button>
                        </Link>
                        <Link :href="route('register')">
                            <Button size="sm" class="bg-primary hover:bg-primary/90 text-primary-foreground"> {{ t('Register') }} </Button>
                        </Link>
                    </template>
                    <ThemeSwitcher />
                </div>

                <!-- Tablet menu button -->
                <div class="hidden items-center space-x-2 md:flex lg:hidden">
                    <Button
                        class="text-muted-foreground hover:text-foreground cursor-pointer transition-all duration-200"
                        variant="ghost"
                        @click="toggleTabletMenu"
                        :aria-label="isTabletMenuOpen ? t('Close Menu') : t('Open Menu')"
                    >
                        {{ t('Menu') }}
                        <ChevronDown :class="{ 'rotate-180': isTabletMenuOpen }" class="h-6 w-6 transition-transform duration-200" />
                    </Button>

                    <template v-if="pageProps.auth.user">
                        <Link :href="route('dashboard')">
                            <Button variant="ghost" size="sm" class="text-muted-foreground hover:text-foreground">
                                {{ t('Dashboard') }}
                            </Button>
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')">
                            <Button variant="ghost" size="sm" class="text-muted-foreground hover:text-foreground"> {{ t('Login') }} </Button>
                        </Link>
                        <Link :href="route('register')">
                            <Button size="sm" class="bg-primary hover:bg-primary/90 text-primary-foreground"> {{ t('Register') }} </Button>
                        </Link>
                    </template>

                    <ThemeSwitcher />
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center space-x-2 md:hidden">
                    <ThemeSwitcher />
                    <Button variant="ghost" size="icon" @click="toggleMobileMenu" :aria-label="isMobileMenuOpen ? t('Close Menu') : t('Open Menu')">
                        <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </Button>
                </div>
            </div>

            <!-- Tablet Menu -->
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 max-h-0"
                enter-to-class="opacity-100 max-h-[600px]"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 max-h-[600px]"
                leave-to-class="opacity-0 max-h-0"
            >
                <div v-if="isTabletMenuOpen" class="overflow-hidden lg:hidden">
                    <div class="bg-background/95 border-border/50 space-y-1 rounded-b-lg border-t py-4 backdrop-blur-sm">
                        <!-- Product Section -->
                        <div class="px-2">
                            <div class="text-muted-foreground px-2 py-1 text-xs font-semibold tracking-wide uppercase">
                                {{ t('Product') }}
                            </div>
                            <Motion
                                v-for="(link, index) in productLinksMobile"
                                :key="link.href"
                                :initial="{ opacity: 0, x: -20 }"
                                :animate="{ opacity: 1, x: 0 }"
                                :transition="{ duration: 0.3, delay: index * 0.05 }"
                            >
                                <a
                                    :href="link.href"
                                    @click="closeTabletMenu"
                                    class="text-muted-foreground hover:text-foreground hover:bg-muted/50 mx-2 flex items-center space-x-3 rounded-md px-4 py-3 transition-all duration-200"
                                    :class="{ 'text-primary bg-primary/10': activeSection === link.section }"
                                >
                                    <component :is="link.icon" class="h-4 w-4" />
                                    <span>{{ t(link.label) }}</span>
                                </a>
                            </Motion>
                        </div>

                        <!-- Resources Section -->
                        <div class="mt-4 px-2">
                            <div class="text-muted-foreground px-2 py-1 text-xs font-semibold tracking-wide uppercase">
                                {{ t('Resources') }}
                            </div>
                            <Motion
                                v-for="(link, index) in resourceLinksMobile"
                                :key="link.href"
                                :initial="{ opacity: 0, x: -20 }"
                                :animate="{ opacity: 1, x: 0 }"
                                :transition="{ duration: 0.3, delay: (productLinksMobile.length + index) * 0.05 }"
                            >
                                <a
                                    :href="link.href"
                                    @click="closeTabletMenu"
                                    class="text-muted-foreground hover:text-foreground hover:bg-muted/50 mx-2 flex items-center space-x-3 rounded-md px-4 py-3 transition-all duration-200"
                                    :class="{ 'text-primary bg-primary/10': activeSection === link.section }"
                                >
                                    <component :is="link.icon" class="h-4 w-4" />
                                    <span>{{ t(link.label) }}</span>
                                </a>
                            </Motion>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Mobile Menu -->
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 max-h-0"
                enter-to-class="opacity-100 max-h-[600px]"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 max-h-[600px]"
                leave-to-class="opacity-0 max-h-0"
            >
                <div v-if="isMobileMenuOpen" class="overflow-hidden md:hidden">
                    <div class="bg-background/95 border-border/50 space-y-1 rounded-b-lg border-t py-4 backdrop-blur-sm">
                        <!-- Product Section -->
                        <div class="px-2">
                            <div class="text-muted-foreground px-2 py-1 text-xs font-semibold tracking-wide uppercase">
                                {{ t('Product') }}
                            </div>
                            <Motion
                                v-for="(link, index) in productLinksMobile"
                                :key="link.href"
                                :initial="{ opacity: 0, x: -20 }"
                                :animate="{ opacity: 1, x: 0 }"
                                :transition="{ duration: 0.3, delay: index * 0.05 }"
                            >
                                <a
                                    :href="link.href"
                                    @click="closeMobileMenu"
                                    class="text-muted-foreground hover:text-foreground hover:bg-muted/50 mx-2 flex items-center space-x-3 rounded-md px-4 py-3 transition-all duration-200"
                                    :class="{ 'text-primary bg-primary/10': activeSection === link.section }"
                                >
                                    <component :is="link.icon" class="h-4 w-4" />
                                    <span>{{ t(link.label) }}</span>
                                </a>
                            </Motion>
                        </div>

                        <!-- Resources Section -->
                        <div class="mt-4 px-2">
                            <div class="text-muted-foreground px-2 py-1 text-xs font-semibold tracking-wide uppercase">
                                {{ t('Resources') }}
                            </div>
                            <Motion
                                v-for="(link, index) in resourceLinksMobile"
                                :key="link.href"
                                :initial="{ opacity: 0, x: -20 }"
                                :animate="{ opacity: 1, x: 0 }"
                                :transition="{ duration: 0.3, delay: (productLinksMobile.length + index) * 0.05 }"
                            >
                                <a
                                    :href="link.href"
                                    @click="closeMobileMenu"
                                    class="text-muted-foreground hover:text-foreground hover:bg-muted/50 mx-2 flex items-center space-x-3 rounded-md px-4 py-3 transition-all duration-200"
                                    :class="{ 'text-primary bg-primary/10': activeSection === link.section }"
                                >
                                    <component :is="link.icon" class="h-4 w-4" />
                                    <span>{{ t(link.label) }}</span>
                                </a>
                            </Motion>
                        </div>

                        <!-- CTAs Section -->
                        <div class="border-border/50 mt-4 space-y-2 border-t px-2 pt-4">
                            <Motion :initial="{ opacity: 0, y: 10 }" :animate="{ opacity: 1, y: 0 }" :transition="{ duration: 0.3, delay: 0.4 }">
                                <Button variant="ghost" class="w-full justify-start" @click="closeMobileMenu">
                                    <LogIn class="mr-2 h-4 w-4" />
                                    {{ t('Login') }}
                                </Button>
                            </Motion>
                            <Motion :initial="{ opacity: 0, y: 10 }" :animate="{ opacity: 1, y: 0 }" :transition="{ duration: 0.3, delay: 0.45 }">
                                <Button class="bg-primary hover:bg-primary/90 text-primary-foreground w-full justify-start" @click="closeMobileMenu">
                                    <UserPlus class="mr-2 h-4 w-4" />
                                    {{ t('Register') }}
                                </Button>
                            </Motion>
                            <!-- <Motion :initial="{ opacity: 0, y: 10 }" :animate="{ opacity: 1, y: 0 }" :transition="{ duration: 0.3, delay: 0.5 }">
                                    <Button variant="outline" class="w-full justify-start" @click="closeMobileMenu">
                                        <Play class="mr-2 h-4 w-4" />
                                        Ver Demo
                                    </Button>
                                </Motion> -->
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </nav>
</template>
