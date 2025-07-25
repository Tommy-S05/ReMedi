<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useTranslations } from '@/composables/useTranslations';
import { MedicationScheduleFrequencyEnum } from '@/Enums/MedicationScheduleFrequencyEnum';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, CalendarIcon, ClockIcon, PackageIcon, PillIcon, PlusCircleIcon, Trash2Icon } from 'lucide-vue-next';
import { computed } from 'vue'; // Importar el composable
import { toast } from 'vue-sonner';

/**
 * Represents a single option for a select input.
 */
interface SelectOption {
    value: string;
    label: string;
}

/**
 * Props received by the CreateMedication component.
 */
interface Props {
    /** List of available medication types for selection. */
    medicationTypes: SelectOption[];
    /** List of available frequency options for schedules. */
    frequencyOptions: SelectOption[];
}

const props = defineProps<Props>();

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
        title: t('Add New Medication'),
        href: route('medications.create'),
    },
];

/**
 * Interface for the structure of a single schedule entry in the form.
 */
interface ScheduleForm {
    time_to_take: string;
    frequency_type: string;
    days_of_week?: number[];
    interval_in_days?: number | undefined;
    interval_in_hours?: number | undefined;
    start_date: string;
    end_date?: string | undefined;
}

/**
 * Interface for the main medication form data.
 */
interface MedicationForm {
    name: string;
    type: string | undefined;
    dosage: string | undefined;
    strength: string | undefined;
    quantity: number | undefined;
    instructions: string | undefined;
    schedules: ScheduleForm[];

    [key: string]: any;
}

const form = useForm<MedicationForm>({
    name: '',
    type: props.medicationTypes.length > 0 ? props.medicationTypes[0].value : undefined,
    dosage: '',
    strength: '',
    quantity: undefined,
    instructions: '',
    schedules: [
        {
            time_to_take: '08:00',
            frequency_type: props.frequencyOptions.length > 0 ? props.frequencyOptions[0].value : 'daily',
            start_date: new Date().toISOString().split('T')[0],
            end_date: undefined,
            days_of_week: [],
            interval_in_days: undefined,
            interval_in_hours: undefined,
        },
    ],
});

/**
 * Options for the days of the week checkboxes.
 */
const daysOfWeekOptions = computed(() => [
    { id: 1, label: t('Mon'), value: 1 },
    { id: 2, label: t('Tue'), value: 2 },
    { id: 3, label: t('Wed'), value: 3 },
    { id: 4, label: t('Thu'), value: 4 },
    { id: 5, label: t('Fri'), value: 5 },
    { id: 6, label: t('Sat'), value: 6 },
    { id: 0, label: t('Sun'), value: 0 },
]);

// const frequencyOptions = [
//     { value: 'daily', label: 'Daily' },
//     { value: 'specific_days', label: 'Specific days of the week' },
//     { value: 'interval_in_days', label: 'Every X days' },
//     { value: 'hourly_interval', label: 'Every X hours' },
// ];

/**
 * Adds a new empty schedule entry to the form.
 */
function addSchedule() {
    form.schedules.push({
        time_to_take: '08:00',
        frequency_type: 'daily',
        start_date: new Date().toISOString().split('T')[0],
        end_date: undefined,
        days_of_week: [],
        interval_in_days: undefined,
        interval_in_hours: undefined,
    });
}

/**
 * Removes a schedule entry from the form at the given index.
 * @param {number} index - The index of the schedule to remove.
 */
function removeSchedule(index: number) {
    if (form.schedules.length > 1) {
        form.schedules.splice(index, 1);
    }
}

/**
 * Submits the medication form to the backend.
 */
function submit() {
    form.post(route('medications.store'), {
        onSuccess: () => {
            toast.success(t('Medication Added!'), {
                description: t('The medication :name has been successfully saved.', { name: form.name }),
                duration: 5000, // Duración en ms
            });
            form.reset();
        },
        onError: (errors) => {
            let errorMessage = t('Please correct the errors in the form.');
            if (errors.message) {
                errorMessage = errors.message;
            }

            toast.error(t('Failed to Add Medication'), {
                description: errorMessage,
                duration: 7000,
            });
        },
    });
}

/**
 * Checks if a specific day is selected for a given schedule.
 * @param {number} scheduleIndex - The index of the schedule.
 * @param {number} dayValue - The value of the day to check (0-6).
 * @returns {boolean} True if the day is selected, false otherwise.
 */
const isDaySelected = (scheduleIndex: number, dayValue: number): boolean => {
    return form.schedules[scheduleIndex].days_of_week?.includes(dayValue) ?? false;
};

/**
 * Toggles the selection state of a day for a given schedule.
 * @param {number} scheduleIndex - The index of the schedule.
 * @param {number} dayValue - The value of the day to toggle (0-6).
 */
const toggleDay = (scheduleIndex: number, dayValue: number): void => {
    const schedule = form.schedules[scheduleIndex];
    if (!schedule.days_of_week) {
        schedule.days_of_week = [];
    }
    const dayIndex = schedule.days_of_week.indexOf(dayValue);
    if (dayIndex > -1) {
        schedule.days_of_week.splice(dayIndex, 1);
    } else {
        schedule.days_of_week.push(dayValue);
    }
};
</script>

<template>
    <Head :title="t('Add New Medication')" />
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <template #page_content_header>
            <div class="mx-auto w-full max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
                <div class="xs:flex-row xs:items-center xs:justify-between mb-8 flex w-full flex-col gap-4">
                    <h1 class="text-primary dark:text-remedi-light-blue flex items-center text-2xl leading-tight font-semibold">
                        <PillIcon class="text-secondary mr-3 inline-block h-6 w-6" />
                        {{ t('Add New Medication') }}
                    </h1>
                    <Link :href="route('medications.index')">
                        <Button
                            variant="default"
                            size="default"
                            class="bg-primary text-primary-foreground hover:bg-primary/90 w-full transition-shadow duration-200 hover:shadow-md sm:w-auto"
                        >
                            <ArrowLeftIcon class="mr-2 h-4 w-4" />
                            {{ t('Back') }}
                        </Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="animate-in fade-in py-6 duration-500">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <Card class="bg-card text-card-foreground border-border/50 overflow-hidden shadow-xl">
                    <CardHeader class="bg-muted/30 border-border/60 border-b p-6">
                        <CardTitle class="text-primary dark:text-remedi-light-blue text-xl">
                            {{ t('Medication Details') }}
                        </CardTitle>
                        <CardDescription class="text-muted-foreground pt-1">
                            {{ t('Provide the necessary information for the new medication.') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="p-6 md:p-8">
                        <form @submit.prevent="submit" class="space-y-10">
                            <section class="space-y-5" aria-labelledby="medication-details-heading">
                                <h2 id="medication-details-heading" class="sr-only">
                                    {{ t('Medication Details') }}
                                </h2>
                                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="name" class="font-medium">{{ t('Name') }} <span class="text-destructive">*</span></Label>
                                        <Input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="mt-1 block w-full transition-shadow"
                                            required
                                            :placeholder="t('e.g., Amoxicillin')"
                                        />
                                        <div v-if="form.errors.name" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors.name }}
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="type" class="flex items-center font-medium">
                                            <PackageIcon class="mr-1.5 h-4 w-4 text-slate-500" />
                                            {{ t('Type') }}
                                        </Label>
                                        <Select v-model="form.type">
                                            <SelectTrigger id="type" class="mt-1 w-full transition-shadow">
                                                <SelectValue :placeholder="t('Select type')" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem v-for="option in props.medicationTypes" :key="option.value" :value="option.value">
                                                        {{ t(option.label) }}
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                        <div v-if="form.errors.type" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors.type }}
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="dosage" class="font-medium">
                                            {{ t('Dosage') }}
                                        </Label>
                                        <Input
                                            id="dosage"
                                            v-model="form.dosage"
                                            type="text"
                                            class="mt-1 block w-full transition-shadow"
                                            :placeholder="t('e.g., 10mg, 1 tablet')"
                                        />
                                        <div v-if="form.errors.dosage" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors.dosage }}
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="strength" class="font-medium">
                                            {{ t('Strength') }}
                                        </Label>
                                        <Input
                                            id="strength"
                                            v-model="form.strength"
                                            type="text"
                                            class="mt-1 block w-full transition-shadow"
                                            :placeholder="t('e.g., 500mg, 250mg/5ml')"
                                        />
                                        <div v-if="form.errors.strength" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors.strength }}
                                        </div>
                                    </div>
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="quantity" class="font-medium">
                                            {{ t('Quantity(for inventory)') }}
                                        </Label>
                                        <Input
                                            id="quantity"
                                            v-model="form.quantity"
                                            type="number"
                                            min="0"
                                            class="mt-1 block w-full transition-shadow"
                                            :placeholder="t('e.g., 30')"
                                        />
                                        <div v-if="form.errors.quantity" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors.quantity }}
                                        </div>
                                    </div>
                                    <div class="space-y-2 sm:col-span-2">
                                        <Label for="instructions" class="font-medium">
                                            {{ t('Instructions') }}
                                        </Label>
                                        <Textarea
                                            id="instructions"
                                            v-model="form.instructions"
                                            class="mt-1 block min-h-[100px] w-full transition-shadow"
                                            :placeholder="t('e.g., Take with food, avoid dairy')"
                                        />
                                        <div v-if="form.errors.instructions" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors.instructions }}
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <hr class="border-border/60" />

                            <section class="space-y-6" aria-labelledby="schedules-heading">
                                <div class="flex items-center justify-between">
                                    <h3 id="schedules-heading" class="text-primary dark:text-remedi-light-blue flex items-center text-lg font-medium">
                                        <ClockIcon class="text-primary mr-2 h-5 w-5" />
                                        {{ t('Schedules') }} <span class="text-destructive">*</span>
                                    </h3>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="addSchedule"
                                        size="sm"
                                        class="border-remedi-mint-green text-remedi-mint-green hover:bg-remedi-mint-green/10 dark:border-remedi-mint-green/50 dark:hover:bg-remedi-mint-green/20 dark:text-remedi-mint-green cursor-pointer transition-colors duration-200 dark:hover:text-white"
                                    >
                                        <PlusCircleIcon class="mr-1.5 h-4 w-4" />
                                        {{ t('Add') }}
                                    </Button>
                                </div>
                                <div v-if="form.errors.schedules" class="text-destructive text-xs">
                                    {{ form.errors.schedules }}
                                </div>

                                <!--Tarjeta individual para cada horario con sombra y transición-->
                                <div
                                    v-for="(schedule, schedIdx) in form.schedules"
                                    :key="schedIdx"
                                    class="border-border/70 bg-background relative space-y-5 rounded-lg border p-5 shadow-md transition-shadow duration-300 hover:shadow-lg"
                                >
                                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <Label :for="`time_to_take_${schedIdx}`" class="text-sm font-medium">
                                                {{ t('Time') }} <span class="text-destructive">*</span>
                                            </Label>
                                            <Input
                                                :id="`time_to_take_${schedIdx}`"
                                                v-model="schedule.time_to_take"
                                                type="time"
                                                class="mt-1 block w-full transition-shadow"
                                                required
                                            />
                                            <div v-if="form.errors[`schedules.${schedIdx}.time_to_take`]" class="text-destructive mt-1.5 text-xs">
                                                {{ form.errors[`schedules.${schedIdx}.time_to_take`] }}
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <Label :for="`frequency_type_${schedIdx}`" class="text-sm font-medium">
                                                {{ t('Frequency') }} <span class="text-destructive">*</span>
                                            </Label>
                                            <Select v-model="schedule.frequency_type" required>
                                                <SelectTrigger :id="`frequency_type_${schedIdx}`" class="mt-1 w-full transition-shadow">
                                                    <SelectValue :placeholder="t('Select frequency')" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <SelectItem
                                                            v-for="option in props.frequencyOptions"
                                                            :key="option.value"
                                                            :value="option.value"
                                                        >
                                                            {{ t(option.label) }}
                                                        </SelectItem>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                            <div v-if="form.errors[`schedules.${schedIdx}.frequency_type`]" class="text-destructive mt-1.5 text-xs">
                                                {{ form.errors[`schedules.${schedIdx}.frequency_type`] }}
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="schedule.frequency_type === MedicationScheduleFrequencyEnum.SPECIFIC_DAYS"
                                        class="animate-in fade-in duration-300space-y-2 space-y-2 pt-2"
                                    >
                                        <Label class="text-sm font-medium">
                                            {{ t('Days of the Week') }} <span class="text-destructive">*</span>
                                        </Label>
                                        <div class="flex flex-wrap gap-x-5 gap-y-3">
                                            <div v-for="day in daysOfWeekOptions" :key="day.id" class="flex items-center space-x-2.5">
                                                <Checkbox
                                                    :id="`day_${schedIdx}_${day.id}`"
                                                    :modelValue="isDaySelected(schedIdx, day.value)"
                                                    @update:modelValue="() => toggleDay(schedIdx, day.value)"
                                                    class="transition-transform hover:scale-110 active:scale-95"
                                                />
                                                <Label :for="`day_${schedIdx}_${day.id}`" class="cursor-pointer text-sm font-normal">
                                                    {{ day.label }}
                                                </Label>
                                            </div>
                                        </div>
                                        <div v-if="form.errors[`schedules.${schedIdx}.days_of_week`]" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors[`schedules.${schedIdx}.days_of_week`] }}
                                        </div>
                                    </div>

                                    <div
                                        v-if="schedule.frequency_type === MedicationScheduleFrequencyEnum.INTERVAL_IN_DAYS"
                                        class="animate-in fade-in duration-300space-y-2 pt-2"
                                    >
                                        <Label :for="`interval_in_days_${schedIdx}`" class="text-sm font-medium">
                                            {{ t('Every X Days') }} <span class="text-destructive">*</span>
                                        </Label>
                                        <Input
                                            :id="`interval_in_days_${schedIdx}`"
                                            v-model.number="schedule.interval_in_days"
                                            type="number"
                                            min="1"
                                            class="mt-1 block w-full transition-shadow"
                                            :placeholder="t('e.g., 3')"
                                        />
                                        <div v-if="form.errors[`schedules.${schedIdx}.interval_in_days`]" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors[`schedules.${schedIdx}.interval_in_days`] }}
                                        </div>
                                    </div>

                                    <div
                                        v-if="schedule.frequency_type === MedicationScheduleFrequencyEnum.HOURLY_INTERVAL"
                                        class="animate-in fade-in duration-300space-y-2 pt-2"
                                    >
                                        <Label :for="`interval_in_hours_${schedIdx}`" class="text-sm font-medium">
                                            {{ t('Every X Hours') }} <span class="text-destructive">*</span>
                                        </Label>
                                        <Input
                                            :id="`interval_in_hours_${schedIdx}`"
                                            v-model.number="schedule.interval_in_hours"
                                            type="number"
                                            min="1"
                                            class="mt-1 block w-full transition-shadow"
                                            :placeholder="t('e.g., 8')"
                                        />
                                        <div v-if="form.errors[`schedules.${schedIdx}.interval_in_hours`]" class="text-destructive mt-1.5 text-xs">
                                            {{ form.errors[`schedules.${schedIdx}.interval_in_hours`] }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 pt-2 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <Label :for="`start_date_${schedIdx}`" class="flex items-center text-sm font-medium">
                                                <CalendarIcon class="mr-1.5 h-4 w-4 text-slate-500" />
                                                {{ t('Start Date') }} <span class="text-destructive ml-1">*</span>
                                            </Label>
                                            <Input
                                                :id="`start_date_${schedIdx}`"
                                                v-model="schedule.start_date"
                                                type="date"
                                                class="mt-1 block w-full transition-shadow"
                                                required
                                            />
                                            <div v-if="form.errors[`schedules.${schedIdx}.start_date`]" class="text-destructive mt-1.5 text-xs">
                                                {{ form.errors[`schedules.${schedIdx}.start_date`] }}
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <Label :for="`end_date_${schedIdx}`" class="flex items-center text-sm font-medium">
                                                <CalendarIcon class="mr-1.5 h-4 w-4 text-slate-500" />
                                                {{ t('End Date') }} <span class="ml-1 text-slate-500">({{ t('Optional') }})</span>
                                            </Label>
                                            <Input
                                                :id="`end_date_${schedIdx}`"
                                                v-model="schedule.end_date"
                                                type="date"
                                                class="mt-1 block w-full transition-shadow"
                                            />
                                            <div v-if="form.errors[`schedules.${schedIdx}.end_date`]" class="text-destructive mt-1.5 text-xs">
                                                {{ form.errors[`schedules.${schedIdx}.end_date`] }}
                                            </div>
                                        </div>
                                    </div>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        @click="removeSchedule(schedIdx)"
                                        v-if="form.schedules.length > 1"
                                        class="text-muted-foreground hover:text-destructive hover:bg-destructive/10 absolute top-3 right-3 h-auto rounded-full p-1.5 transition-all duration-200 hover:scale-110 active:scale-90"
                                        :aria-label="t('Remove schedule')"
                                    >
                                        <Trash2Icon class="h-4 w-4" />
                                    </Button>
                                </div>
                            </section>

                            <footer class="border-border/60 flex items-center justify-center xs:justify-end xs:space-x-6 border-t pt-6 flex-col-reverse xs:flex-row gap-3">
                                <Link
                                    :href="route('medications.index')"
                                    class="text-muted-foreground hover:text-primary dark:hover:text-remedi-light-blue text-sm font-medium transition-colors duration-200"
                                >
                                    {{ t('Cancel') }}
                                </Link>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    size="lg"
                                    class="min-w-[150px] transition-all duration-200 hover:shadow-lg active:scale-[0.98] w-full xs:w-auto"
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
                                    <span v-else class="mr-2">💊</span>
                                    {{ t('Save Medication') }}
                                </Button>
                            </footer>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
