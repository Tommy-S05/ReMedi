<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useTranslations } from '@/composables/useTranslations';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    CalendarIcon as CalendarIconLucide,
    FileTextIcon,
    PillIcon,
    PlusCircleIcon,
    SaveIcon,
    Trash2Icon,
    UserIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

/**
 * Interface para los medicamentos disponibles del usuario para seleccionar.
 */
interface UserMedicationOption {
    id: number;
    name: string;
}

/**
 * Interface para los detalles de un medicamento dentro del formulario de prescripción.
 * Esto se enviará al backend como parte de 'medication_details'.
 */
interface PrescribedMedicationFormDetail {
    medication_id: number;
    name?: string; // Para mostrar en el UI, no se envía directamente si no es necesario
    dosage_on_prescription: string | undefined;
    quantity_prescribed: string | undefined;
    instructions_on_prescription: string | undefined;
}

/**
 * Interface para el formulario principal de la prescripción.
 */
interface PrescriptionForm {
    title: string | undefined;
    doctor_name: string | undefined;
    prescription_date: string | undefined;
    notes: string | undefined;
    medication_details: PrescribedMedicationFormDetail[]; // Array de medicamentos con sus detalles pivot

    [key: string]: any;
}

/**
 * Props recibidas por el componente.
 */
interface Props {
    userMedications: UserMedicationOption[]; // Lista de medicamentos del usuario para seleccionar
}

const props = defineProps<Props>();
const { t } = useTranslations();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Dashboard'),
        href: route('dashboard'),
    },
    {
        title: t('Prescriptions'),
        href: route('prescriptions.index'),
    },
    {
        title: t('Add New Prescription'),
        href: route('prescriptions.create'),
    },
];

const form = useForm<PrescriptionForm>({
    title: '',
    doctor_name: '',
    prescription_date: new Date().toISOString().split('T')[0], // Default a hoy
    notes: '',
    medication_details: [],
});

/**
 * ID del medicamento actualmente seleccionado en el select para añadir.
 */
const selectedMedicationIdToAdd = ref<number | null>(null);

/**
 * Medicamentos del usuario que aún no han sido añadidos al formulario de la receta.
 * Esto es para el selector, para no poder añadir el mismo medicamento múltiples veces directamente.
 */
const availableMedicationsToAdd = computed(() => {
    const addedMedicationIds = new Set(form.medication_details.map((md) => md.medication_id));
    return props.userMedications.filter((med) => !addedMedicationIds.has(med.id));
});

/**
 * Añade el medicamento seleccionado (selectedMedicationIdToAdd) a la lista de form.medication_details.
 */
const addMedicationToPrescription = () => {
    if (selectedMedicationIdToAdd.value) {
        const medicationToAdd = props.userMedications.find((med) => med.id === selectedMedicationIdToAdd.value);
        if (medicationToAdd) {
            const alreadyAdded = form.medication_details.some((md) => md.medication_id === medicationToAdd.id);
            if (!alreadyAdded) {
                form.medication_details.push({
                    medication_id: medicationToAdd.id,
                    name: medicationToAdd.name,
                    dosage_on_prescription: '',
                    quantity_prescribed: '',
                    instructions_on_prescription: '',
                });
            }
        }
        selectedMedicationIdToAdd.value = null;
    }
};

/**
 * Elimina un medicamento de la lista form.medication_details.
 * @param {number} index - El índice del medicamento a eliminar en el array medication_details.
 */
const removeMedicationFromPrescription = (index: number) => {
    form.medication_details.splice(index, 1);
};

/**
 * Envía el formulario para crear la prescripción.
 */
const submit = () => {
    const dataToSubmit = {
        ...form.data(),
        medication_details: form.medication_details.map((md) => ({
            medication_id: md.medication_id,
            dosage_on_prescription: md.dosage_on_prescription,
            quantity_prescribed: md.quantity_prescribed,
            instructions_on_prescription: md.instructions_on_prescription,
        })),
    };

    form.transform(() => dataToSubmit).post(route('prescriptions.store'), {
        onSuccess: () => {
            toast.success(t('Prescription Added!'), {
                description: t('The prescription has been successfully saved.', { name: form.name }),
                duration: 5000,
            });
            form.reset();
        },
        onError: (error) => {
            console.log(error);
            toast.error(t('Creation Failed'), {
                description: t('Please correct the errors in the form for the prescription.'),
            });
        },
    });
};
</script>

<template>
    <Head :title="t('Add New Medication')" />
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <template #page_content_header>
            <div class="mx-auto w-full max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
                <div class="xs:flex-row xs:items-center xs:justify-between mb-8 flex w-full flex-col gap-4">
                    <h1 class="text-primary dark:text-remedi-light-blue flex items-center text-2xl leading-tight font-semibold">
                        <FileTextIcon class="text-secondary mr-3 inline-block h-6 w-6" />
                        {{ t('Create New Prescription') }}
                    </h1>
                    <Link :href="route('prescriptions.index')">
                        <Button
                            variant="outline"
                            size="default"
                            class="hover:dark:text-secondary w-full transition-all duration-200 hover:shadow-md sm:w-auto"
                        >
                            <ArrowLeftIcon class="mr-2 h-4 w-4" />
                            {{ t('Back to Prescriptions') }}
                        </Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="animate-in fade-in py-6 duration-500">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <Card class="bg-card text-card-foreground border-border/50 overflow-hidden shadow-xl">
                    <CardHeader class="bg-muted/30 border-border/60 xs:p-6 border-b p-4">
                        <CardTitle class="text-primary dark:text-remedi-light-blue text-xl">
                            {{ t('Prescription Information') }}
                        </CardTitle>
                        <CardDescription class="text-muted-foreground pt-1">
                            {{ t('Fill in the details for the new prescription.') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="xs:p-6 p-4 md:p-8">
                        <form @submit.prevent="submit" class="space-y-10">
                            <section class="space-y-5" aria-labelledby="prescription-details-heading">
                                <h2 id="medication-details-heading" class="sr-only">
                                    {{ t('Prescription Details') }}
                                </h2>
                                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="title" class="font-medium">{{ t('Title') }} <span class="text-destructive">*</span></Label>
                                        <Input
                                            id="title"
                                            v-model="form.title"
                                            type="text"
                                            class="mt-1 block w-full transition-shadow"
                                            :placeholder="t('e.g., Flu Treatment Dr. Smith')"
                                        />
                                        <InputError :message="form.errors.title" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="doctor_name" class="flex items-center font-medium">
                                            <UserIcon class="mr-1.5 h-4 w-4 text-slate-500" />
                                            {{ t('Doctor Name') }}
                                        </Label>
                                        <Input
                                            id="doctor_name"
                                            v-model="form.doctor_name"
                                            type="text"
                                            class="mt-1 block w-full transition-shadow"
                                            :placeholder="t('e.g., Dr. John Doe')"
                                        />
                                        <InputError :message="form.errors.doctor_name" />
                                    </div>
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="prescription_date" class="flex items-center font-medium">
                                            <CalendarIconLucide class="mr-1.5 h-4 w-4 text-slate-500" />
                                            {{ t('Prescription Date') }}
                                        </Label>
                                        <Input
                                            id="prescription_date"
                                            v-model="form.prescription_date"
                                            type="date"
                                            class="mt-1 block w-full transition-shadow"
                                        />
                                        <InputError :message="form.errors.prescription_date" />
                                    </div>
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="notes" class="font-medium">
                                            {{ t('Notes') }}
                                        </Label>
                                        <Textarea
                                            id="notes"
                                            v-model="form.notes"
                                            class="mt-1 block min-h-[100px] w-full transition-shadow"
                                            :placeholder="t('e.g., Follow up in 2 weeks')"
                                        />
                                        <InputError :message="form.errors.notes" />
                                    </div>
                                </div>
                            </section>

                            <hr class="border-border/60" />

                            <section class="space-y-6" aria-labelledby="medications-on-prescription-heading">
                                <div class="flex flex-col items-center justify-between gap-2 sm:flex-row">
                                    <h3
                                        id="medications-on-prescription-heading"
                                        class="text-primary dark:text-remedi-light-blue flex items-center text-lg font-medium"
                                    >
                                        <PillIcon class="text-primary mr-2 h-5 w-5" />
                                        {{ t('Assigned Medications') }}
                                    </h3>

                                    <div class="flex w-full items-center gap-2 sm:w-auto">
                                        <Select v-model="selectedMedicationIdToAdd" :disabled="availableMedicationsToAdd.length === 0">
                                            <SelectTrigger class="w-full cursor-pointer transition-shadow sm:w-[200px]">
                                                <SelectValue :placeholder="t('Select a medication')" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectLabel v-if="availableMedicationsToAdd.length === 0"
                                                        >{{ t('No more medications to add') }}
                                                    </SelectLabel>
                                                    <SelectItem v-for="med in availableMedicationsToAdd" :key="med.id" :value="med.id">
                                                        {{ med.name }}
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>

                                        <Button
                                            type="button"
                                            @click="addMedicationToPrescription"
                                            :disabled="!selectedMedicationIdToAdd"
                                            size="icon"
                                            class="bg-remedi-mint-green hover:bg-remedi-mint-green/90 shrink-0 text-white"
                                        >
                                            <PlusCircleIcon class="h-5 w-5" />
                                            <span class="sr-only">{{ t('Add Medication') }}</span>
                                        </Button>
                                    </div>
                                </div>
                                <InputError :message="form.errors.medication_details" />
                                <InputError :message="form.errors.medication_ids" />

                                <div
                                    v-if="form.medication_details.length === 0"
                                    class="text-muted-foreground rounded-md border border-dashed py-6 text-center text-sm"
                                >
                                    {{ t('No medications added to this prescription yet.') }}
                                </div>

                                <div v-else class="space-y-5">
                                    <Card
                                        v-for="(medDetail, index) in form.medication_details"
                                        :key="medDetail.medication_id"
                                        class="bg-muted/20 dark:bg-muted/10 group relative shadow-sm"
                                    >
                                        <CardHeader class="px-6">
                                            <CardTitle class="text-md flex items-center justify-between">
                                                <div class="space-y-2">
                                                    <span>{{ medDetail.name || t('Medication') }}</span>
                                                    <div
                                                        v-if="form.errors[`medication_details.${index}.medication_id`]"
                                                        class="text-destructive mt-1 text-xs"
                                                    >
                                                        {{ form.errors[`medication_details.${index}.medication_id`] }}
                                                    </div>
                                                </div>
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    @click="removeMedicationFromPrescription(index)"
                                                    class="text-muted-foreground hover:text-destructive h-7 w-7 opacity-50 transition-all duration-200 group-hover:opacity-100"
                                                    :aria-label="t('Remove') + ' ' + medDetail.name"
                                                >
                                                    <Trash2Icon class="h-4 w-4" />
                                                </Button>
                                            </CardTitle>
                                        </CardHeader>
                                        <CardContent class="space-y-4 px-4 pb-2">
                                            <div class="space-y-2">
                                                <Label :for="`med_dosage_${index}`" class="text-xs font-medium">
                                                    {{ t('Dosage on Prescription') }}</Label
                                                >
                                                <Input
                                                    :id="`med_dosage_${index}`"
                                                    v-model="medDetail.dosage_on_prescription"
                                                    type="text"
                                                    class="mt-1 block w-full text-sm"
                                                    :placeholder="t('e.g., 1 pill every 8 hours')"
                                                />
                                                <InputError :message="form.errors[`medication_details.${index}.dosage_on_prescription`]" />
                                            </div>
                                            <div class="space-y-2">
                                                <Label :for="`med_quantity_${index}`" class="text-xs font-medium">{{
                                                    t('Quantity Prescribed')
                                                }}</Label>
                                                <Input
                                                    :id="`med_quantity_${index}`"
                                                    v-model="medDetail.quantity_prescribed"
                                                    type="text"
                                                    class="mt-1 block w-full text-sm"
                                                    :placeholder="t('e.g., 30 pills, 1 bottle')"
                                                />
                                                <InputError :message="form.errors[`medication_details.${index}.quantity_prescribed`]" />
                                            </div>
                                            <div class="space-y-2">
                                                <Label :for="`med_instructions_${index}`" class="text-xs font-medium">{{
                                                    t('Instructions on Prescription')
                                                }}</Label>
                                                <Textarea
                                                    :id="`med_instructions_${index}`"
                                                    v-model="medDetail.instructions_on_prescription"
                                                    class="mt-1 block min-h-[60px] w-full text-sm"
                                                    :placeholder="t('e.g., For 7 days, complete course')"
                                                />
                                                <InputError :message="form.errors[`medication_details.${index}.instructions_on_prescription`]" />
                                            </div>
                                        </CardContent>
                                    </Card>
                                </div>
                            </section>

                            <footer
                                class="border-border/60 xs:justify-end xs:flex-row flex flex-col-reverse items-center justify-center gap-3 border-t pt-6 sm:space-x-6"
                            >
                                <Link
                                    :href="route('prescriptions.index')"
                                    class="text-muted-foreground hover:text-primary dark:hover:text-remedi-light-blue text-sm font-medium transition-colors duration-200"
                                >
                                    {{ t('Cancel') }}
                                </Link>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    size="lg"
                                    class="xs:w-auto w-full min-w-[150px] transition-all duration-200 hover:shadow-lg active:scale-[0.98]"
                                >
                                    <span v-if="form.processing" class="mr-2 animate-spin">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                    </span>
                                    <SaveIcon v-else class="mr-2 h-4 w-4" />
                                    {{ t('Save Prescription') }}
                                </Button>
                            </footer>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
