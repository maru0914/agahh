<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps<{
    startTime: string | null;
    endTime: string | null;
    stepMinutes?: number; // default 5
    busyRanges?: Array<{ start: string; end: string }>;
}>();

const emit = defineEmits<{
    (e: 'update:startTime', v: string): void;
    (e: 'update:endTime', v: string): void;
}>();

const container = ref<HTMLElement | null>(null);
const dragging = ref(false);
const startMin = ref<number | null>(null);
const endMin = ref<number | null>(null);
const step = computed(() => props.stepMinutes ?? 5);

function timeToMinutes(t: string | null): number | null {
    if (!t) return null;
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
}

function minutesToTime(m: number): string {
    const hh = Math.floor(m / 60)
        .toString()
        .padStart(2, '0');
    const mm = Math.round(m % 60)
        .toString()
        .padStart(2, '0');
    return `${hh}:${mm}`;
}

function snap(m: number): number {
    const s = step.value;
    return Math.max(0, Math.min(1440, Math.round(m / s) * s));
}

function pxToMinutes(x: number): number {
    const el = container.value!;
    const rect = el.getBoundingClientRect();
    const ratio = (x - rect.left) / rect.width;
    return snap(ratio * 1440);
}

function onMouseDown(e: MouseEvent) {
    dragging.value = true;
    const m = pxToMinutes(e.clientX);
    startMin.value = m;
    endMin.value = m;
}

function onMouseMove(e: MouseEvent) {
    if (!dragging.value) return;
    endMin.value = pxToMinutes(e.clientX);
}

function rangesOverlap(
    aStart: number,
    aEnd: number,
    bStart: number,
    bEnd: number,
) {
    return aStart < bEnd && aEnd > bStart;
}

const busyRangesMin = computed(() => {
    const list = props.busyRanges ?? [];
    return list
        .map((r) => ({
            s: timeToMinutes(r.start) ?? 0,
            e: timeToMinutes(r.end) ?? 0,
        }))
        .filter((r) => r.s < r.e);
});

const hasOverlap = computed(() => {
    if (startMin.value === null || endMin.value === null) return false;
    const a = Math.min(startMin.value, endMin.value);
    const b = Math.max(startMin.value, endMin.value);
    return busyRangesMin.value.some((r) => rangesOverlap(a, b, r.s, r.e));
});

const durationText = computed(() => {
    if (startMin.value === null || endMin.value === null) return '';
    const mins = Math.abs(endMin.value - startMin.value);
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    if (h === 0) return `${m}分`;
    if (m === 0) return `${h}時間`;
    return `${h}時間${m}分`;
});

function onMouseUp() {
    if (!dragging.value || startMin.value === null || endMin.value === null)
        return;
    dragging.value = false;
    const a = Math.min(startMin.value, endMin.value);
    const b = Math.max(startMin.value, endMin.value);
    if (busyRangesMin.value.some((r) => rangesOverlap(a, b, r.s, r.e))) {
        // Overlap: do not emit, keep current visual but let parent show error
        return;
    }
    emit('update:startTime', minutesToTime(a));
    emit('update:endTime', minutesToTime(b));
}

onMounted(() => {
    window.addEventListener('mouseup', onMouseUp);
    window.addEventListener('mousemove', onMouseMove);
    startMin.value = timeToMinutes(props.startTime);
    endMin.value = timeToMinutes(props.endTime);
});
onUnmounted(() => {
    window.removeEventListener('mouseup', onMouseUp);
    window.removeEventListener('mousemove', onMouseMove);
});

watch(
    () => props.startTime,
    (v) => {
        startMin.value = timeToMinutes(v);
    },
);
watch(
    () => props.endTime,
    (v) => {
        endMin.value = timeToMinutes(v);
    },
);

const selectionStyle = computed(() => {
    if (startMin.value === null || endMin.value === null)
        return { display: 'none' };
    const a = Math.min(startMin.value, endMin.value);
    const b = Math.max(startMin.value, endMin.value);
    const left = (a / 1440) * 100;
    const width = ((b - a) / 1440) * 100;
    return { left: left + '%', width: width + '%' };
});
</script>

<template>
    <div class="w-full">
        <div class="mb-2 flex justify-between text-xs text-gray-500">
            <span>{{ startTime ?? '開始未選択' }}</span>
            <span>{{ endTime ?? '終了未選択' }}</span>
        </div>
        <div
            ref="container"
            class="relative h-10 w-full select-none rounded border border-gray-300 bg-gray-100"
            @mousedown="onMouseDown"
            title="ドラッグで時間帯を選択"
        >
            <!-- hour ticks -->
            <div class="absolute inset-0">
                <div
                    v-for="h in 24"
                    :key="h"
                    class="absolute top-0 h-full w-px bg-gray-300"
                    :style="{ left: (h / 24) * 100 + '%' }"
                ></div>
            </div>
            <!-- busy ranges -->
            <div class="absolute inset-0">
                <div
                    v-for="(r, i) in busyRangesMin"
                    :key="i"
                    class="absolute top-0 h-full rounded bg-gray-400/50"
                    :style="{
                        left: (r.s / 1440) * 100 + '%',
                        width: ((r.e - r.s) / 1440) * 100 + '%',
                    }"
                ></div>
            </div>
            <!-- selection -->
            <div
                class="absolute top-0 h-full rounded"
                :class="hasOverlap ? 'bg-red-400/70' : 'bg-green-400/70'"
                :style="selectionStyle"
            ></div>
        </div>
        <div
            class="mt-1 flex items-center justify-between text-[10px] text-gray-400"
        >
            <div class="flex gap-4">
                <span>00:00</span>
                <span>06:00</span>
                <span>12:00</span>
                <span>18:00</span>
                <span>24:00</span>
            </div>
            <div
                class="text-[11px]"
                :class="hasOverlap ? 'text-red-600' : 'text-gray-500'"
            >
                <span v-if="durationText">選択中: {{ durationText }}</span>
                <span v-if="hasOverlap" class="ml-2">重複しています</span>
            </div>
        </div>
    </div>
</template>
