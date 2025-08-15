<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
</script>

<template>
    <svg width="800" height="600" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Main background -->
        <rect width="800" height="600" rx="24" class="svg-bg" />

        <!-- Dashboard Container -->
        <g transform="translate(40, 40)">
            <!-- Welcome Header -->
            <text x="0" y="35" font-family="Inter, sans-serif" font-size="28" font-weight="bold" class="svg-text-primary">{{ t('Dashboard') }}</text>
            <rect x="0" y="55" width="150" height="4" rx="2" class="svg-text-secondary" opacity="0.3" />

            <!-- Top Statistics Cards -->
            <g id="stats-cards" transform="translate(0, 90)">
                <!-- Card 1: Next Dose -->
                <rect width="230" height="120" rx="16" class="svg-card-bg" />
                <circle cx="30" cy="30" r="12" class="svg-accent-light" />
                <path d="M30 22V30H38" stroke="#00D2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <text x="18" y="75" font-family="Inter, sans-serif" font-size="24" font-weight="bold" class="svg-text-primary">{{ t('08:00 PM') }}</text>
                <text x="18" y="98" font-family="Inter, sans-serif" font-size="14" class="svg-text-secondary">{{ t('Next Dose') }}</text>

                <!-- Card 2: Active Medications -->
                <rect x="245" width="230" height="120" rx="16" class="svg-card-bg" />
                <circle cx="275" cy="30" r="12" class="svg-accent-light" />
                <path d="M271 30H279" stroke="#00D2B3" stroke-width="2" stroke-linecap="round" />
                <path d="M275 26V34" stroke="#00D2B3" stroke-width="2" stroke-linecap="round" />
                <text x="263" y="75" font-family="Inter, sans-serif" font-size="24" font-weight="bold" class="svg-text-primary">5</text>
                <text x="263" y="98" font-family="Inter, sans-serif" font-size="14" class="svg-text-secondary">{{ t('Active Medications') }}</text>

                <!-- Card 3: Adherence -->
                <rect x="490" width="230" height="120" rx="16" class="svg-card-bg" />
                <circle cx="520" cy="30" r="12" class="svg-accent-light" />
                <path d="M515 32L519 36L525 26" stroke="#00D2B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <text x="508" y="75" font-family="Inter, sans-serif" font-size="24" font-weight="bold" class="svg-text-primary">98%</text>
                <text x="508" y="98" font-family="Inter, sans-serif" font-size="14" class="svg-text-secondary">{{ t('Adherence (7d)') }}</text>
            </g>

            <!-- Large Bottom Card: Today's Reminders -->
            <g id="main-card" transform="translate(0, 230)">
                <rect width="720" height="290" rx="16" class="svg-card-bg" />

                <!-- Card Header -->
                <text x="25" y="40" font-family="Inter, sans-serif" font-size="18" font-weight="600" class="svg-text-primary">
                    {{ t('Today\'s Reminders') }}
                </text>
                <line x1="25" y1="60" x2="695" y2="60" stroke="#374151" stroke-width="1" />

                <!-- Reminder List (example items) -->
                <g id="reminder-list" transform="translate(25, 85)">
                    <!-- Item 1 -->
                    <circle cx="12" cy="12" r="12" class="svg-accent-light" />
                    <path d="M9.5 12.5L11.5 14.5L14.5 9.5" stroke="#00D2B3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <text x="40" y="12" font-family="Inter, sans-serif" font-size="14" font-weight="500" class="svg-text-primary">{{ t('Ibuprofeno') }}</text>
                    <text x="40" y="28" font-family="Inter, sans-serif" font-size="12" class="svg-text-secondary">200mg</text>
                    <text x="640" y="20" font-family="Inter, sans-serif" font-size="14" font-weight="500" class="svg-text-primary" text-anchor="end">
                        {{ t('08:00 AM') }}
                    </text>

                    <!-- Item 2 -->
                    <g transform="translate(0, 50)">
                        <circle cx="12" cy="12" r="12" class="svg-accent-light" />
                        <path d="M9.5 12.5L11.5 14.5L14.5 9.5" stroke="#00D2B3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <text x="40" y="12" font-family="Inter, sans-serif" font-size="14" font-weight="500" class="svg-text-primary">
                            {{ t('Paracetamol') }}
                        </text>
                        <text x="40" y="28" font-family="Inter, sans-serif" font-size="12" class="svg-text-secondary">500mg</text>
                        <text
                            x="640"
                            y="20"
                            font-family="Inter, sans-serif"
                            font-size="14"
                            font-weight="500"
                            class="svg-text-primary"
                            text-anchor="end"
                        >
                            {{ t('02:00 PM') }}
                        </text>
                    </g>

                    <!-- Item 3 (Placeholder) -->
                    <g transform="translate(0, 100)">
                        <circle cx="12" cy="12" r="12" class="svg-text-secondary" opacity="0.1" />
                        <rect x="40" y="6" width="120" height="12" rx="4" class="svg-text-secondary" opacity="0.3" />
                        <rect x="40" y="24" width="80" height="8" rx="3" class="svg-text-secondary" opacity="0.2" />
                        <rect x="580" y="12" width="60" height="12" rx="4" class="svg-text-secondary" opacity="0.3" />
                    </g>

                    <!-- Item 4 (Placeholder) -->
                    <g transform="translate(0, 150)">
                        <circle cx="12" cy="12" r="12" class="svg-text-secondary" opacity="0.1" />
                        <rect x="40" y="6" width="150" height="12" rx="4" class="svg-text-secondary" opacity="0.3" />
                        <rect x="40" y="24" width="100" height="8" rx="3" class="svg-text-secondary" opacity="0.2" />
                        <rect x="580" y="12" width="60" height="12" rx="4" class="svg-text-secondary" opacity="0.3" />
                    </g>
                </g>
            </g>
        </g>
    </svg>
</template>

<style scoped>
.svg-bg {
    fill: #111827;
} /* Dark background */
.svg-card-bg {
    fill: #1f2937;
} /* Card background */
.svg-text-primary {
    fill: #d1d5db;
} /* Primary text */
.svg-text-secondary {
    fill: #9ca3af;
} /* Secondary text */
.svg-accent {
    fill: #00d2b3;
} /* Primary brand color */
.svg-accent-light {
    fill: rgba(0, 210, 179, 0.2);
} /* Lighter accent for backgrounds */

@media (prefers-color-scheme: light) {
    .svg-bg {
        fill: #f9fafb;
    }
    .svg-card-bg {
        fill: #ffffff;
    }
    .svg-text-primary {
        fill: #1f2937;
    }
    .svg-text-secondary {
        fill: #6b7280;
    }
}
</style>
