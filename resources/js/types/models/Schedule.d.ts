import type { MedicationScheduleFrequencyEnum } from '@/Enums/MedicationScheduleFrequencyEnum';

/**
 * Interface para la estructura de un Horario de Medicamento (Medication Schedule)
 * tal como se recibe del backend y se usa en el frontend.
 */
export interface Schedule {
    id: number;
    medication_id: number;
    time_to_take: string;
    frequency_type: MedicationScheduleFrequencyEnum;
    frequency_type_label?: string;
    days_of_week?: number[] | null;
    interval_in_days?: number | null;
    interval_in_hours?: number | null;
    start_date: string;
    end_date?: string | null;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
}
