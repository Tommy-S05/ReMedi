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

const dayHeaders = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];

const calendarWeeks = [
    [
        { number: 1, hasActivity: true },
        { number: 2, hasActivity: false },
        { number: 3, hasActivity: true },
        { number: 4, hasActivity: true },
        { number: 5, hasActivity: false },
        { number: 6, hasActivity: true },
        { number: 7, hasActivity: true },
    ],
    [
        { number: 8, hasActivity: true },
        { number: 9, hasActivity: true },
        { number: 10, hasActivity: false },
        { number: 11, hasActivity: true },
        { number: 12, hasActivity: true },
        { number: 13, hasActivity: true },
        { number: 14, hasActivity: true },
    ],
    [
        { number: 15, hasActivity: true },
        { number: 16, hasActivity: true },
        { number: 17, hasActivity: true },
        { number: 18, hasActivity: false },
        { number: 19, hasActivity: true },
        { number: 20, hasActivity: true },
        { number: 21, hasActivity: true },
    ],
    [
        { number: 22, hasActivity: true },
        { number: 23, hasActivity: true },
        { number: 24, hasActivity: true },
        { number: 25, hasActivity: true },
        { number: 26, hasActivity: false },
        { number: 27, hasActivity: true },
        { number: 28, hasActivity: true },
    ],
];

const recentActivities = [
    { medication: 'Aspirin 100mg', time: 'Today 8:00 AM', status: 'taken' },
    { medication: 'Vitamin D', time: 'Today 6:00 PM', status: 'taken' },
    { medication: 'Omega 3', time: 'Yesterday 8:00 PM', status: 'missed' },
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

        <!-- Title -->
        <text x="50" y="55" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="16" font-weight="600">{{ t('Medication History') }}</text>

        <!-- Filter buttons -->
        <rect x="350" y="35" width="60" height="25" :fill="isDark ? '#334155' : '#e2e8f0'" rx="12" />
        <text x="380" y="50" :fill="isDark ? '#e2e8f0' : '#64748b'" font-size="10" text-anchor="middle">{{ t('Week') }}</text>

        <rect x="420" y="35" width="50" height="25" fill="#00d2b3" rx="12" />
        <text x="445" y="50" fill="white" font-size="10" text-anchor="middle">{{ t('Month') }}</text>

        <rect x="480" y="35" width="50" height="25" :fill="isDark ? '#334155' : '#e2e8f0'" rx="12" />
        <text x="505" y="50" :fill="isDark ? '#e2e8f0' : '#64748b'" font-size="10" text-anchor="middle">{{ t('Year') }}</text>

        <!-- Calendar grid -->
        <rect x="50" y="100" width="500" height="280" :fill="isDark ? '#1e293b' : '#f8fafc'" rx="12" />

        <!-- Calendar header -->
        <text x="300" y="125" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="14" font-weight="600" text-anchor="middle">{{ t('January 2024') }}</text>

        <!-- Day headers -->
        <g v-for="(day, index) in dayHeaders" :key="index">
            <text :x="80 + index * 60" y="145" :fill="isDark ? '#64748b' : '#64748b'" font-size="10" font-weight="500" text-anchor="middle">
                {{ t(day) }}
            </text>
        </g>

        <!-- Calendar days -->
        <g v-for="(week, weekIndex) in calendarWeeks" :key="weekIndex">
            <g v-for="(day, dayIndex) in week" :key="dayIndex">
                <rect
                    :x="50 + dayIndex * 60"
                    :y="155 + weekIndex * 35"
                    width="50"
                    height="30"
                    :fill="day.hasActivity ? '#00d2b3' : isDark ? '#334155' : '#ffffff'"
                    rx="6"
                    :stroke="isDark ? '#475569' : '#e2e8f0'"
                    stroke-width="1"
                />
                <text
                    :x="75 + dayIndex * 60"
                    :y="175 + weekIndex * 35"
                    :fill="day.hasActivity ? 'white' : isDark ? '#e2e8f0' : '#1e293b'"
                    font-size="12"
                    text-anchor="middle"
                >
                    {{ day.number }}
                </text>

                <!-- Activity dots -->
                <g v-if="day.hasActivity">
                    <circle :cx="85 + dayIndex * 60" :cy="180 + weekIndex * 35" r="2" fill="white" opacity="0.8" />
                    <circle :cx="90 + dayIndex * 60" :cy="180 + weekIndex * 35" r="2" fill="white" opacity="0.6" />
                    <circle :cx="95 + dayIndex * 60" :cy="180 + weekIndex * 35" r="2" fill="white" opacity="0.4" />
                </g>
            </g>
        </g>

        <!-- Statistics panel -->
        <rect
            x="450"
            y="100"
            width="100"
            height="120"
            :fill="isDark ? '#334155' : '#ffffff'"
            rx="8"
            :stroke="isDark ? '#475569' : '#e2e8f0'"
            stroke-width="1"
        />

        <text x="500" y="120" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="600" text-anchor="middle">{{ t('Statistics') }}</text>

        <text x="465" y="140" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">{{ t('Adherence') }}:</text>
        <text x="535" y="140" fill="#00d2b3" font-size="10" font-weight="600" text-anchor="end">94%</text>

        <text x="465" y="155" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">{{ t('Taken') }}:</text>
        <text x="535" y="155" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10" font-weight="600" text-anchor="end">28/30</text>

        <text x="465" y="170" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">{{ t('Missed') }}:</text>
        <text x="535" y="170" fill="#ef4444" font-size="10" font-weight="600" text-anchor="end">2</text>

        <text x="465" y="185" :fill="isDark ? '#64748b' : '#64748b'" font-size="8">{{ t('Streak') }}:</text>
        <text x="535" y="185" fill="#00d2b3" font-size="10" font-weight="600" text-anchor="end">7 días</text>

        <!-- Recent activity -->
        <text x="70" y="340" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="12" font-weight="600">{{ t('Recent Activity') }}</text>

        <g v-for="(activity, index) in recentActivities" :key="index">
            <circle :cx="80" :cy="360 + index * 15" r="4" :fill="activity.status === 'taken' ? '#00d2b3' : '#ef4444'" />
            <text :x="95" :y="365 + index * 15" :fill="isDark ? '#e2e8f0' : '#1e293b'" font-size="10">
                {{ t(activity.medication) }}
            </text>
            <text :x="200" :y="365 + index * 15" :fill="isDark ? '#64748b' : '#64748b'" font-size="9">{{ t(activity.time) }}</text>
            <text :x="300" :y="365 + index * 15" :fill="activity.status === 'taken' ? '#00d2b3' : '#ef4444'" font-size="9">
                {{ activity.status === 'taken' ? t('Taken') : t('Lost') }}
            </text>
        </g>
    </svg>
</template>
