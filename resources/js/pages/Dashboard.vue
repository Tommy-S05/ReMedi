<script setup lang="ts">
import DashboardCalendarBackup from '@/components/dashboard/DashboardCalendarBackup.vue';
import DashboardStatsComponent from '@/components/dashboard/DashboardStats.vue';
import TodaysReminders from '@/components/dashboard/TodaysReminders.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useTranslations } from '@/composables/useTranslations';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, DashboardStats, ReminderForToday, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useTranslations();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Dashboard'),
        href: route('dashboard'),
    },
];

/**
 * Props received from the DashboardController.
 */
interface Props {
    stats: DashboardStats;
    remindersForToday: ReminderForToday[];
}

const props = defineProps<Props>();

const user = usePage<SharedData>().props.auth.user;

const welcomeMessage = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) {
        return t('Good morning');
    }
    if (hour < 18) {
        return t('Good afternoon');
    }
    return t('Good evening');
});
</script>

<template>
    <Head :title="t('Dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-4 p-4 pt-6 md:p-8">
            <div class="flex items-center justify-between space-y-2">
                <h2 class="text-3xl font-bold tracking-tight">{{ welcomeMessage }}, {{ user?.name }}!</h2>
            </div>

            <!-- Componente de Estadísticas -->
            <DashboardStatsComponent :stats="props.stats" />

            <!-- Pestañas para Hoy y Calendario -->
            <Tabs default-value="today" class="space-y-4">
                <TabsList>
                    <TabsTrigger value="today">
                        {{ t('Today') }}
                    </TabsTrigger>
                    <TabsTrigger value="calendar">
                        {{ t('Calendar') }}
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="today" class="space-y-4">
                    <TodaysReminders :reminders="props.remindersForToday" />
                </TabsContent>
                <TabsContent value="calendar" class="space-y-4">
                    <DashboardCalendarBackup />
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
