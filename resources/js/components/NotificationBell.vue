<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useTranslations } from '@/composables/useTranslations';
import axios from 'axios'; // Usaremos axios para las peticiones API
import { BellIcon, CheckCircle2Icon, PillIcon, XCircleIcon } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';
import { toast } from 'vue-sonner';

/**
 * Interface para la data específica de una notificación de recordatorio.
 */
interface MedicationReminderNotificationData {
    medication_id: number;
    medication_name: string;
    dosage: string | null;
    message_key: string;
    message_params: Record<string, string>;
    // ... otros campos de toArray()
}

/**
 * Interface para una notificación de la base de datos de Laravel.
 */
interface DatabaseNotification {
    id: string; // Es un UUID
    type: string; // ej. 'App\\Notifications\\MedicationReminderNotification'
    data: MedicationReminderNotificationData;
    read_at: string | null;
    created_at: string;
}

const { t } = useTranslations();
const notifications = ref<DatabaseNotification[]>([]);
const totalUnread = ref(0);
const isLoading = ref(false);
let pollingInterval: number | null = null;

/**
 * @description Obtiene las notificaciones no leídas del servidor.
 * @returns {Promise<void>}
 */
const fetchNotifications = async (): Promise<void> => {
    // Evitar múltiples peticiones simultáneas
    if (isLoading.value) return;
    isLoading.value = true;

    try {
        const response = await axios.get<{
            success: boolean;
            data: { notifications: DatabaseNotification[]; total_unread: number };
        }>(route('notifications.index'));

        if (response.data.success) {
            notifications.value = response.data.data.notifications;
            totalUnread.value = response.data.data.total_unread;
        }
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    } finally {
        isLoading.value = false;
    }
};

/**
 * @description Maneja la acción del usuario sobre un recordatorio (Tomado/Omitido).
 * @param {'taken' | 'skipped'} status - El estado a registrar.
 * @param {string} notificationId - El ID de la notificación.
 */
const handleTakeAction = async (status: 'taken' | 'skipped', notificationId: string) => {
    try {
        const response = await axios.post(route('medication-logs.store'), {
            notification_id: notificationId,
            status: status,
        });

        if (response.data.success) {
            toast.success(t('Action Recorded'), {
                description: response.data.message || t('Your action has been logged successfully.'),
            });
            // Remover la notificación de la lista local para feedback inmediato
            notifications.value = notifications.value.filter((n) => n.id !== notificationId);
            totalUnread.value = Math.max(0, totalUnread.value - 1);
        } else {
            toast.error(t('Action Failed'), {
                description: response.data.message,
            });
        }
    } catch (error: any) {
        console.error(`Failed to record action for notification ${notificationId}:`, error);
        toast.error(t('Action Failed'), {
            description: error.response?.data?.message || t('An unexpected error occurred.'),
        });
    }
};

/**
 * @description Marca todas las notificaciones como leídas.
 */
const markAllAsRead = async () => {
    if (totalUnread.value === 0) return;
    try {
        await axios.post(route('notifications.markAllAsRead'));
        notifications.value = [];
        totalUnread.value = 0;
        toast.info(t('All notifications marked as read.'));
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error);
        toast.error(t('Error'), { description: t('Could not mark all as read.') });
    }
};

// Cargar notificaciones al montar el componente
onMounted(() => {
    fetchNotifications();
    // Iniciar polling para refrescar las notificaciones cada 60 segundos
    pollingInterval = window.setInterval(fetchNotifications, 60000);
});

// Limpiar el intervalo al desmontar el componente para evitar memory leaks
onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="text-muted-foreground hover:text-foreground relative h-9 w-9">
                <BellIcon class="h-5 w-5" />
                <span class="sr-only">{{ t('Notifications') }}</span>
                <div
                    v-if="totalUnread > 0"
                    class="bg-remedi-mint-green absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full text-[10px] font-bold text-white"
                >
                    {{ totalUnread > 9 ? '9+' : totalUnread }}
                </div>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-80 md:w-96">
            <DropdownMenuLabel class="flex items-center justify-between">
                <span>{{ t('Notifications') }}</span>
                <Button variant="link" class="h-auto p-0 text-xs" @click.prevent="markAllAsRead" :disabled="totalUnread === 0">
                    {{ t('Mark all as read') }}
                </Button>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />

            <div v-if="isLoading && notifications.length === 0" class="text-muted-foreground p-4 text-center text-sm">
                {{ t('Loading...') }}
            </div>
            <div v-else-if="notifications.length === 0" class="text-muted-foreground p-4 text-center text-sm">
                {{ t('You have no new notifications.') }}
            </div>

            <div v-else class="max-h-96 overflow-y-auto">
                <template v-for="(notification, index) in notifications" :key="notification.id">
                    <DropdownMenuItem class="hover:bg-muted/50 focus:bg-muted/50 focus:dark:text-white flex flex-col items-start gap-2 p-3">
                        <div class="w-full">
                            <div class="flex items-start gap-3">
                                <div class="pt-1">
                                    <PillIcon class="text-secondary h-5 w-5" />
                                </div>
                                <div class="flex-grow">
                                    <p class="leading-tight font-semibold">{{ notification.data.medication_name }}</p>
                                    <p class="text-muted-foreground text-xs">{{ notification.data.dosage }}</p>
                                    <p class="mt-1 text-sm">
                                        {{ t(notification.data.message_key, notification.data.message_params) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 flex w-full justify-end gap-2">
                                <Button @click.stop="handleTakeAction('skipped', notification.id)" variant="outline" size="sm" class="h-8 text-xs dark:hover:text-white hover:bg-muted-foreground/20 dark:hover:bg-muted-foreground/20">
                                    <XCircleIcon class="mr-1.5 h-4 w-4" />
                                    {{ t('Skip') }}
                                </Button>
                                <Button
                                    @click.stop="handleTakeAction('taken', notification.id)"
                                    variant="default"
                                    size="sm"
                                    class="bg-remedi-mint-green hover:bg-remedi-mint-green/80 dark:hover:bg-remedi-mint-green/80 h-8 text-xs text-white"
                                >
                                    <CheckCircle2Icon class="mr-1.5 h-4 w-4 text-white" />
                                    {{ t('Mark as Taken') }}
                                </Button>
                            </div>
                        </div>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator v-if="index < notifications.length - 1" class="mx-0 dark:text-red-400" />
                </template>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
