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
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Prescription } from '@/types'; // Importar los tipos actualizados
import { Head, Link, router } from '@inertiajs/vue3';
import {
    AlertTriangleIcon,
    CalendarIcon as CalendarIconLucide,
    Edit3Icon,
    FileTextIcon,
    InfoIcon,
    PillIcon,
    PlusCircleIcon,
    Trash2Icon,
    UserIcon,
} from 'lucide-vue-next'; // UserMdIcon para doctor
import { ref } from 'vue';
import { toast } from 'vue-sonner';

/**
 * Props recibidas por el componente Index de Prescripciones.
 */
interface Props {
    prescriptions: Prescription[];
}

const props = defineProps<Props>();
const { t, formatDate } = useTranslations(); // Asumimos que formatDate está en useTranslations o lo importamos

// Estado para el diálogo de confirmación de eliminación
const showDeleteDialog = ref(false);
const prescriptionToDelete = ref<Prescription | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Dashboard'),
        href: route('dashboard'),
    },
    {
        title: t('Prescriptions'),
        href: route('prescriptions.index'),
    },
];

/**
 * Abre el diálogo de confirmación para eliminar una prescripción.
 * @param {Prescription} prescription - La prescripción a eliminar.
 */
const confirmDeletePrescription = (prescription: Prescription) => {
    prescriptionToDelete.value = prescription;
    showDeleteDialog.value = true;
};

/**
 * Ejecuta la eliminación de la prescripción.
 */
const handleDeletePrescription = () => {
    if (!prescriptionToDelete.value) return;
    router.delete(route('prescriptions.destroy', prescriptionToDelete.value.id), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            prescriptionToDelete.value = null;
            showDeleteDialog.value = false;
            // Toast manejado globalmente por mensaje flash
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).join(' ');
            toast.error(t('Deletion Failed'), {
                description: errorMessages || t('Could not delete the prescription. Please try again.'),
            });
            prescriptionToDelete.value = null;
            showDeleteDialog.value = false;
        },
    });
};

/**
 * Navega a la página de edición de la prescripción.
 * @param {number} prescriptionId - El ID de la prescripción a editar.
 */
const editPrescription = (prescriptionId: number) => {
    router.get(route('prescriptions.edit', prescriptionId));
};

/**
 * Navega a la página de visualización de la prescripción.
 * @param {number} prescriptionId - El ID de la prescripción a ver.
 */
const viewPrescription = (prescriptionId: number) => {
    router.get(route('prescriptions.show', prescriptionId));
};
</script>

<template>
    <Head :title="t('My Prescriptions')" />
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <template #page_content_header>
            <div class="mx-auto w-full max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
                <div class="xs:flex-row xs:items-center xs:justify-between mb-8 flex w-full flex-col gap-4">
                    <h1 class="text-primary dark:text-remedi-light-blue flex items-center text-2xl leading-tight font-semibold">
                        <FileTextIcon class="text-secondary mr-3 inline-block h-7 w-7" />
                        {{ t('My Prescriptions') }}
                    </h1>
                    <Link :href="route('prescriptions.create')">
                        <Button
                            variant="default"
                            size="default"
                            class="bg-primary text-primary-foreground hover:bg-primary/90 w-full transition-shadow duration-200 hover:shadow-md sm:w-auto"
                        >
                            <PlusCircleIcon class="mr-2 h-4 w-4" />
                            {{ t('Create New Prescription') }}
                        </Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="animate-in fade-in-0 pb-8 duration-500 md:pb-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="props.prescriptions.length === 0" class="py-16 text-center">
                    <InfoIcon class="mx-auto mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" />
                    <h3 class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-300">{{ t('No Prescriptions Yet') }}</h3>
                    <p class="mt-2 text-base text-gray-500 dark:text-gray-400">{{ t('Get started by creating your first prescription.') }}</p>
                    <div class="mt-8">
                        <Link :href="route('prescriptions.create')">
                            <Button
                                variant="default"
                                size="lg"
                                class="bg-primary text-primary-foreground hover:bg-primary/90 transition-shadow duration-200 hover:shadow-md"
                            >
                                <PlusCircleIcon class="mr-2 h-5 w-5" />
                                {{ t('Create Prescription') }}
                            </Button>
                        </Link>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="prescription in props.prescriptions"
                        :key="prescription.id"
                        class="bg-card text-card-foreground shadow-remedi-mint-green/20 hover:shadow-remedi-mint-green/40 flex cursor-pointer flex-col overflow-hidden rounded-xl transition-all duration-300 ease-out hover:-translate-y-1 hover:scale-[1.02]"
                        @click="viewPrescription(prescription.id)"
                    >
                        <CardHeader class="border-border/60 bg-muted/20 dark:bg-muted/5 border-b p-5 pb-4">
                            <div class="flex items-start justify-between">
                                <CardTitle class="text-primary dark:text-remedi-light-blue mb-1 text-lg leading-tight font-semibold">
                                    {{ prescription.title || t('Prescription') + ` #${prescription.id}` }}
                                </CardTitle>
                                <div class="flex space-x-1 rtl:space-x-reverse">
                                    <Button
                                        @click.stop="editPrescription(prescription.id)"
                                        variant="ghost"
                                        size="icon"
                                        class="text-muted-foreground hover:text-primary dark:hover:text-remedi-light-blue h-7 w-7 transition-colors"
                                        :aria-label="t('Edit') + ' ' + (prescription.title || `Prescription ${prescription.id}`)"
                                    >
                                        <Edit3Icon class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        @click.stop="confirmDeletePrescription(prescription)"
                                        variant="ghost"
                                        size="icon"
                                        class="text-muted-foreground hover:text-destructive h-7 w-7 transition-colors dark:hover:text-red-400"
                                        :aria-label="t('Delete') + ' ' + (prescription.title || `Prescription ${prescription.id}`)"
                                    >
                                        <Trash2Icon class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                            <CardDescription class="text-muted-foreground mt-1 space-y-0.5 text-sm">
                                <div v-if="prescription.doctor_name" class="flex items-center">
                                    <UserIcon class="text-secondary mr-1.5 h-3.5 w-3.5 opacity-80" />
                                    {{ prescription.doctor_name }}
                                </div>
                                <div v-if="prescription.prescription_date" class="flex items-center">
                                    <CalendarIconLucide class="text-secondary mr-1.5 h-3.5 w-3.5 opacity-80" />
                                    {{ prescription.prescription_date_formatted || formatDate(prescription.prescription_date) }}
                                </div>
                            </CardDescription>
                        </CardHeader>

                        <CardContent class="flex-grow space-y-3 p-5">
                            <div v-if="prescription.medications && prescription.medications.length > 0">
                                <h4 class="text-muted-foreground mb-2 flex items-center text-xs font-semibold tracking-wider uppercase">
                                    <PillIcon class="text-secondary mr-1.5 h-3.5 w-3.5 opacity-80" />
                                    {{ t('Medications') }} ({{ prescription.medications.length }})
                                </h4>
                                <ul class="max-h-32 space-y-1.5 overflow-y-auto pr-1">
                                    <!-- Limitar altura y añadir scroll -->
                                    <li
                                        v-for="med in prescription.medications"
                                        :key="med.id"
                                        class="text-foreground/80 dark:text-foreground/70 text-xs"
                                    >
                                        <span class="font-medium">{{ med.name }}</span>
                                        <span v-if="med.pivot.dosage_on_prescription" class="text-muted-foreground">
                                            - {{ med.pivot.dosage_on_prescription }}</span
                                        >
                                    </li>
                                </ul>
                            </div>
                            <p v-else class="text-muted-foreground text-xs italic">{{ t('No medications assigned to this prescription.') }}</p>

                            <div v-if="prescription.notes" class="border-border/30 mt-3 border-t pt-3 text-sm">
                                <h4 class="text-muted-foreground mb-1 text-xs font-semibold tracking-wider uppercase">{{ t('Notes') }}</h4>
                                <p class="text-foreground/80 dark:text-foreground/70 text-xs whitespace-pre-line">{{ prescription.notes }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
            <AlertDialogContent v-if="prescriptionToDelete">
                <AlertDialogHeader>
                    <AlertDialogTitle class="flex items-center">
                        <AlertTriangleIcon class="text-destructive mr-2 h-5 w-5" />
                        {{ t('Confirm Deletion') }}
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        {{
                            t('Are you sure you want to delete this prescription (:title)? This action cannot be undone.', {
                                title: prescriptionToDelete.title || '#' + prescriptionToDelete.id,
                            })
                        }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        @click="
                            showDeleteDialog = false;
                            prescriptionToDelete = null;
                        "
                        >{{ t('Cancel') }}</AlertDialogCancel
                    >
                    <AlertDialogAction @click="handleDeletePrescription" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
                        {{ t('Delete Prescription') }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AuthenticatedLayout>
</template>
