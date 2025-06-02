<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslations } from '@/composables/useTranslations';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import type { Prescription, Schedule } from '@/types'; // Importar los tipos
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    CalendarIcon as CalendarIconLucide,
    Edit3Icon,
    FileTextIcon,
    InfoIcon,
    PillIcon,
    PrinterIcon,
    UserIcon,
} from 'lucide-vue-next';
import { toast } from 'vue-sonner';

/**
 * Props recibidas por el componente Show de Prescripciones.
 */
interface Props {
    prescription: Prescription; // La receta que se está mostrando
}

const props = defineProps<Props>();
const { t } = useTranslations();

/**
 * Formatea los días de la semana para visualización.
 * @param {number[] | null | undefined} days - Array de números de días (0-6).
 * @returns {string} String formateado de los días.
 */
const formatDaysOfWeek = (days: number[] | null | undefined): string => {
    if (!days || days.length === 0) return '-';
    const dayNames = [t('Sun'), t('Mon'), t('Tue'), t('Wed'), t('Thu'), t('Fri'), t('Sat')];
    return days
        .map((dayIndex) => dayNames[dayIndex] || String(dayIndex))
        .sort((a, b) => dayNames.indexOf(a) - dayNames.indexOf(b))
        .join(', ');
};

/**
 * Formatea la información de un horario de medicamento para una visualización concisa.
 * Esta función se usa para mostrar los horarios *generales* del medicamento,
 * no los de la prescripción específica.
 * @param {Schedule} schedule - El objeto del horario del medicamento.
 * @returns {string} Una descripción textual del horario.
 */
const formatMedicationScheduleInfo = (schedule: Schedule): string => {
    let info = `${schedule.time_to_take} - ${t(schedule.frequency_type_label || schedule.frequency_type.toString())}`;
    if (schedule.frequency_type === 'specific_days') {
        info += ` (${formatDaysOfWeek(schedule.days_of_week)})`;
    } else if (schedule.frequency_type === 'interval_in_days' && schedule.interval_in_days) {
        info += ` (${t('every')} ${schedule.interval_in_days} ${t('days', { count: schedule.interval_in_days })})`;
    } else if (schedule.frequency_type === 'hourly_interval' && schedule.interval_in_hours) {
        info += ` (${t('every')} ${schedule.interval_in_hours} ${t('hours', { count: schedule.interval_in_hours })})`;
    }
    return info;
};

const exportToPDF = () => {
    toast.info(t('Coming soon!'), { description: t('PDF export functionality will be available soon.') });
};
</script>

<template>
    <Head :title="t('Prescription Details') + (props.prescription.title ? ` - ${props.prescription.title}` : ` #${props.prescription.id}`)" />
    <AuthenticatedLayout>
        <template #page_content_header>
            <div class="mx-auto w-full max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
                <div class="xs:flex-row xs:items-center xs:justify-between mb-8 flex w-full flex-col gap-4">
                    <h1 class="text-primary dark:text-remedi-light-blue flex items-center text-2xl leading-tight font-semibold">
                        <FileTextIcon class="text-secondary mr-3 inline-block h-7 w-7" />
                        {{ t('Prescription Details') }}
                    </h1>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Link :href="route('prescriptions.index')">
                            <Button
                                variant="outline"
                                size="default"
                                class="hover:dark:text-secondary w-full transition-shadow duration-200 hover:shadow-md sm:w-auto"
                            >
                                <ArrowLeftIcon class="mr-2 h-4 w-4" />
                                {{ t('Back to Prescriptions') }}
                            </Button>
                        </Link>
                        <Link :href="route('prescriptions.edit', props.prescription.id)">
                            <Button
                                variant="default"
                                size="default"
                                class="bg-primary text-primary-foreground hover:bg-primary/90 w-full transition-shadow duration-200 hover:shadow-md sm:w-auto"
                            >
                                <Edit3Icon class="mr-2 h-4 w-4" />
                                {{ t('Edit Prescription') }}
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="animate-in fade-in-0 pb-8 duration-500 md:pb-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <Card class="bg-card text-card-foreground border-border/50 overflow-hidden shadow-xl">
                    <CardHeader class="bg-muted/30 border-border/60 border-b p-6">
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-primary dark:text-remedi-light-blue text-xl">{{ t('Prescription Information') }}</CardTitle>
                            <Button variant="outline" size="sm" @click="exportToPDF" class="hover:dark:text-secondary ml-auto">
                                <PrinterIcon class="mr-2 h-4 w-4" />
                                {{ t('Export to PDF') }}
                            </Button>
                        </div>
                        <CardDescription
                            v-if="props.prescription.doctor_name || props.prescription.prescription_date_formatted"
                            class="text-muted-foreground space-y-1 pt-2"
                        >
                            <div v-if="props.prescription.title" class="flex items-center text-sm">
                                <UserIcon class="text-secondary mr-2 h-4 w-4 opacity-90" />
                                <strong>{{ t('Title') }}:</strong>
                                <span class="ml-1"> {{ props.prescription.title }}</span>
                            </div>
                            <div v-if="props.prescription.doctor_name" class="flex items-center text-sm">
                                <UserIcon class="text-secondary mr-2 h-4 w-4 opacity-90" />
                                <strong>{{ t('Doctor') }}:</strong> <span class="ml-1">{{ props.prescription.doctor_name }}</span>
                            </div>
                            <div v-if="props.prescription.prescription_date_formatted" class="flex items-center text-sm">
                                <CalendarIconLucide class="text-secondary mr-2 h-4 w-4 opacity-90" />
                                <strong>{{ t('Date') }}:</strong> <span class="ml-1">{{ props.prescription.prescription_date_formatted }}</span>
                            </div>
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-6 p-6 md:p-8">
                        <div v-if="props.prescription.notes" class="space-y-1">
                            <h3 class="text-muted-foreground text-sm font-semibold tracking-wider uppercase">{{ t('Notes') }}</h3>
                            <p class="text-foreground/90 dark:text-foreground/80 whitespace-pre-line">{{ props.prescription.notes }}</p>
                        </div>
                        <hr v-if="props.prescription.notes && props.prescription.medications.length > 0" class="border-border/60" />

                        <section class="space-y-4" aria-labelledby="medications-on-prescription-heading">
                            <h3
                                id="medications-on-prescription-heading"
                                class="text-md text-primary dark:text-remedi-light-blue flex items-center font-semibold"
                            >
                                <PillIcon class="text-secondary mr-2 h-5 w-5" />
                                {{ t('Prescribed Medications') }}
                            </h3>

                            <div
                                v-if="props.prescription.medications.length === 0"
                                class="text-muted-foreground rounded-md border border-dashed py-6 text-center text-sm"
                            >
                                <InfoIcon class="mx-auto mb-2 h-8 w-8 text-gray-400" />
                                {{ t('No medications assigned to this prescription.') }}
                            </div>

                            <div v-else class="space-y-4">
                                <Card v-for="med in props.prescription.medications" :key="med.id" class="bg-muted/20 dark:bg-muted/10 shadow-sm">
                                    <CardHeader class="px-4 pt-4 pb-3">
                                        <CardTitle class="text-md flex items-center justify-between">
                                            <span>{{ med.name }}</span>
                                            <span
                                                v-if="med.type_label"
                                                class="bg-secondary/20 dark:text-secondary text-secondary-foreground rounded-full px-2 py-0.5 text-xs font-normal"
                                                >{{ t(med.type_label) }}</span
                                            >
                                        </CardTitle>
                                        <CardDescription v-if="med.strength" class="text-xs">{{ med.strength }}</CardDescription>
                                    </CardHeader>
                                    <CardContent class="space-y-2 px-4 pb-4 text-sm">
                                        <div v-if="med.pivot.dosage_on_prescription">
                                            <strong class="text-muted-foreground">{{ t('Dosage for this prescription') }}:</strong>
                                            {{ med.pivot.dosage_on_prescription }}
                                        </div>
                                        <div v-if="med.pivot.quantity_prescribed">
                                            <strong class="text-muted-foreground">{{ t('Quantity for this prescription') }}:</strong>
                                            {{ med.pivot.quantity_prescribed }}
                                        </div>
                                        <div v-if="med.pivot.instructions_on_prescription">
                                            <strong class="text-muted-foreground">{{ t('Instructions for this prescription') }}:</strong>
                                            {{ med.pivot.instructions_on_prescription }}
                                        </div>

                                        <div v-if="med.schedules && med.schedules.length > 0" class="border-border/30 mt-2 border-t pt-2">
                                            <p class="text-muted-foreground mb-1 text-xs">
                                                <em>{{ t('General schedules for this medication (may differ from prescription)') }}:</em>
                                            </p>
                                            <ul class="space-y-1 text-xs">
                                                <li v-for="schedule in med.schedules" :key="schedule.id">
                                                    {{ formatMedicationScheduleInfo(schedule) }}
                                                </li>
                                            </ul>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </section>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
