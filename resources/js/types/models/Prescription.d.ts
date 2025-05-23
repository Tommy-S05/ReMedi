import type { Medication } from './Medication';

/**
 * Representa los datos pivot cuando un medicamento está asociado a una prescripción.
 */
export interface MedicationPivot {
    dosage_on_prescription?: string | null;
    quantity_prescribed?: string | null;
    instructions_on_prescription?: string | null;
}

/**
 * Representa un medicamento tal como se recibe dentro de una prescripción.
 * Extiende la interfaz Medication e incluye los datos pivot.
 */
export interface PrescribedMedication extends Medication {
    pivot: MedicationPivot;
}

/**
 * Interface para la estructura de una Prescripción (Receta Médica)
 * tal como se recibe del backend y se usa en el frontend.
 */
export interface Prescription {
    id: number;
    user_id: number;
    title?: string | null;
    doctor_name?: string | null;
    prescription_date?: string | null;
    prescription_date_formatted?: string | null;
    notes?: string | null;
    created_at: string;
    updated_at: string;
    medications: PrescribedMedication[];
}
