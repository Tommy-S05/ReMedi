<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import { computed } from 'vue';

defineOptions({ inheritAttrs: false });

type Fit = 'contain' | 'cover' | 'none';

const { t } = useTranslations();

const props = defineProps<{
    /** Dark mode for internal art colors */
    isDark?: boolean;
    /** If true, the SVG occupies 100% of the container */
    responsive?: boolean;
    /** Specific width */
    width?: number | string;
    /** Specific height */
    height?: number | string;
    /** Square size (fallback if no width/height) */
    size?: number | string;
    /** Fit within the viewBox */
    wrapperFit?: Fit;
    /** Accessibility */
    title?: string;
    desc?: string;
    ariaHidden?: boolean;
    /** Stroke/fill styles */
    color?: string;
    stroke?: string;
    fill?: string;
    strokeWidth?: number | string;
    /** Extra CSS classes */
    extraClass?: string;
    /** Optional animation (spin) */
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
    return [base, props.extraClass].filter(Boolean).join(' ');
});

const medications = [
    { name: 'Aspirin', time: '2:30 PM' },
    { name: 'Vitamin D', time: '6:00 PM' },
    { name: 'Omega 3', time: '8:00 PM' },
];
</script>

<template>
    <svg
        viewBox="0 0 600 400"
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
            <title :id="titleId">{ '{' } props.title { '}' }</title>
            <desc v-if="props.desc">{ '{' } props.desc { '}' }</desc>
        </template>

        <!-- Background -->
        <rect width="600" height="400" :fill="isDark ? '#0f172a' : '#ffffff'" rx="20" />

        <!-- Header -->
        <rect x="20" y="20" width="560" height="60" :fill="isDark ? '#1e293b' : '#f8fafc'" rx="12" />

        <!-- Logo and title -->
        <rect x="40" y="35" width="30" height="30" fill="#00d2b3" rx="8" />
        <rect x="85" y="40" width="80" height="8" :fill="isDark ? '#e2e8f0' : '#334155'" rx="4" />
        <rect x="85" y="52" width="60" height="6" :fill="isDark ? '#64748b' : '#64748b'" rx="3" />

        <!-- Navigation -->
        <rect x="450" y="40" width="60" height="20" :fill="isDark ? '#334155' : '#e2e8f0'" rx="10" />
        <rect x="520" y="40" width="50" height="20" fill="#00d2b3" rx="10" />

        <!-- Main content area -->
        <rect x="20" y="100" width="360" height="280" :fill="isDark ? '#1e293b' : '#f8fafc'" rx="12" />

        <!-- Stats cards -->
        <rect
            x="40"
            y="120"
            width="100"
            height="80"
            :fill="isDark ? '#334155' : '#ffffff'"
            rx="8"
            :stroke="isDark ? '#475569' : '#e2e8f0'"
            stroke-width="1"
        />
        <rect
            x="160"
            y="120"
            width="100"
            height="80"
            :fill="isDark ? '#334155' : '#ffffff'"
            rx="8"
            :stroke="isDark ? '#475569' : '#e2e8f0'"
            stroke-width="1"
        />
        <rect
            x="280"
            y="120"
            width="80"
            height="80"
            :fill="isDark ? '#334155' : '#ffffff'"
            rx="8"
            :stroke="isDark ? '#475569' : '#e2e8f0'"
            stroke-width="1"
        />

        <!-- Stat values -->
        <text x="90" y="145" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="600" text-anchor="middle">94%</text>
        ```
        <text x="90" y="160" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">{{ t('Adherence') }}</text>
        ```
        <text x="210" y="145" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="600" text-anchor="middle">12</text>
        ```
        <text x="210" y="160" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">{{ t('Medications') }}</text>
        ```
        <text x="320" y="145" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="600" text-anchor="middle">3</text>
        ```
        <text x="320" y="160" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">{{ t('Upcoming') }}</text>
        ```
        <!-- Chart area -->
        <rect
            x="40"
            y="220"
            width="320"
            height="140"
            :fill="isDark ? '#334155' : '#ffffff'"
            rx="8"
            :stroke="isDark ? '#475569' : '#e2e8f0'"
            stroke-width="1"
        />

        <!-- Chart bars -->
        <rect x="60" y="280" width="20" height="60" fill="#00d2b3" rx="2" />
        <rect x="90" y="260" width="20" height="80" fill="#00d2b3" rx="2" />
        <rect x="120" y="290" width="20" height="50" fill="#00d2b3" rx="2" />
        <rect x="150" y="250" width="20" height="90" fill="#00d2b3" rx="2" />
        <rect x="180" y="270" width="20" height="70" fill="#00d2b3" rx="2" />
        <rect x="210" y="240" width="20" height="100" fill="#00d2b3" rx="2" />
        <rect x="240" y="285" width="20" height="55" fill="#00d2b3" rx="2" />
        <rect x="270" y="265" width="20" height="75" fill="#00d2b3" rx="2" />
        <rect x="300" y="275" width="20" height="65" fill="#00d2b3" rx="2" />
        <rect x="330" y="255" width="20" height="85" fill="#00d2b3" rx="2" />
        ```
        <!-- Chart title -->
        <text x="200" y="240" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="500" text-anchor="middle">
            {{ t('Weekly Adherence') }}
        </text>
        ```
        <!-- Sidebar -->
        <rect x="400" y="100" width="180" height="280" :fill="isDark ? '#1e293b' : '#f8fafc'" rx="12" />
        ```
        <!-- Upcoming medications -->
        <text x="420" y="125" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="600">{{ t('Upcoming Doses') }}</text>
        ```
        <!-- Medication items -->
        <g v-for="(med, index) in medications" :key="index">
            <rect
                :x="420"
                :y="140 + index * 50"
                width="140"
                height="40"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
            />
            <circle :cx="435" :cy="160 + index * 50" r="6" fill="#00d2b3" />
            <text :x="450" :y="155 + index * 50" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="9" font-weight="500">
                {{ t(med.name) }}
            </text>
            <text :x="450" :y="167 + index * 50" :fill="isDark ? '#64748b' : '#64748b'" font-size="7">{{ t(med.time) }}</text>
        </g>

        <!-- Floating notification -->
        <g>
            <rect
                x="450"
                y="50"
                width="120"
                height="35"
                :fill="isDark ? '#0f172a' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
                filter="drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1))"
            />
            <circle cx="465" cy="67" r="4" fill="#00d2b3">
                <animate attributeName="opacity" values="1;0.5;1" dur="2s" repeatCount="indefinite" />
            </circle>
            <text x="475" y="65" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="8" font-weight="500">Recordatorio</text>
            <text x="475" y="75" :fill="isDark ? '#64748b' : '#64748b'" font-size="7">Aspirina - 2:30 PM</text>
        </g>
    </svg>
</template>
