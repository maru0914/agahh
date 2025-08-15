<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    year: number;
    month: number;
    isCurrentYearMonth: boolean;
    previousLink: string;
    nextLink: string;
    logs: Array<{
        id: number;
        studied_at: string;
        subject: string;
        duration_minutes: number;
        notes?: string | null;
        started_at?: string | null;
        ended_at?: string | null;
    }>;
    totalMinutes: number;
}>();

function formatMinutes(mins: number) {
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    if (h === 0) return `${m}分`;
    if (m === 0) return `${h}時間`;
    return `${h}時間${m}分`;
}
function minutesFromDateTime(dt: string): number {
    // dt expected like 'YYYY-MM-DD HH:MM' or ISO; take the time portion safely
    const time = dt.slice(11, 16);
    const [h, m] = time.split(':').map((n) => parseInt(n, 10));
    if (Number.isFinite(h) && Number.isFinite(m)) {
        return h * 60 + m;
    }
    // Fallback: try Date parsing if format unexpected
    const d = new Date(dt as any);
    if (!isNaN(d.getTime())) {
        return d.getHours() * 60 + d.getMinutes();
    }
    return 0;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="学習記録" />
        <section class="relative py-8 sm:p-8">
            <div class="mx-auto w-full max-w-5xl px-4 lg:px-8">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <h5
                            class="text-xl font-semibold leading-8 text-gray-900"
                        >
                            {{ year }}年{{ month }}月
                        </h5>
                        <div class="flex items-center gap-2">
                            <Link :href="previousLink" :as="'button'">
                                <Button variant="outline">前へ</Button>
                            </Link>
                            <Link :href="nextLink" :as="'button'">
                                <Button variant="outline">次へ</Button>
                            </Link>
                            <Link
                                v-if="!isCurrentYearMonth"
                                :href="route('study-logs.index')"
                                :as="'button'"
                            >
                                <Button variant="outline">今月</Button>
                            </Link>
                        </div>
                    </div>
                    <Link :href="route('study-logs.create')" :as="'button'">
                        <Button>学習を記録</Button>
                    </Link>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-gray-700">合計学習時間</p>
                        <p class="text-lg font-semibold">
                            {{ formatMinutes(totalMinutes) }}
                        </p>
                    </div>

                    <div v-if="logs.length" class="divide-y">
                        <Link
                            v-for="log in logs"
                            :key="log.id"
                            :href="route('study-logs.show', log.id)"
                            :as="'button'"
                        >
                            <div
                                class="flex items-center justify-between py-3 text-left"
                            >
                                <div>
                                    <p class="text-sm text-gray-500">
                                        {{ log.studied_at }}
                                        <span
                                            v-if="
                                                log.started_at && log.ended_at
                                            "
                                            class="ml-2 text-xs text-gray-400"
                                        >
                                            {{
                                                (log.started_at as any)
                                                    .toString()
                                                    .slice(11, 16)
                                            }}
                                            -
                                            {{
                                                (log.ended_at as any)
                                                    .toString()
                                                    .slice(11, 16)
                                            }}
                                        </span>
                                    </p>
                                    <p class="font-medium text-gray-900">
                                        {{ log.subject }}
                                    </p>
                                    <div
                                        v-if="log.started_at && log.ended_at"
                                        class="mt-2 h-2 w-full rounded bg-gray-100"
                                    >
                                        <div
                                            class="h-2 rounded bg-green-400"
                                            :style="{
                                                width:
                                                    ((minutesFromDateTime(
                                                        log.ended_at as any,
                                                    ) -
                                                        minutesFromDateTime(
                                                            log.started_at as any,
                                                        )) /
                                                        1440) *
                                                        100 +
                                                    '%',
                                                marginLeft:
                                                    (minutesFromDateTime(
                                                        log.started_at as any,
                                                    ) /
                                                        1440) *
                                                        100 +
                                                    '%',
                                            }"
                                        />
                                    </div>
                                </div>
                                <div class="text-gray-700">
                                    {{ formatMinutes(log.duration_minutes) }}
                                </div>
                            </div>
                        </Link>
                    </div>
                    <div v-else class="text-center text-gray-500">
                        この月の学習記録はありません。
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
