<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslations } from '@/composables/useTranslations';
import type { DashboardStats } from '@/types';
import { AlarmClockIcon, PillIcon, TargetIcon } from 'lucide-vue-next';

interface Props {
    stats: DashboardStats;
}

defineProps<Props>();

const { t } = useTranslations();
</script>

<template>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">
                    {{ t('Next Dose') }}
                </CardTitle>
                <AlarmClockIcon class="text-muted-foreground h-4 w-4" />
            </CardHeader>
            <CardContent>
                <div v-if="stats.nextDose" class="text-2xl font-bold">
                    {{ stats.nextDose.time }}
                </div>
                <p v-if="stats.nextDose" class="text-muted-foreground text-xs">
                    {{ stats.nextDose.medication_name }}
                </p>
                <div v-else class="text-muted-foreground pt-2 text-lg font-semibold">
                    {{ t('No upcoming doses') }}
                </div>
            </CardContent>
        </Card>
        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">
                    {{ t('Active Medications') }}
                </CardTitle>
                <PillIcon class="text-muted-foreground h-4 w-4" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">
                    {{ stats.activeMedicationsCount }}
                </div>
                <p class="text-muted-foreground text-xs">
                    {{ t('currently in your list') }}
                </p>
            </CardContent>
        </Card>
        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">
                    {{ t('Adherence (7 days)') }}
                </CardTitle>
                <TargetIcon class="text-muted-foreground h-4 w-4" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ stats.adherencePercentage }}%</div>
                <p class="text-muted-foreground text-xs">
                    {{ t('of doses taken correctly') }}
                </p>
            </CardContent>
        </Card>
    </div>
</template>
