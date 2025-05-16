<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslations } from '@/composables/useTranslations';
import { MedicationScheduleFrequencyEnum } from '@/Enums/MedicationScheduleFrequencyEnum';
import type { MedicationTypeEnum } from '@/Enums/MedicationTypeEnum';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertTriangleIcon, CalendarDaysIcon, ClockIcon, Edit3Icon, InfoIcon, PillIcon, PlusCircleIcon, TagIcon, Trash2Icon } from 'lucide-vue-next'; // TagIcon para el tipo
import { ref } from 'vue';
import { toast } from 'vue-sonner';

/**
 * Interface para la estructura de un horario de medicamento tal como se recibe del backend.
 */
interface Schedule {
    id: number;
    time_to_take: string;
    frequency_type: MedicationScheduleFrequencyEnum;
    frequency_type_label: string;
    days_of_week?: number[] | null;
    interval_in_days?: number | null;
    interval_in_hours?: number | null;
    start_date: string;
    end_date?: string | null;
    is_active: boolean;
}

/**
 * Interface para la estructura de un medicamento tal como se recibe del backend.
 */
interface Medication {
    id: number;
    name: string;
    type: MedicationTypeEnum | null;
    type_label: string | null;
    dosage: string | null;
    strength: string | null;
    quantity: number | null;
    instructions: string | null;
    schedules: Schedule[];
    created_at: string;
    updated_at: string;
}

/**
 * Props recibidas por el componente Index de Medicamentos.
 */
interface Props {
    medications: Medication[];
    // No necesitamos medicationTypeLabels ni frequencyTypeLabels si los labels vienen en el objeto medication
}

const props = defineProps<Props>();
const { t, currentLocale } = useTranslations();

const showDeleteDialog = ref(false);
const medicationToDelete = ref<Medication | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Dashboard'),
        href: route('dashboard'),
    },
    {
        title: t('Medications'),
        href: route('medications.index'),
    },
];

/**
 * Formatea un array de números de días de la semana (0-6) a una cadena legible.
 * @param {number[] | null | undefined} days - Array de números de días (0 para Domingo, 6 para Sábado).
 * @returns {string} Cadena formateada de los días (ej. "Mon, Wed, Fri") o "-".
 */
const formatDaysOfWeek = (days: number[] | null | undefined): string => {
    if (!days || days.length === 0) return '-';
    // Los labels para los días ya se traducen en el composable o en el template
    const dayNames = [t('Sun'), t('Mon'), t('Tue'), t('Wed'), t('Thu'), t('Fri'), t('Sat')];
    return days
        .map((dayIndex) => dayNames[dayIndex] || String(dayIndex))
        .sort((a, b) => dayNames.indexOf(a) - dayNames.indexOf(b))
        .join(', ');
};

/**
 * Formatea una cadena de fecha (YYYY-MM-DD) a un formato localizado y legible.
 * @param {string | null | undefined} dateString - Fecha en formato YYYY-MM-DD.
 * @returns {string} Fecha formateada o "-".
 */
const formatDate = (dateString: string | null | undefined): string => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString(currentLocale.value === 'es' ? 'es-DO' : 'en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

/**
 * Formatea la información de un horario de medicamento para una visualización concisa.
 * @param {Schedule} schedule - El objeto del horario.
 * @returns {string} Una descripción textual del horario.
 */
const formatScheduleInfo = (schedule: Schedule): string => {
    let info = `${schedule.time_to_take} - ${t(schedule.frequency_type_label)}`;

    if (schedule.frequency_type === MedicationScheduleFrequencyEnum.SPECIFIC_DAYS) {
        // Usar el valor string del enum
        info += ` (${formatDaysOfWeek(schedule.days_of_week)})`;
    } else if (schedule.frequency_type === MedicationScheduleFrequencyEnum.INTERVAL_IN_DAYS && schedule.interval_in_days) {
        info += ` (${t('every')} ${schedule.interval_in_days} ${t('days', { count: schedule.interval_in_days })})`;
    } else if (schedule.frequency_type === MedicationScheduleFrequencyEnum.HOURLY_INTERVAL && schedule.interval_in_hours) {
        info += ` (${t('every')} ${schedule.interval_in_hours} ${t('hours', { count: schedule.interval_in_hours })})`;
    }
    return info;
};

/**
 * Abre el diálogo de confirmación para eliminar un medicamento.
 * @param {Medication} medication - El medicamento a eliminar.
 */
const confirmDeleteMedication = (medication: Medication) => {
    medicationToDelete.value = medication;
    showDeleteDialog.value = true;
};

/**
 * Ejecuta la eliminación del medicamento.
 * Se llama desde la acción de confirmación del AlertDialog.
 */
const handleDeleteMedication = () => {
    if (!medicationToDelete.value) return;

    router.delete(route('medications.destroy', medicationToDelete.value.id), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            // toast.success(t('Medication Deleted!'), {
            //  description: t('The medication "{name}" has been removed.', { name: medicationToDelete.value!.name })
            // });
            medicationToDelete.value = null;
            showDeleteDialog.value = false;
        },
        onError: () => {
            // El toast de error ya es manejado por el mensaje flash global desde AppLayout.vue
            // Opcionalmente, puedes mostrar un error más específico aquí si es necesario
            toast.error(t('Deletion Failed'), {
                description: t('Could not delete the medication. Please try again.'),
            });
            medicationToDelete.value = null;
            showDeleteDialog.value = false;
        },
    });
};

/**
 * Navega a la página de edición para el medicamento especificado.
 * @param {number} medicationId - El ID del medicamento a editar.
 */
const editMedication = (medicationId: number) => {
    router.get(route('medications.edit', medicationId)); // <--- ACTUALIZADO AQUÍ
};
</script>

<template>
    <Head :title="t('My Medications')" />
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <template #page_content_header>
            <div class="mx-auto w-full max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
                <div class="xs:flex-row xs:items-center xs:justify-between mb-8 flex w-full flex-col gap-4">
                    <h1 class="text-primary dark:text-remedi-light-blue flex items-center text-2xl leading-tight font-semibold">
                        <PillIcon class="text-secondary mr-3 inline-block h-7 w-7" />
                        {{ t('My Medications') }}
                    </h1>
                    <Link :href="route('medications.create')">
                        <Button
                            variant="default"
                            size="default"
                            class="bg-primary text-primary-foreground hover:bg-primary/90 w-full transition-shadow duration-200 hover:shadow-md sm:w-auto"
                        >
                            <PlusCircleIcon class="mr-2 h-4 w-4" />
                            {{ t('Add New Medication') }}
                        </Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="animate-in fade-in-0 py-6 duration-500">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="props.medications.length === 0" class="py-16 text-center">
                    <InfoIcon class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" />
                    <h3 class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-300">{{ t('No Medications Yet') }}</h3>
                    <p class="mt-2 text-base text-gray-500 dark:text-gray-400">
                        {{ t('Get started by adding your first medication.') }}
                    </p>
                    <div class="mt-8">
                        <Link :href="route('medications.create')">
                            <Button
                                variant="default"
                                size="lg"
                                class="bg-primary text-primary-foreground hover:bg-primary/90 transition-shadow duration-200 hover:shadow-md"
                            >
                                <PlusCircleIcon class="mr-2 h-5 w-5" />
                                {{ t('Add Medication') }}
                            </Button>
                        </Link>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <Card
                        v-for="medication in props.medications"
                        :key="medication.id"
                        class="bg-card text-card-foreground shadow-remedi-mint-green/20 hover:shadow-remedi-mint-green/50 flex cursor-pointer flex-col overflow-hidden rounded-xl shadow-lg transition-all duration-300 ease-out hover:-translate-y-1 hover:scale-[1.02]"
                        @click="editMedication(medication.id)"
                        tabindex="0"
                        @keydown.enter="editMedication(medication.id)"
                    >
                        <CardHeader class="border-border/60 bg-muted/20 dark:bg-muted/5 border-b p-5 pb-4">
                            <div class="flex items-start justify-between">
                                <CardTitle class="text-primary dark:text-remedi-light-blue mb-1 text-lg leading-tight font-semibold">
                                    {{ medication.name }}
                                </CardTitle>
                                <div class="flex space-x-1 rtl:space-x-reverse">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="text-muted-foreground hover:text-primary dark:hover:text-remedi-light-blue h-7 w-7 transition-colors"
                                        @click.stop="editMedication(medication.id)"
                                    >
                                        <span class="sr-only">{{ t('Edit') }}</span>
                                        <Edit3Icon class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="text-muted-foreground hover:text-destructive h-7 w-7 transition-colors dark:hover:text-red-400"
                                        @click.stop="confirmDeleteMedication(medication)"
                                        :aria-label="t('Delete') + ' ' + medication.name"
                                    >
                                        <Trash2Icon class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                            <CardDescription class="text-muted-foreground mt-1 items-center text-sm">
                                <TagIcon class="text-secondary mr-1.5 inline-block h-3.5 w-3.5 align-middle opacity-80" />
                                <span v-if="medication.type_label">{{ t(medication.type_label) }}</span>
                                <span v-if="medication.dosage"> - {{ medication.dosage }}</span>
                                <span v-if="medication.strength"> ({{ medication.strength }})</span>
                                <span v-if="!medication.type_label && !medication.dosage && !medication.strength">{{
                                    t('No type/dosage info')
                                }}</span>
                            </CardDescription>
                        </CardHeader>

                        <CardContent class="flex-grow space-y-4 px-5">
                            <div v-if="medication.instructions" class="text-sm">
                                <h4 class="text-muted-foreground mb-1 text-xs font-semibold tracking-wider uppercase">
                                    {{ t('Instructions') }}
                                </h4>
                                <p class="text-foreground/80 dark:text-foreground/70">{{ medication.instructions }}</p>
                            </div>

                            <div>
                                <h4 class="text-muted-foreground mb-2 flex items-center text-xs font-semibold tracking-wider uppercase">
                                    <ClockIcon class="text-secondary mr-1.5 h-3.5 w-3.5 opacity-80" />
                                    {{ t('Schedules') }}
                                </h4>
                                <ul v-if="medication.schedules.length > 0" class="space-y-2">
                                    <li
                                        v-for="schedule in medication.schedules"
                                        :key="schedule.id"
                                        class="text-foreground/90 dark:text-foreground/80 bg-muted/30 dark:bg-muted/10 rounded-md p-2 text-sm"
                                    >
                                        <div class="font-medium">{{ formatScheduleInfo(schedule) }}</div>
                                        <div class="text-muted-foreground mt-0.5 ml-0.5 flex items-center text-xs">
                                            <CalendarDaysIcon class="mr-1 inline-block h-3.5 w-3.5 text-slate-400 dark:text-slate-500" />
                                            {{ t('Starts') }}: {{ formatDate(schedule.start_date) }}
                                            <span v-if="schedule.end_date">
                                                <span class="mx-1">·</span> {{ t('Ends') }}: {{ formatDate(schedule.end_date) }}
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                                <p v-else class="text-muted-foreground py-2 text-sm italic">
                                    {{ t('No schedules defined.') }}
                                </p>
                            </div>
                        </CardContent>
                        <div
                            v-if="medication.quantity !== null"
                            class="text-muted-foreground border-border/60 bg-muted/20 dark:bg-muted/5 border-t px-5 py-3 text-xs"
                        >
                            {{ t('Quantity') }}: {{ medication.quantity }}
                        </div>
                    </Card>
                </div>
            </div>
        </div>

        <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
            <AlertDialogContent v-if="medicationToDelete">
                <AlertDialogHeader>
                    <AlertDialogTitle class="flex items-center">
                        <AlertTriangleIcon class="text-destructive mr-2 h-5 w-5" />
                        {{ t('Confirm Deletion') }}
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        {{
                            t(
                                'Are you sure you want to delete the medication :name? All of its associated schedules will also be removed. This action cannot be undone.',
                                { name: medicationToDelete.name },
                            )
                        }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        @click="
                            showDeleteDialog = false;
                            medicationToDelete = null;
                        "
                        class="cursor-pointer"
                    >
                        {{ t('Cancel') }}
                    </AlertDialogCancel>
                    <AlertDialogAction
                        @click="handleDeleteMedication"
                        class="bg-destructive text-destructive-foreground hover:bg-destructive/90 cursor-pointer"
                    >
                        {{ t('Delete Medication') }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AuthenticatedLayout>
</template>
