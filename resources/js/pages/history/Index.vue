<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslations } from '@/composables/useTranslations';
import { MedicationLogStatusEnum } from '@/Enums/MedicationLogStatusEnum';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import { PaginationLinks, PaginationMeta } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { AlertCircleIcon, CheckCircle2Icon, HistoryIcon, LoaderIcon, PillIcon, XCircleIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

interface FormattedTakeLog {
    id: number;
    status: MedicationLogStatusEnum;
    status_label: string;
    scheduled_for_formatted: string;
    action_taken_at_formatted: string | null;
    medication: {
        id: number;
        name: string;
    };
}

interface Props {
    history: Record<string, FormattedTakeLog[]>;
    pagination_meta: PaginationMeta;
    pagination_links: PaginationLinks;
}

const props = defineProps<Props>();
const { t } = useTranslations();

const localHistory = ref(props.history);
const paginationMeta = ref(props.pagination_meta);
const paginationLinks = ref(props.pagination_links);
const isLoadingMore = ref(false);

/**
 * @description Mapea un estado de log a un componente de icono y clases de estilo.
 * @param {MedicationLogStatusEnum} status - El estado del log.
 * @returns {{ icon: any, class: string }}
 */
const statusVisuals = (status: MedicationLogStatusEnum) => {
    switch (status) {
        case MedicationLogStatusEnum.TAKEN:
            return { icon: CheckCircle2Icon, class: 'text-green-500 dark:text-green-400' };
        case MedicationLogStatusEnum.SKIPPED:
            return { icon: XCircleIcon, class: 'text-yellow-500 dark:text-yellow-400' };
        case MedicationLogStatusEnum.MISSED:
            return { icon: AlertCircleIcon, class: 'text-destructive' };
        default:
            return { icon: PillIcon, class: 'text-muted-foreground' };
    }
};

/**
 * @description Carga la siguiente página de resultados del historial y los fusiona con los existentes.
 */
const loadMore = async () => {
    if (!paginationLinks.value.next || isLoadingMore.value || paginationMeta.value.current_page >= paginationMeta.value.last_page) return;

    isLoadingMore.value = true;
    const nextPage = paginationMeta.value.current_page + 1;

    await axios
        .get(route('history.fetch', { page: nextPage }))
        .then(({ data }) => {
            const newHistoryData = data.data.history;
            const mergedHistory = { ...localHistory.value };

            for (const dateKey in newHistoryData) {
                if (mergedHistory[dateKey]) {
                    mergedHistory[dateKey].push(...newHistoryData[dateKey]);
                } else {
                    mergedHistory[dateKey] = newHistoryData[dateKey];
                }
            }
            localHistory.value = mergedHistory;
            paginationMeta.value = data.data.pagination_meta;
            paginationLinks.value = data.data.pagination_links;
        })
        .catch(() => {
            toast.error(t('Error'), { description: t('Could not load more history.') });
        })
        .finally(() => {
            isLoadingMore.value = false;
        });
};

/**
 * Verifica si hay más páginas disponibles
 */
const hasMorePages = () => {
    return paginationLinks.value.next !== null;
};

/**
 * Obtiene el texto del botón de cargar más
 */
const getLoadMoreButtonText = () => {
    if (isLoadingMore.value) {
        return t('Loading...');
    }

    const remaining = paginationMeta.value.total - paginationMeta.value.to;
    if (remaining > 0) {
        return t('Load More (:count remaining)', { count: remaining });
    }

    return t('Load More');
};
</script>

<template>
    <Head :title="t('Medication History')" />
    <AuthenticatedLayout>
        <template #page_content_header>
            <div class="mx-auto w-full max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
                <div class="mb-8 flex w-full flex-col items-center gap-4 sm:flex-row sm:justify-between">
                    <h1 class="text-accent dark:text-remedi-light-blue flex items-center text-2xl leading-tight font-semibold">
                        <HistoryIcon class="text-secondary mr-3 inline-block h-7 w-7" />
                        {{ t('Medication History') }}
                    </h1>
                </div>
            </div>
        </template>

        <div class="animate-in fade-in-0 pb-8 duration-500 md:pb-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <Card class="bg-card text-card-foreground border-border/50 overflow-hidden shadow-lg">
                    <CardHeader>
                        <CardTitle>{{ t('Recent Activity') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="Object.keys(localHistory).length === 0" class="text-muted-foreground py-10 text-center">
                            <HistoryIcon class="mx-auto mb-2 h-12 w-12 text-gray-300 dark:text-gray-600" />
                            <p>{{ t('No history records found yet.') }}</p>
                            <p class="mt-1 text-sm">{{ t('Actions you take on your reminders will appear here.') }}</p>
                        </div>

                        <div v-else class="space-y-6">
                            <div v-for="(logs, date) in localHistory" :key="date" class="relative">
                                <div class="bg-card/80 sticky top-0 z-10 py-2 backdrop-blur-sm">
                                    <h3 class="text-md text-primary dark:text-remedi-light-blue font-semibold">
                                        {{ t(date) }}
                                    </h3>
                                </div>

                                <ul class="border-border/70 mt-2 ml-2 space-y-3 border-l-2 pl-6">
                                    <li v-for="log in logs" :key="log.id" class="relative flex items-start gap-4">
                                        <div
                                            :class="[
                                                'absolute top-1 -left-[30px] flex h-4 w-4 items-center justify-center rounded-full',
                                                statusVisuals(log.status).class,
                                            ]"
                                        >
                                            <component :is="statusVisuals(log.status).icon" class="h-4 w-4 text-white" />
                                        </div>

                                        <div class="flex-grow">
                                            <div class="flex items-center justify-between">
                                                <p class="text-foreground font-medium">{{ log.medication.name }}</p>
                                                <p class="text-muted-foreground font-mono text-sm">
                                                    {{ log.scheduled_for_formatted }}
                                                </p>
                                            </div>
                                            <p :class="['text-sm font-semibold', statusVisuals(log.status).class]">
                                                {{ t(log.status_label) }}
                                                <span v-if="log.action_taken_at_formatted" class="text-muted-foreground text-xs font-normal">
                                                    {{ t('at') }} {{ log.action_taken_at_formatted }}
                                                </span>
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <!-- Load More Button -->
                            <div v-if="hasMorePages()" class="pt-4 text-center">
                                <Button
                                    variant="outline"
                                    @click="loadMore"
                                    :disabled="isLoadingMore"
                                    class="dark:hover:text-remedi-mint-green min-w-[120px]"
                                >
                                    <LoaderIcon v-if="isLoadingMore" class="mr-2 h-4 w-4 animate-spin" />
                                    {{ getLoadMoreButtonText() }}
                                </Button>
                            </div>

                            <div v-else-if="paginationMeta.total > paginationMeta.per_page" class="pt-4 text-center">
                                <p class="text-muted-foreground text-sm">
                                    {{ t('All records loaded') }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
