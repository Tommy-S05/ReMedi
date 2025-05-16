import { usePage } from '@inertiajs/vue3';
import { computed, type ComputedRef } from 'vue'; // Importar ComputedRef
import type { SharedData } from '@/types';

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

    return { t, currentLocale };
}
