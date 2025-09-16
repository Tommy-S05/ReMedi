<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import { useTranslations } from '@/composables/useTranslations';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType, FlashMessages, SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// Para acceder a las props compartidas, incluyendo 'flash'
const page = usePage<SharedData>();

const { t } = useTranslations();

/**
 * Muestra un toast basado en el tipo y mensaje.
 * @param {keyof FlashMessages} type - El tipo de mensaje (success, error, etc.).
 * @param {string} message - El mensaje a mostrar.
 */
const showToast = (type: keyof FlashMessages, message: string | undefined) => {
    if (!message) return;

    switch (type) {
        case 'success':
            toast.success(t('Success!'), { description: t(message), duration: 5000 });
            break;
        case 'error':
            toast.error(t('Error!'), { description: t(message), duration: 7000 });
            break;
        case 'warning':
            toast.warning(t('Warning!'), { description: t(message), duration: 6000 });
            break;
        case 'info':
            toast.info(t('Info'), { description: t(message), duration: 5000 });
            break;
    }
};

/**
 * Observa los cambios en los mensajes flash de Inertia.
 * @remarks
 * Se utiliza `watch` con `deep: true` para detectar cambios en el objeto flash.
 */
watch(
    () => page.props.flash,
    (newFlash, oldFlash) => {
        if (newFlash) {
            if (newFlash.success && newFlash.success !== oldFlash?.success) showToast('success', newFlash.success);
            if (newFlash.error && newFlash.error !== oldFlash?.error) showToast('error', newFlash.error);
            if (newFlash.warning && newFlash.warning !== oldFlash?.warning) showToast('warning', newFlash.warning);
            if (newFlash.info && newFlash.info !== oldFlash?.info) showToast('info', newFlash.info);
        }
    },
    { deep: true },
);

/**
 * Comprueba los mensajes flash iniciales cuando el componente se monta.
 * @remarks
 * Esto asegura que si la página se carga con un mensaje flash (ej. después de una redirección),
 * el toast se muestre.
 */
onMounted(() => {
    const initialFlash = page.props.flash;
    if (initialFlash) {
        if (initialFlash.success) showToast('success', initialFlash.success);
        if (initialFlash.error) showToast('error', initialFlash.error);
        if (initialFlash.warning) showToast('warning', initialFlash.warning);
        if (initialFlash.info) showToast('info', initialFlash.info);
    }
});
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <template #page_content_header>
            <slot name="page_content_header" />
        </template>
        <slot />
    </AppSidebarLayout>
    <Toaster richColors position="top-right" :duration="5000" />
</template>
