<script setup lang="ts">
import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

defineOptions({ inheritAttrs: false });

type Fit = 'contain' | 'cover' | 'none';

const { t } = useTranslations();

const props = defineProps<{
    /** Dark mode (if your art reacts to isDark) */
    isDark?: boolean;
    /** Occupy 100% of the container */
    responsive?: boolean;
    /** Explicit sizes or square shortcut */
    width?: number | string;
    height?: number | string;
    size?: number | string;
    /** Fit within the viewBox */
    wrapperFit?: Fit;
    /** Accessibility */
    title?: string;
    desc?: string;
    ariaHidden?: boolean;
    /** Optional styles */
    color?: string;
    stroke?: string;
    fill?: string;
    strokeWidth?: number | string;
    /** Extra classes and animation */
    extraClass?: string;
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

const timeSlots = ['8:00 AM', '4:00 PM', '12:00 AM'];
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

        <!-- Back button -->
        <circle cx="50" cy="50" r="15" :fill="isDark ? '#334155' : '#e2e8f0'" />
        <path
            d="M45 50 L50 45 M45 50 L50 55 M45 50 L55 50"
            :stroke="isDark ? '#e2e8f0' : '#334155'"
            stroke-width="2"
            fill="none"
            stroke-linecap="round"
        />

        <!-- Title -->
        <text x="80" y="55" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="16" font-weight="600">{{ t('Add Medication') }}</text>

        <!-- Main form area -->
        <rect x="50" y="100" width="500" height="280" :fill="isDark ? '#1e293b' : '#f8fafc'" rx="12" />

        <!-- Form fields -->
        <!-- Medication name -->
        <text x="80" y="130" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="500">{{ t('Medication Name') }}</text>
        <rect
            x="80"
            y="140"
            width="440"
            height="40"
            :fill="isDark ? '#334155' : '#ffffff'"
            rx="8"
            :stroke="isDark ? '#475569' : '#e2e8f0'"
            stroke-width="1"
        />
        <text x="95" y="165" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="14">{{ t('Aspirin 100mg') }}</text>

        <!-- Dosage -->
        <text x="80" y="200" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="500">{{ t('Dosage') }}</text>
        <rect
            x="80"
            y="210"
            width="200"
            height="40"
            :fill="isDark ? '#334155' : '#ffffff'"
            rx="8"
            :stroke="isDark ? '#475569' : '#e2e8f0'"
            stroke-width="1"
        />
        <text x="95" y="235" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="14">{{ t('1 tablet') }}</text>

        <!-- Frequency -->
        <text x="320" y="200" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="500">{{ t('Frequency') }}</text>
        <rect
            x="320"
            y="210"
            width="200"
            height="40"
            :fill="isDark ? '#334155' : '#ffffff'"
            rx="8"
            :stroke="isDark ? '#475569' : '#e2e8f0'"
            stroke-width="1"
        />
        <text x="335" y="235" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="14">{{ t('Every 8 hours') }}</text>

        <!-- Schedule section -->
        <text x="80" y="280" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="500">{{ t('Schedules') }}</text>

        <!-- Time slots -->
        <g v-for="(time, index) in timeSlots" :key="index">
            <rect
                :x="80 + index * 140"
                y="290"
                width="120"
                height="35"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
            />
            <text :x="140 + index * 140" y="312" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="500" text-anchor="middle">
                {{ time }}
            </text>
        </g>

        <!-- Add time button -->
        <rect x="500" y="290" width="35" height="35" fill="#00d2b3" rx="8" />
        <path d="M517.5 300 L517.5 320 M507.5 310 L527.5 310" stroke="white" stroke-width="2" stroke-linecap="round" />

        <!-- Save button -->
        <rect x="420" y="340" width="100" height="35" fill="#00d2b3" rx="8" />
        <text x="470" y="362" fill="white" font-size="12" font-weight="600" text-anchor="middle">{{ t('Save') }}</text>

        <!-- Medication icon -->
        <g transform="translate(450, 120)">
            <rect width="60" height="80" :fill="isDark ? '#334155' : '#e2e8f0'" rx="8" />
            <rect x="10" y="15" width="40" height="50" fill="#00d2b3" rx="4" />
            <circle cx="30" cy="25" r="3" fill="white" />
            <circle cx="30" cy="35" r="3" fill="white" />
            <circle cx="30" cy="45" r="3" fill="white" />
            <circle cx="30" cy="55" r="3" fill="white" />
            <text x="30" y="75" :fill="isDark ? '#64748b' : '#64748b'" font-size="8" text-anchor="middle">{{ t('Aspirin') }}</text>
        </g>
    </svg>
</template>
