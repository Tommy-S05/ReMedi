<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslations } from '@/composables/useTranslations';
import { CalendarOptions } from '@fullcalendar/core';
import esLocale from '@fullcalendar/core/locales/es';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import FullCalendar from '@fullcalendar/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const { t, currentLocale } = useTranslations();

const calendarOptions = ref<CalendarOptions>({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: '',
    },
    locale: currentLocale.value,
    locales: [esLocale], // Añadir locales disponibles
    events: {
        url: route('calendar.events'),
        failure: () => {
            toast.error(t('Error'), { description: t('Could not load calendar events.') });
        },
    },
    eventDisplay: 'list-item', // Muestra eventos como una lista con puntos
    dayMaxEvents: 3,
    eventTimeFormat: {
        hour: 'numeric',
        minute: '2-digit',
        meridiem: 'short',
    },
    fixedWeekCount: false, // Permite que el calendario ajuste la cantidad de semanas según el mes
});
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ t('Monthly Schedule') }}</CardTitle>
            <CardDescription>{{ t('View your scheduled doses for the month.') }}</CardDescription>
        </CardHeader>
        <CardContent class="remedi-calendar-wrapper">
            <FullCalendar :options="calendarOptions" />
        </CardContent>
    </Card>
</template>
