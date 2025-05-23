import type { MedicationTypeEnum } from '@/Enums/MedicationTypeEnum';
import type { Schedule } from './Schedule';

/**
 * Interface para la estructura de un Medicamento (Medication)
 * tal como se recibe del backend y se usa en el frontend.
 */
export interface Medication {
    id: number;
    user_id: number;
    name: string;
    type: MedicationTypeEnum | null;
    type_label?: string | null;
    dosage: string | null;
    strength: string | null;
    quantity: number | null;
    instructions: string | null;
    created_at: string;
    updated_at: string;
    schedules: Schedule[];
}
