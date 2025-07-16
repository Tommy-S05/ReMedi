<script setup lang="ts">
import type { ReminderForToday } from '@/types';
import { useTranslations } from '@/composables/useTranslations';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { CheckCircle2Icon, XCircleIcon, CircleIcon } from 'lucide-vue-next';

interface Props {
    reminders: ReminderForToday[];
}

defineProps<Props>();

const { t } = useTranslations();

/**
 * Maps a reminder status to a visual representation (icon and class).
 */
const statusVisuals = (status: 'taken' | 'skipped' | null) => {
    switch (status) {
        case 'taken':
            return { icon: CheckCircle2Icon, class: 'text-green-500' };
        case 'skipped':
            return { icon: XCircleIcon, class: 'text-yellow-500' };
        default:
            return { icon: CircleIcon, class: 'text-muted-foreground/50' };
    }
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ t("Today's Reminders") }}</CardTitle>
            <CardDescription>
                {{ t('This is your medication schedule for the rest of today.') }}
            </CardDescription>
        </CardHeader>
        <CardContent>
            <div v-if="reminders.length === 0" class="text-muted-foreground py-10 text-center">
                <CheckCircle2Icon class="mx-auto h-12 w-12 text-green-500" />
                <p class="mt-4 font-semibold">{{ t('All done for today!') }}</p>
                <p class="text-sm">{{ t('You have no more scheduled doses for today.') }}</p>
            </div>
            <div v-else class="space-y-4">
                <div v-for="(reminder, index) in reminders" :key="index" class="flex items-center">
                    <div class="bg-muted flex h-10 w-10 items-center justify-center rounded-full">
                        <component :is="statusVisuals(reminder.status).icon" :class="['h-5 w-5', statusVisuals(reminder.status).class]" />
                    </div>
                    <div class="ml-4 space-y-1">
                        <p class="text-sm leading-none font-medium">
                            {{ reminder.medication_name }}
                        </p>
                        <p class="text-muted-foreground text-sm">
                            {{ reminder.dosage || t('No dosage specified') }}
                        </p>
                    </div>
                    <div class="ml-auto font-medium">
                        {{ reminder.time }}
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
