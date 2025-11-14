<script setup lang="ts">
import ShareResourceModal from '@/components/shares/ShareResourceModal.vue';
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
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Separator } from '@/components/ui/separator';
import { useTranslations } from '@/composables/useTranslations';
import { MedicationScheduleFrequencyEnum } from '@/Enums/MedicationScheduleFrequencyEnum';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Medication, Prescription, Schedule } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertTriangleIcon, ChevronLeft, MoreVertical, Pencil, Share2, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

// Define las propiedades que la página recibe del backend
const props = defineProps<{
    medication: Medication & { schedules: Schedule[]; prescriptions: Prescription[] };
}>();

const { t } = useTranslations();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Dashboard'),
        href: route('dashboard'),
    },
    {
        title: t('Medications'),
        href: route('medications.index'),
    },
    {
        title: props.medication.name || t('Medication #') + props.medication.id,
        href: route('medications.show', props.medication.id),
    },
];

const isShareModalOpen = ref(false);
const showDeleteDialog = ref(false);
const medicationToDelete = ref<Medication | null>(null);

const shareableResource = {
    id: props.medication.id,
    type: 'medication' as const,
    name: props.medication.name || undefined,
};

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
 * Formatea la información de un horario de medicamento para una visualización concisa.
 * @param {Schedule} schedule - El objeto del horario.
 * @returns {string} Una descripción textual del horario.
 */
const formatScheduleInfo = (schedule: Schedule): string => {
    const frequencyLabel = t(schedule.frequency_type_label ?? schedule.frequency_type);
    let info = `${frequencyLabel}`;

    if (schedule.frequency_type === MedicationScheduleFrequencyEnum.SPECIFIC_DAYS) {
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
 */
const handleDeleteMedication = () => {
    if (!medicationToDelete.value) return;
    router.delete(route('medications.destroy', medicationToDelete.value.id), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            medicationToDelete.value = null;
            showDeleteDialog.value = false;
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).join(' ');
            toast.error(t('Deletion Failed'), {
                description: errorMessages || t('Could not delete the medication. Please try again.'),
            });
            medicationToDelete.value = null;
            showDeleteDialog.value = false;
        },
    });
};
</script>

<template>
    <Head :title="t('Medication Details')" />
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div class="animate-in fade-in-0 pb-8 duration-500 md:pb-12">
            <div class="container mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <!-- Cabecera con acciones -->
                <div class="mb-6 flex items-center justify-between">
                    <Link :href="route('medications.index')">
                        <Button variant="outline" class="flex items-center gap-2">
                            <ChevronLeft class="h-4 w-4" />
                            <span>{{ t('Back to Medications') }}</span>
                        </Button>
                    </Link>

                    <div class="flex items-center gap-2">
                        <!-- Botón de Compartir -->
                        <Button variant="outline" size="icon" @click="isShareModalOpen = true">
                            <Share2 class="h-4 w-4" />
                        </Button>

                        <!-- Menú de Opciones (Editar/Eliminar) -->
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon">
                                    <MoreVertical class="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <Link :href="route('medications.edit', medication.id)">
                                    <DropdownMenuItem class="flex cursor-pointer items-center gap-2">
                                        <Pencil class="h-4 w-4" />
                                        <span>{{ t('Edit') }}</span>
                                    </DropdownMenuItem>
                                </Link>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    class="flex cursor-pointer items-center gap-2 text-red-600 focus:text-red-500"
                                    @click="confirmDeleteMedication(props.medication)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    <span>{{ t('Delete') }}</span>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-2xl">
                            {{ medication.name }}
                        </CardTitle>
                        <CardDescription v-if="medication.type_label">
                            {{ t(medication.type_label) }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-6">
                        <!-- Detalles del Medicamento -->
                        <div class="grid grid-cols-2 gap-4 text-sm md:grid-cols-3">
                            <div v-if="medication.dosage">
                                <p class="text-muted-foreground">{{ t('Dosage') }}</p>
                                <p>{{ medication.dosage }}</p>
                            </div>
                            <div v-if="medication.strength">
                                <p class="text-muted-foreground">{{ t('Strength') }}</p>
                                <p>{{ medication.strength }}</p>
                            </div>
                            <div v-if="medication.quantity">
                                <p class="text-muted-foreground">{{ t('Quantity') }}</p>
                                <p>{{ medication.quantity }}</p>
                            </div>
                        </div>

                        <!-- Instrucciones -->
                        <div v-if="medication.instructions">
                            <h3 class="mb-2 font-semibold">{{ t('Instructions') }}</h3>
                            <p class="text-muted-foreground whitespace-pre-wrap">{{ medication.instructions }}</p>
                        </div>

                        <Separator />

                        <!-- Horarios Asociados -->
                        <div>
                            <h3 class="mb-4 font-semibold">{{ t('Schedules') }}</h3>
                            <div v-if="medication.schedules.length > 0" class="space-y-2">
                                <div
                                    v-for="schedule in medication.schedules"
                                    :key="schedule.id"
                                    class="bg-muted/50 flex items-center justify-between rounded-md p-2"
                                >
                                    <span class="text-primary font-mono">{{ schedule.time_to_take }}</span>
                                    <span class="text-muted-foreground text-sm">
                                        {{ formatScheduleInfo(schedule) }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-muted-foreground rounded-lg border-2 border-dashed py-4 text-center">
                                <p>{{ t('No schedules have been set for this medication.') }}</p>
                            </div>
                        </div>

                        <Separator />

                        <!-- Recetas Asociadas -->
                        <div>
                            <h3 class="mb-4 font-semibold">{{ t('Included in Prescriptions') }}</h3>
                            <template v-if="medication.prescriptions.length > 0">
                                <div class="flex flex-col gap-4">
                                    <Link
                                        v-for="prescription in medication.prescriptions"
                                        :key="prescription.id"
                                        :href="route('prescriptions.show', prescription.id)"
                                    >
                                        <div class="hover:bg-muted/50 flex items-center justify-between rounded-md border p-3 transition-colors">
                                            <span>{{ prescription.title }}</span>
                                            <span class="text-muted-foreground text-sm">{{ prescription.doctor_name }}</span>
                                        </div>
                                    </Link>
                                </div>
                            </template>
                            <template v-else>
                                <div class="text-muted-foreground rounded-lg border-2 border-dashed py-4 text-center">
                                    <p>{{ t('This medication is not part of any prescription.') }}</p>
                                </div>
                            </template>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <ShareResourceModal v-if="isShareModalOpen" v-model="isShareModalOpen" :resource="shareableResource" />

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
                                {
                                    name: medicationToDelete.name || '#' + medicationToDelete.id,
                                },
                            )
                        }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        class="cursor-pointer"
                        @click="
                            showDeleteDialog = false;
                            medicationToDelete = null;
                        "
                        >{{ t('Cancel') }}</AlertDialogCancel
                    >
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
