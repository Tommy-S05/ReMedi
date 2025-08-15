<script setup lang="ts">
import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

defineOptions({ inheritAttrs: false });

const { t } = useTranslations();

type Fit = 'contain' | 'cover' | 'none';

const props = defineProps<{
    isDark?: boolean;
    responsive?: boolean;
    width?: number | string;
    height?: number | string;
    size?: number | string;
    wrapperFit?: Fit;
    title?: string;
    desc?: string;
    ariaHidden?: boolean;
    color?: string;
    stroke?: string;
    fill?: string;
    strokeWidth?: number | string;
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

const medications = [
    { name: 'Aspirin', dose: '100mg', time: '8:00 AM', taken: true, color: '#00d2b3' },
    { name: 'Vitamin D', dose: '1000 IU', time: '2:30 PM', taken: false, color: '#f59e0b' },
    { name: 'Omega 3', dose: '1 capsule', time: '8:00 PM', taken: false, color: '#3b82f6' },
];

const navigation = [{ active: true }, { active: false }, { active: false }, { active: false }];
</script>

<template>
    <svg
        viewBox="0 0 500 600"
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

        <!-- Phone mockup -->
        <rect
            x="50"
            y="50"
            width="400"
            height="500"
            :fill="isDark ? '#1e293b' : '#f1f5f9'"
            rx="40"
            :stroke="isDark ? '#475569' : '#cbd5e1'"
            stroke-width="2"
        />

        <!-- Screen -->
        <rect x="70" y="90" width="360" height="420" :fill="isDark ? '#0f172a' : '#ffffff'" rx="20" />

        <!-- Status bar -->
        <rect x="90" y="110" width="320" height="20" :fill="isDark ? '#1e293b' : '#f8fafc'" />
        <text x="110" y="125" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="500">9:41</text>
        <circle cx="380" cy="120" r="3" fill="#00d2b3" />
        <rect x="390" y="117" width="15" height="6" :fill="isDark ? '#e2e8f0' : '#1e293b'" rx="3" />

        <!-- Header -->
        <rect x="90" y="140" width="320" height="50" :fill="isDark ? '#1e293b' : '#f8fafc'" rx="8" />
        <circle cx="115" cy="165" r="15" fill="#00d2b3" />
        <path d="M110 165 L115 160 L120 165 L115 170 Z" fill="white" />
        <text x="140" y="170" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="16" font-weight="600">ReMedi</text>

        <!-- Greeting -->
        <text x="110" y="220" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="14" font-weight="500">{{ t('Hello, Maria!') }}</text>
        <text x="110" y="240" :fill="isDark ? '#64748b' : '#64748b'" font-size="12">{{ t('You have 3 medications scheduled for today') }}</text>

        <!-- Quick stats -->
        <rect x="110" y="260" width="280" height="60" :fill="isDark ? '#334155' : '#f1f5f9'" rx="12" />

        <g transform="translate(130, 280)">
            <text x="0" y="0" fill="#00d2b3" font-size="18" font-weight="700">94%</text>
            <text x="0" y="15" :fill="isDark ? '#64748b' : '#64748b'" font-size="10">{{ t('Adherence') }}</text>
        </g>

        <g transform="translate(220, 280)">
            <text x="0" y="0" fill="#00d2b3" font-size="18" font-weight="700">12</text>
            <text x="0" y="15" :fill="isDark ? '#64748b' : '#64748b'" font-size="10">{{ t('Medications') }}</text>
        </g>

        <g transform="translate(310, 280)">
            <text x="0" y="0" fill="#00d2b3" font-size="18" font-weight="700">3</text>
            <text x="0" y="15" :fill="isDark ? '#64748b' : '#64748b'" font-size="10">{{ t('Today') }}</text>
        </g>

        <!-- Upcoming medications -->
        <text x="110" y="350" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="14" font-weight="600">{{ t('Upcoming Doses') }}...</text>

        <!-- Medication cards -->
        <g v-for="(med, index) in medications" :key="index">
            <rect
                :x="110"
                :y="365 + index * 55"
                width="280"
                height="45"
                :fill="isDark ? '#334155' : '#ffffff'"
                rx="8"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
            />

            <!-- Medication icon -->
            <rect :x="125" :y="375 + index * 55" width="25" height="25" :fill="med.color" rx="4" />
            <circle :cx="137.5" :cy="387.5 + index * 55" r="3" fill="white" />

            <!-- Medication info -->
            <text :x="160" :y="385 + index * 55" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="500">
                {{ t(med.name)}}
            </text>
            <text :x="160" :y="400 + index * 55" :fill="isDark ? '#64748b' : '#64748b'" font-size="10">{{ t(med.dose) }} • {{ t(med.time) }}</text>

            <!-- Status -->
            <circle :cx="360" :cy="387.5 + index * 55" r="8" :fill="med.taken ? '#00d2b3' : isDark ? '#475569' : '#e2e8f0'" />
            <path v-if="med.taken" d="M356 387.5 L359 390.5 L364 384.5" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" />
        </g>

        <!-- Bottom navigation -->
        <rect x="90" y="470" width="320" height="50" :fill="isDark ? '#1e293b' : '#f8fafc'" rx="8" />

        <g v-for="(nav, index) in navigation" :key="index">
            <circle :cx="130 + index * 80" cy="495" r="12" :fill="nav.active ? '#00d2b3' : 'transparent'" />
            <rect :x="125 + index * 80" :y="490" width="10" height="10" :fill="nav.active ? 'white' : isDark ? '#64748b' : '#64748b'" rx="2" />
        </g>

        <!-- Floating notification -->
        <g>
            <rect
                x="200"
                y="30"
                width="150"
                height="40"
                :fill="isDark ? '#0f172a' : '#ffffff'"
                rx="20"
                :stroke="isDark ? '#475569' : '#e2e8f0'"
                stroke-width="1"
                filter="drop-shadow(0 4px 12px rgba(0, 0, 0, 0.15))"
            />
            <circle cx="220" cy="50" r="5" fill="#00d2b3">
                <animate attributeName="opacity" values="1;0.5;1" dur="2s" repeatCount="indefinite" />
            </circle>
            <text x="235" y="47" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="500">{{ t('Reminder') }}</text>
            <text x="235" y="57" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">{{ t('Aspirin in 5 min') }}</text>
        </g>
    </svg>
</template>
