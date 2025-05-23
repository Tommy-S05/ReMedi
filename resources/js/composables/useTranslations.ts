import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, type ComputedRef } from 'vue'; // Importar ComputedRef

/**
 * Interface para el retorno del composable `useTranslations`.
 */
interface UseTranslationsReturn {
    /**
     * Traduce una clave dada, opcionalmente reemplazando placeholders.
     * @param {string} key - La clave de traducción.
     * @param {Record<string, string | number>} [replacements] - Placeholders y sus valores.
     * @returns {string} La cadena traducida o la clave original si no se encuentra.
     */
    t: (key: string, replacements?: Record<string, string | number>) => string;
    /**
     * El locale actual de la aplicación.
     * @type {ComputedRef<'en' | 'es'>}
     */
    currentLocale: ComputedRef<SharedData['current_locale']>;
    /**
     * Formatea una cadena de fecha (YYYY-MM-DD) a un formato localizado y legible.
     * @param {string | null | undefined} dateString - Fecha en formato YYYY-MM-DD.
     * @param {string} [localeOverride] - Opcional. Un locale específico para usar en el formateo.
     * @returns {string} Fecha formateada o "-".
     */
    formatDate: (dateString: string | null | undefined, localeOverride?: string) => string;
}

/**
 * Composable para manejar traducciones en componentes Vue utilizando
 * las traducciones compartidas por Inertia.
 *
 * @returns {UseTranslationsReturn} Objeto con la función de traducción `t` y el `currentLocale`.
 */
export function useTranslations(): UseTranslationsReturn {
    const page = usePage<SharedData>();

    /**
     * Objeto computado que contiene las traducciones para el locale actual.
     * @type {ComputedRef<Record<string, string>>}
     */
    const translations: ComputedRef<Record<string, string>> = computed(() => page.props.translations || {});

    /**
     * Locale actual de la aplicación.
     * @type {ComputedRef<'en' | 'es'>}
     */
    const currentLocale: ComputedRef<SharedData['current_locale']> = computed(() => page.props.current_locale);

    /**
     * Traduce una clave dada.
     *
     * @param {string} key - La clave de traducción (ej. "Hello World").
     * @param {Record<string, string | number>} [replacements] - Opcional. Un objeto de placeholders y sus reemplazos.
     * @returns {string} La cadena traducida o la clave si no se encuentra.
     */
    const t = (key: string, replacements?: Record<string, string | number>): string => {
        let translation = translations.value[key] || key;

        if (replacements) {
            Object.keys(replacements).forEach((replaceKey) => {
                // Usar :placeholder como en Laravel para consistencia, o {placeholder}
                const placeholder = `:${replaceKey}`;
                // Escapar el placeholder para usarlo en RegExp de forma segura
                const escapedPlaceholder = placeholder.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                translation = translation.replace(new RegExp(escapedPlaceholder, 'g'), String(replacements[replaceKey]));
            });
        }
        return translation;
    };

    /**
     * Formatea una cadena de fecha (YYYY-MM-DD) a un formato localizado y legible.
     * @param {string | null | undefined} dateString - Fecha en formato YYYY-MM-DD.
     * @param {string} [localeOverride] - Opcional. Un locale específico para usar en el formateo.
     * @returns {string} Fecha formateada o "-".
     */
    const formatDate = (dateString: string | null | undefined, localeOverride?: string): string => {
        if (!dateString) return '-';
        try {
            // El añadir 'T00:00:00' ayuda a que el constructor de Date lo interprete como fecha local
            // y no como UTC, lo que puede causar problemas de "un día menos" dependiendo de la zona horaria.
            const date = new Date(dateString);
            const localeToUse = localeOverride || currentLocale.value;

            // Intl.DateTimeFormat es más robusto para la localización
            return new Intl.DateTimeFormat(localeToUse === 'es' ? 'es-DO' : 'en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            }).format(date);
        } catch (error) {
            console.error('Error formatting date:', dateString, error);
            return dateString; // Devolver el original si hay error
        }
    };

    // const formatDate = (dateString: string | null | undefined): string => {
    //     if (!dateString) return '-';
    //     const date = new Date(dateString);
    //     return date.toLocaleDateString(currentLocale.value === 'es' ? 'es-DO' : 'en-US', {
    //         year: 'numeric',
    //         month: 'short',
    //         day: 'numeric',
    //     });
    // };

    return { t, currentLocale, formatDate };
}
