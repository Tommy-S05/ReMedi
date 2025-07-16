import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';
// Re-exportar tipos de modelos para un único punto de importación
export * from './models/Medication';
export * from './models/Prescription';
export * from './models/Schedule';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    flash: FlashMessages;
    translations: Record<string, string>;
    current_locale: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    timezone?: string | null;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

/**
 * Defines the structure for flash messages passed from the backend.
 * Each property is optional as not all responses will have all types of messages.
 */
export interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

export interface PaginationLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: { url: string | null; label: string; active: boolean }[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

/**
 * Interface for the statistics cards on the dashboard.
 */
export interface DashboardStats {
    nextDose: {
        medication_name: string;
        time: string;
    } | null;
    activeMedicationsCount: number;
    adherencePercentage: number;
}

/**
 * Interface for a single reminder item in the "Today's Reminders" list.
 */
export interface ReminderForToday {
    medication_name: string;
    dosage: string | null;
    time: string; // e.g., "09:00 AM"
    is_past: boolean;
    status: 'taken' | 'skipped' | null;
}

export type BreadcrumbItemType = BreadcrumbItem;
