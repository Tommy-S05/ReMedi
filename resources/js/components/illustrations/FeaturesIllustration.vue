<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import { computed } from 'vue';

defineOptions({ inheritAttrs: false });

type Fit = 'contain' | 'cover' | 'none';

const { t } = useTranslations();

const props = defineProps<{
    /** Dark mode for internal background/stroke colors already used by the art */
    isDark?: boolean;
    /** Feature selection to display (preserve original union) */
    feature: 'bell' | 'chart' | 'users' | 'pills' | 'calendar' | 'dashboard' | 'inventory' | 'reports' | 'sharing' | 'inventory-management';
    /** If true, the SVG occupies 100% of the container */
    responsive?: boolean;
    /** Specific width (takes precedence over size) */
    width?: number | string;
    /** Specific height (takes precedence over size) */
    height?: number | string;
    /** Shortcut for square size (applies to width/height if not defined) */
    size?: number | string;
    /** Art adjustment within the viewBox */
    wrapperFit?: Fit;
    /** Accessibility */
    title?: string;
    desc?: string;
    ariaHidden?: boolean;
    /** Colors/style */
    color?: string;
    stroke?: string;
    fill?: string;
    strokeWidth?: number | string;
    /** Extra classes to combine with $attrs.class */
    extraClass?: string;
    /** Optional animation */
    spin?: boolean;
}>();

const isDark = computed(() => props.isDark ?? false);

const preserveAspectRatio = computed(() => {
    switch (props.wrapperFit) {
        case 'cover':
            return 'xMidYMid slice';
        case 'none':
            return 'none';
        default:
            return 'xMidYMid meet';
    }
});

const widthAttr = computed(() => {
    if (props.responsive) return '100%';
    if (props.width != null) return String(props.width);
    if (props.size != null) return String(props.size);
    return undefined;
});

const heightAttr = computed(() => {
    if (props.responsive) return '100%';
    if (props.height != null) return String(props.height);
    if (props.size != null) return String(props.size);
    return undefined;
});

const styleColor = computed(() => (props.color ? { color: props.color } : undefined));

const titleId = `icon-title-` + Math.random().toString(36).slice(2);
const ariaLabelledBy = computed(() => (props.title && !props.ariaHidden ? titleId : undefined));
const ariaHidden = computed(() => (props.ariaHidden ? 'true' : undefined));

const svgClasses = computed(() => {
    const base = props.spin ? 'inline-block align-middle animate-spin' : 'inline-block align-middle';
    // Combinar extraClass con cualquier class que venga en $attrs.class se hará en el template
    return [base, props.extraClass].filter(Boolean).join(' ');
});

const connectedUsers = [
    { x: 120, y: 80, color: '#f59e0b' },
    { x: 280, y: 80, color: '#3b82f6' },
    { x: 120, y: 180, color: '#ef4444' },
];

const pillPositions = [
    { x: 120, y: 200, color: '#ef4444' },
    { x: 140, y: 210, color: '#3b82f6' },
    { x: 160, y: 195, color: '#f59e0b' },
    { x: 240, y: 200, color: '#10b981' },
    { x: 260, y: 190, color: '#8b5cf6' },
];

const calendarDays = [
    { number: 1, active: false },
    { number: 2, active: true },
    { number: 3, active: false },
    { number: 4, active: false },
    { number: 5, active: true },
    { number: 6, active: false },
    { number: 7, active: false },
    { number: 8, active: false },
    { number: 9, active: false },
    { number: 10, active: true },
    { number: 11, active: false },
    { number: 12, active: false },
    { number: 13, active: false },
    { number: 14, active: false },
];

const inventoryBottles = [
    { x: 100, y: 90, color: '#ef4444' },
    { x: 140, y: 95, color: '#3b82f6' },
    { x: 180, y: 90, color: '#f59e0b' },
    { x: 220, y: 95, color: '#10b981' },
    { x: 260, y: 90, color: '#8b5cf6' },
    { x: 120, y: 140, color: '#06b6d4' },
    { x: 160, y: 145, color: '#84cc16' },
    { x: 200, y: 140, color: '#f97316' },
    { x: 240, y: 145, color: '#ec4899' },
];

const reportDataPoints = [
    { x: 130, y: 140 },
    { x: 150, y: 145 },
    { x: 170, y: 125 },
    { x: 190, y: 130 },
    { x: 210, y: 115 },
    { x: 230, y: 120 },
    { x: 250, y: 105 },
    { x: 270, y: 110 },
    { x: 290, y: 95 },
];

const secureConnections = [
    { x: 120, y: 80, color: '#f59e0b' },
    { x: 280, y: 80, color: '#3b82f6' },
    { x: 120, y: 180, color: '#ef4444' },
];

const smartInventoryBottles = [
    { x: 90, y: 75, color: '#ef4444', count: '12', lowStock: false, textColor: '#ef4444' },
    { x: 130, y: 80, color: '#3b82f6', count: '8', lowStock: false, textColor: '#3b82f6' },
    { x: 170, y: 75, color: '#f59e0b', count: '3', lowStock: true, textColor: '#f59e0b' },
    { x: 210, y: 80, color: '#10b981', count: '15', lowStock: false, textColor: '#10b981' },
    { x: 250, y: 75, color: '#8b5cf6', count: '6', lowStock: false, textColor: '#8b5cf6' },
    { x: 110, y: 135, color: '#06b6d4', count: '2', lowStock: true, textColor: '#06b6d4' },
    { x: 150, y: 140, color: '#84cc16', count: '20', lowStock: false, textColor: '#84cc16' },
    { x: 190, y: 135, color: '#f97316', count: '9', lowStock: false, textColor: '#f97316' },
];

const medicationProgress = [
    { name: 'Aspirin', progress: 18, color: '#ef4444' },
    { name: 'Vitamin D', progress: 45, color: '#3b82f6' },
    { name: 'Omega 3', progress: 52, color: '#10b981' },
];
</script>

<template>
    <svg
        viewBox="0 0 400 300"
        :preserveAspectRatio="preserveAspectRatio"
        :width="widthAttr"
        :height="heightAttr"
        :style="[styleColor, { fontFamily: 'var(--font-sans)' }]"
        :class="[svgClasses, $attrs.class]"
        role="img"
        :aria-labelledby="ariaLabelledBy"
        :aria-hidden="ariaHidden"
        v-bind="Object.fromEntries(Object.entries($attrs).filter(([k]) => k !== 'class'))"
        :fill="props.fill ?? undefined"
        :stroke="props.stroke ?? undefined"
        :stroke-width="props.strokeWidth ?? undefined"
    >
        <template v-if="props.title && !props.ariaHidden">
            <title :id="titleId">{ props.title }</title>
            <desc v-if="props.desc">{ props.desc }</desc>
        </template>

        <!-- Background -->
        <rect width="400" height="300" :fill="isDark ? '#0f172a' : '#ffffff'" rx="16" />

        <!-- Feature 1: Bell/Notification (Reminders) -->
        <g v-if="feature === 'bell'">
            <!-- Phone outline -->
            <rect
                x="150"
                y="50"
                width="100"
                height="180"
                :fill="isDark ? '#1e293b' : '#f8fafc'"
                rx="20"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="2"
            />
            <rect x="160" y="70" width="80" height="140" :fill="isDark ? '#0f172a' : '#ffffff'" rx="8" />

            <!-- Notification -->
            <rect
                x="120"
                y="80"
                width="160"
                height="50"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="12"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
                filter="drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1))"
            />

            <!-- Bell icon -->
            <g transform="translate(140, 95)">
                <path d="M10 5 C10 2.24 12.24 0 15 0 C17.76 0 20 2.24 20 5 C20 8 22 10 22 13 L8 13 C8 10 10 8 10 5 Z" fill="#00d2b3" />
                <path
                    d="M13 17 C13 18.1 13.9 19 15 19 C16.1 19 17 18.1 17 17"
                    :stroke="isDark ? '#e2e8f0' : '#1e293b'"
                    stroke-width="1.5"
                    fill="none"
                    stroke-linecap="round"
                />
            </g>

            <!-- Notification text -->
            <text x="170" y="100" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="600">
                {{ t('Reminder') }}
            </text>
            <text x="170" y="115" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">{{ t('Aspirin - 8:00 AM') }}</text>

            <!-- Time slots -->
            <g v-for="(time, index) in ['8:00', '16:00', '00:00']" :key="index">
                <rect :x="170 + index * 25" y="160" width="20" height="15" fill="#00d2b3" rx="3" />
                <text :x="180 + index * 25" y="170" fill="white" font-size="6" text-anchor="middle">{{ t(time) }}</text>
            </g>

            <!-- Animated pulse -->
            <circle cx="200" cy="105" r="8" fill="#00d2b3" opacity="0.3">
                <animate attributeName="r" values="8;12;8" dur="2s" repeatCount="indefinite" />
                <animate attributeName="opacity" values="0.3;0;0.3" dur="2s" repeatCount="indefinite" />
            </circle>
        </g>

        <!-- Feature 2: Chart/Analytics (Seguimiento de Adherencia) -->
        <g v-if="feature === 'chart'">
            <!-- Dashboard background -->
            <rect x="50" y="50" width="300" height="200" :fill="isDark ? '#1e293b' : '#f8fafc'" rx="12" />

            <!-- Chart area -->
            <rect x="70" y="80" width="260" height="120" :fill="isDark ? '#334155' : '#ffffff'" rx="8" />

            <!-- Chart bars -->
            <g v-for="(height, index) in [40, 60, 35, 70, 45, 80, 55]" :key="index">
                <rect :x="90 + index * 30" :y="200 - height" width="20" :height="height" fill="#00d2b3" rx="2" />
            </g>

            <!-- Chart title -->
            <text x="200" y="75" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="600" text-anchor="middle">
                {{ t('Adherence Weekly') }}
            </text>

            <!-- Stats -->
            <text x="90" y="230" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="14" font-weight="700">94%</text>
            <text x="90" y="245" :fill="isDark ? '#64748b' : '#64748b'" font-size="10">{{ t('Adherence Average') }}</text>

            <text x="200" y="230" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="14" font-weight="700">28/30</text>
            <text x="200" y="245" :fill="isDark ? '#64748b' : '#64748b'" font-size="10">{{ t('Doses Taken') }}</text>

            <!-- Calendar icon -->
            <rect x="280" y="220" width="30" height="25" :fill="isDark ? '#475569' : '#e2e8f0'" rx="4" />
            <rect x="285" y="225" width="20" height="15" :fill="isDark ? '#334155' : '#ffffff'" rx="2" />
            <circle cx="290" cy="232" r="2" fill="#00d2b3" />
            <circle cx="295" cy="232" r="2" fill="#00d2b3" />
            <circle cx="300" cy="232" r="2" :fill="isDark ? '#64748b' : '#cbd5e1'" />
        </g>

        <!-- Feature 3: Users/Sharing (Cuidadores) -->
        <g v-if="feature === 'users'">
            <!-- Main user -->
            <circle cx="200" cy="120" r="30" fill="#00d2b3" />
            <circle cx="200" cy="110" r="12" fill="white" />
            <path d="M185 135 Q200 125 215 135" fill="white" />

            <!-- Connected users -->
            <g v-for="(user, index) in connectedUsers" :key="index">
                <circle :cx="user.x" :cy="user.y" r="20" :fill="user.color" />
                <circle :cx="user.x" :cy="user.y - 5" r="8" fill="white" />
                <path :d="`M${user.x - 10} ${user.y + 8} Q${user.x} ${user.y + 2} ${user.x + 10} ${user.y + 8}`" fill="white" />

                <!-- Connection line -->
                <line
                    :x1="200"
                    :y1="120"
                    :x2="user.x"
                    :y2="user.y"
                    :stroke="isDark ? '#475569' : '#cbd5e1'"
                    stroke-width="2"
                    stroke-dasharray="5,5"
                />
            </g>

            <!-- Labels -->
            <text x="200" y="170" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="600" text-anchor="middle">{{ t('You') }}</text>
            <text x="120" y="90" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">{{ t('Family') }}</text>
            <text x="280" y="90" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">{{ t('Caregiver') }}</text>
            <text x="120" y="190" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">{{ t('Doctor') }}</text>

            <!-- Sharing indicator -->
            <rect x="160" y="200" width="80" height="25" :fill="isDark ? '#334155' : '#f1f5f9'" rx="12" />
            <circle cx="175" cy="212" r="4" fill="#00d2b3" />
            <text x="185" y="216" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="8">{{ t('Sharing') }}</text>
        </g>

        <!-- Feature 4: Medication/Pills (Registro de Medicamentos) -->
        <g v-if="feature === 'pills'">
            <!-- Medicine bottle -->
            <rect
                x="150"
                y="80"
                width="60"
                height="100"
                :fill="isDark ? '#1e293b' : '#f8fafc'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="2"
            />
            <rect x="155" y="70" width="50" height="20" :fill="isDark ? '#475569' : '#e2e8f0'" rx="4" />

            <!-- Label on bottle -->
            <rect x="160" y="100" width="40" height="30" :fill="isDark ? '#334155' : '#ffffff'" rx="4" />
            <text x="180" y="115" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="8" font-weight="600" text-anchor="middle">
                {{ t('Aspirin') }}
            </text>
            <text x="180" y="125" :fill="isDark ? '#64748b' : '#64748b'" font-size="6" text-anchor="middle">100mg</text>

            <!-- Pills scattered -->
            <g v-for="(pill, index) in pillPositions" :key="index">
                <ellipse :cx="pill.x" :cy="pill.y" rx="8" ry="6" :fill="pill.color" />
                <ellipse :cx="pill.x" :cy="pill.y - 1" rx="6" ry="4" fill="white" opacity="0.3" />
            </g>

            <!-- Plus icon for adding -->
            <circle cx="300" cy="100" r="25" fill="#00d2b3" />
            <line x1="290" y1="100" x2="310" y2="100" stroke="white" stroke-width="3" stroke-linecap="round" />
            <line x1="300" y1="90" x2="300" y2="110" stroke="white" stroke-width="3" stroke-linecap="round" />

            <!-- Text -->
            <text x="200" y="220" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="600" text-anchor="middle">
                {{ t('Add Medication') }}
            </text>
        </g>

        <!-- Feature 5: Calendar/Schedule (Horarios Flexibles) -->
        <g v-if="feature === 'calendar'">
            <!-- Calendar grid -->
            <rect
                x="80"
                y="60"
                width="240"
                height="180"
                :fill="isDark ? '#1e293b' : '#f8fafc'"
                rx="12"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="2"
            />

            <!-- Calendar header -->
            <rect x="80" y="60" width="240" height="40" fill="#00d2b3" rx="12" />
            <rect x="80" y="80" width="240" height="20" fill="#00d2b3" />

            <!-- Month/Year -->
            <text x="200" y="85" fill="white" font-size="14" font-weight="600" text-anchor="middle">{{ t('March 2024') }}</text>

            <!-- Calendar days -->
            <g v-for="(day, index) in calendarDays" :key="index">
                <rect
                    :x="100 + (index % 7) * 30"
                    :y="110 + Math.floor(index / 7) * 25"
                    width="25"
                    height="20"
                    :fill="day.active ? '#00d2b3' : 'transparent'"
                    rx="4"
                />
                <text
                    :x="112 + (index % 7) * 30"
                    :y="123 + Math.floor(index / 7) * 25"
                    :fill="day.active ? 'white' : isDark ? '#e2e8f0' : '#1e293b'"
                    font-size="10"
                    text-anchor="middle"
                >
                    {{ day.number }}
                </text>
            </g>

            <!-- Time slots -->
            <rect x="100" y="200" width="200" height="30" :fill="isDark ? '#334155' : '#ffffff'" rx="8" />
            <g v-for="(time, index) in ['8:00', '14:00', '20:00']" :key="index">
                <circle :cx="130 + index * 60" cy="215" r="8" fill="#00d2b3" />
                <!--                <text :cx="130 + index * 60" :cy="230" :fill="isDark ? '#e2e8f0' : '#1e293b'"  font-size="8" text-anchor="middle">-->
                <!--                    {{ time }}-->
                <!--                </text>-->
            </g>
        </g>

        <!-- Feature 6: Dashboard (Dashboard Dinámico) -->
        <g v-if="feature === 'dashboard'">
            <!-- Main dashboard container -->
            <rect
                x="60"
                y="40"
                width="280"
                height="220"
                :fill="isDark ? '#1e293b' : '#f8fafc'"
                rx="16"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="2"
            />

            <!-- Header bar -->
            <rect x="60" y="40" width="280" height="40" fill="#00d2b3" rx="16" />
            <rect x="60" y="60" width="280" height="20" fill="#00d2b3" />

            <!-- Dashboard title -->
            <text x="200" y="65" fill="white" font-size="14" font-weight="600" text-anchor="middle">
                {{ t('Health Dashboard') }}
            </text>

            <!-- Widget 1: Próxima dosis -->
            <rect
                x="80"
                y="100"
                width="120"
                height="60"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
            />
            <circle cx="100" cy="120" r="8" fill="#00d2b3" />
            <text x="115" y="118" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="600">
                {{ t('Next Dose') }}
            </text>
            <text x="115" y="132" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">
                {{ t('Aspirin - 14:30') }}
            </text>
            <text x="115" y="145" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">
                {{ t('In 2 hours') }}
            </text>

            <!-- Widget 2: Estadísticas rápidas -->
            <rect
                x="220"
                y="100"
                width="100"
                height="60"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
            />
            <text x="270" y="118" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="700" text-anchor="middle">96%</text>
            <text x="270" y="132" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">
                {{ t('This week') }}
            </text>
            <text x="270" y="145" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">
                {{ t('Adherence') }}
            </text>

            <!-- Widget 3: Mini gráfico de progreso -->
            <rect
                x="80"
                y="180"
                width="120"
                height="60"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
            />
            <text x="140" y="198" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="9" font-weight="600" text-anchor="middle">
                {{ t('Weekly Progress') }}
            </text>

            <!-- Mini chart bars -->
            <g v-for="(height, index) in [12, 18, 10, 20, 15, 22, 16]" :key="index">
                <rect :x="90 + index * 12" :y="230 - height" width="8" :height="height" fill="#00d2b3" rx="1" />
            </g>

            <!-- Widget 4: Recordatorios activos -->
            <rect
                x="220"
                y="180"
                width="100"
                height="60"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
            />
            <circle cx="240" cy="200" r="6" fill="#f59e0b" />
            <text x="250" y="204" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="9" font-weight="600">
                {{ t('3 Actives') }}
            </text>
            <text x="270" y="220" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">
                {{ t('Reminders') }}
            </text>
            <text x="270" y="232" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">
                {{ t('Pending') }}
            </text>

            <!-- Animated data flow -->
            <g opacity="0.6">
                <circle cx="140" cy="130" r="3" fill="#00d2b3">
                    <animate attributeName="opacity" values="0.6;1;0.6" dur="2s" repeatCount="indefinite" />
                </circle>
                <circle cx="270" cy="130" r="3" fill="#00d2b3">
                    <animate attributeName="opacity" values="1;0.6;1" dur="2s" repeatCount="indefinite" />
                </circle>
            </g>
        </g>

        <!-- Feature 7: Inventory/Package (Inventario de Medicamentos) -->
        <g v-if="feature === 'inventory'">
            <!-- Shelf/Cabinet -->
            <rect
                x="80"
                y="80"
                width="240"
                height="140"
                :fill="isDark ? '#1e293b' : '#f8fafc'"
                rx="12"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="2"
            />

            <!-- Shelves -->
            <line x1="90" y1="130" x2="310" y2="130" :stroke="isDark ? '#475569' : '#e2e8f0'" stroke-width="2" />
            <line x1="90" y1="180" x2="310" y2="180" :stroke="isDark ? '#475569' : '#e2e8f0'" stroke-width="2" />

            <!-- Medicine bottles on shelves -->
            <g v-for="(bottle, index) in inventoryBottles" :key="index">
                <rect :x="bottle.x" :y="bottle.y" width="25" height="40" :fill="bottle.color" rx="4" />
                <rect :x="bottle.x + 2" :y="bottle.y - 5" width="21" height="8" :fill="isDark ? '#475569' : '#e2e8f0'" rx="2" />
                <circle :cx="bottle.x + 12" :cy="bottle.y + 20" r="3" fill="white" opacity="0.7" />
            </g>

            <!-- Warning indicator for low stock -->
            <circle cx="280" cy="110" r="15" fill="#ef4444" />
            <text x="280" y="115" fill="white" font-size="12" font-weight="bold" text-anchor="middle">!</text>

            <!-- Inventory count -->
            <rect x="120" y="240" width="160" height="30" :fill="isDark ? '#334155' : '#ffffff'" rx="8" />
            <text x="200" y="260" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="600" text-anchor="middle">
                {{ t('5 medications • 2 remaining') }}
            </text>
        </g>

        <!-- Feature 8: Advanced Reports (Reportes Avanzados) -->
        <g v-if="feature === 'reports'">
            <!-- Tablet device -->
            <rect
                x="80"
                y="40"
                width="240"
                height="180"
                :fill="isDark ? '#1e293b' : '#f8fafc'"
                rx="20"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="3"
            />
            <rect x="90" y="60" width="220" height="140" :fill="isDark ? '#0f172a' : '#ffffff'" rx="12" />

            <!-- Report header -->
            <rect x="90" y="60" width="220" height="30" fill="#00d2b3" rx="12" />
            <rect x="90" y="75" width="220" height="15" fill="#00d2b3" />
            <text x="200" y="82" fill="white" font-size="12" font-weight="600" text-anchor="middle">
                {{ t('Adherence Report') }}
            </text>

            <!-- Main chart area -->
            <rect x="100" y="100" width="200" height="80" :fill="isDark ? '#334155' : '#f8fafc'" rx="8" />

            <!-- Line chart -->
            <polyline
                points="110,160 130,140 150,145 170,125 190,130 210,115 230,120 250,105 270,110 290,95"
                fill="none"
                stroke="#00d2b3"
                stroke-width="3"
                stroke-linecap="round"
            />

            <!-- Data points -->
            <g v-for="(point, index) in reportDataPoints" :key="index">
                <circle :cx="point.x" :cy="point.y" r="4" fill="#00d2b3" />
                <circle :cx="point.x" :cy="point.y" r="2" fill="white" />
            </g>

            <!-- Percentage indicators -->
            <text x="110" y="195" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="700">94%</text>
            <text x="110" y="208" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">
                {{ t('March') }}
            </text>

            <text x="200" y="195" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="700">97%</text>
            <text x="200" y="208" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">
                {{ t('April') }}
            </text>

            <text x="280" y="195" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="700">92%</text>
            <text x="280" y="208" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">
                {{ t('May') }}
            </text>

            <!-- PDF export icon -->
            <rect x="270" y="110" width="20" height="25" :fill="isDark ? '#475569' : '#e2e8f0'" rx="3" />
            <rect x="272" y="112" width="16" height="21" :fill="isDark ? '#334155' : '#ffffff'" rx="2" />
            <text x="280" y="125" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="8" font-weight="600" text-anchor="middle">PDF</text>

            <!-- Animated progress indicator -->
            <rect x="100" y="185" width="200" height="4" :fill="isDark ? '#475569' : '#e2e8f0'" rx="2" />
            <rect x="100" y="185" width="180" height="4" fill="#00d2b3" rx="2">
                <animate attributeName="width" values="0;180;0" dur="3s" repeatCount="indefinite" />
            </rect>
        </g>

        <!-- Feature 9: Secure Sharing (Compartir Seguro) -->
        <g v-if="feature === 'sharing'">
            <!-- Main device/phone -->
            <rect
                x="160"
                y="60"
                width="80"
                height="140"
                :fill="isDark ? '#1e293b' : '#f8fafc'"
                rx="16"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="2"
            />
            <rect x="170" y="80" width="60" height="100" :fill="isDark ? '#0f172a' : '#ffffff'" rx="8" />

            <!-- Screen content -->
            <text x="200" y="95" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="8" font-weight="600" text-anchor="middle">
                {{ t('Share with') }}
            </text>

            <!-- User avatars on screen -->
            <circle cx="185" cy="110" r="8" fill="#f59e0b" />
            <circle cx="200" cy="110" r="8" fill="#3b82f6" />
            <circle cx="215" cy="110" r="8" fill="#ef4444" />

            <!-- Permission controls -->
            <rect x="175" y="125" width="50" height="15" :fill="isDark ? '#334155' : '#f1f5f9'" rx="7" />
            <circle cx="190" cy="132" r="5" fill="#00d2b3" />
            <text x="205" y="135" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="6">
                {{ t('View all') }}
            </text>

            <rect x="175" y="145" width="50" height="15" :fill="isDark ? '#334155' : '#f1f5f9'" rx="7" />
            <circle cx="190" cy="152" r="5" :fill="isDark ? '#64748b' : '#cbd5e1'" />
            <text x="205" y="155" :fill="isDark ? '#64748b' : '#64748b'" font-size="6">
                {{ t('Only alerts') }}
            </text>

            <!-- Secure connection indicators -->
            <g v-for="(connection, index) in secureConnections" :key="index">
                <line
                    :x1="200"
                    :y1="110"
                    :x2="connection.x"
                    :y2="connection.y"
                    stroke="#00d2b3"
                    stroke-width="2"
                    stroke-dasharray="3,3"
                    opacity="0.7"
                >
                    <animate attributeName="stroke-dashoffset" values="0;6" dur="1s" repeatCount="indefinite" />
                </line>

                <!-- External user devices -->
                <rect
                    :x="connection.x - 15"
                    :y="connection.y - 20"
                    width="30"
                    height="40"
                    :fill="isDark ? '#334155' : '#f8fafc'"
                    rx="8"
                    :stroke="isDark ? '#475569' : '#e2e8f0'"
                    stroke-width="1"
                />
                <rect :x="connection.x - 10" :y="connection.y - 15" width="20" height="30" :fill="isDark ? '#1e293b' : '#ffffff'" rx="4" />

                <!-- Lock icon for security -->
                <rect
                    :x="connection.x - 4"
                    :y="connection.y - 8"
                    width="8"
                    height="6"
                    fill="none"
                    :stroke="connection.color"
                    stroke-width="1"
                    rx="2"
                />
                <rect :x="connection.x - 3" :y="connection.y - 5" width="6" height="4" :fill="connection.color" rx="1" />
            </g>

            <!-- Security badge -->
            <circle cx="200" cy="230" r="20" fill="#00d2b3" />
            <path d="M190 230 L195 235 L210 220" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            <text x="200" y="250" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="8" font-weight="600" text-anchor="middle">
                {{ t('Secure Sharing') }}
            </text>
        </g>

        <!-- Feature 10: Inventory Management (Gestión de Inventario) -->
        <g v-if="feature === 'inventory-management'">
            <!-- Medicine cabinet/shelf system -->
            <rect
                x="60"
                y="60"
                width="280"
                height="180"
                :fill="isDark ? '#1e293b' : '#f8fafc'"
                rx="16"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="2"
            />

            <!-- Cabinet shelves -->
            <line x1="70" y1="120" x2="330" y2="120" :stroke="isDark ? '#475569' : '#e2e8f0'" stroke-width="2" />
            <line x1="70" y1="180" x2="330" y2="180" :stroke="isDark ? '#475569' : '#e2e8f0'" stroke-width="2" />

            <!-- Medicine bottles with smart labels -->
            <g v-for="(bottle, index) in smartInventoryBottles" :key="index">
                <rect :x="bottle.x" :y="bottle.y" width="30" height="45" :fill="bottle.color" rx="6" />
                <rect :x="bottle.x + 2" :y="bottle.y - 8" width="26" height="12" :fill="isDark ? '#475569' : '#e2e8f0'" rx="3" />

                <!-- Smart label with count -->
                <rect :x="bottle.x + 5" :y="bottle.y + 10" width="20" height="15" :fill="isDark ? '#0f172a' : '#ffffff'" rx="2" />
                <text :x="bottle.x + 15" :y="bottle.y + 20" :fill="bottle.textColor" font-size="8" font-weight="600" text-anchor="middle">
                    {{ bottle.count }}
                </text>

                <!-- Low stock warning -->
                <circle v-if="bottle.lowStock" :cx="bottle.x + 25" :cy="bottle.y + 5" r="6" fill="#ef4444" />
                <text v-if="bottle.lowStock" :x="bottle.x + 25" :y="bottle.y + 8" fill="white" font-size="8" font-weight="bold" text-anchor="middle">
                    !
                </text>
            </g>

            <!-- Auto-tracking system -->
            <rect x="80" y="200" width="240" height="30" :fill="isDark ? '#334155' : '#ffffff'" rx="8" />

            <!-- Progress bars for different medications -->
            <g v-for="(med, index) in medicationProgress" :key="index">
                <rect :x="90 + index * 70" y="210" width="60" height="4" :fill="isDark ? '#475569' : '#e2e8f0'" rx="2" />
                <rect :x="90 + index * 70" y="210" :width="med.progress" height="4" :fill="med.color" rx="2" />
                <text :x="120 + index * 70" y="225" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="7" text-anchor="middle">
                    {{ t(med.name) }}
                </text>
            </g>

            <!-- Smart notification -->
            <rect
                x="250"
                y="80"
                width="80"
                height="50"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
            />
            <circle cx="270" cy="95" r="6" fill="#f59e0b" />
            <text x="280" y="98" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="8" font-weight="600">
                {{ t('Refill') }}
            </text>
            <text x="290" y="110" :fill="isDark ? '#64748b' : '#64748b'" font-size="7" text-anchor="middle">
                {{ t('Aspirin') }}
            </text>
            <text x="290" y="120" :fill="isDark ? '#64748b' : '#64748b'" font-size="7" text-anchor="middle">
                {{ t('3 days remaining') }}
            </text>

            <!-- Animated scanning beam -->
            <rect x="70" y="70" width="2" height="160" fill="#00d2b3" opacity="0.6">
                <animate attributeName="x" values="70;320;70" dur="4s" repeatCount="indefinite" />
                <animate attributeName="opacity" values="0.6;1;0.6" dur="4s" repeatCount="indefinite" />
            </rect>
        </g>
    </svg>
</template>
