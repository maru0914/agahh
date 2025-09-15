<script setup lang="ts">
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Calendar, Clock } from 'lucide-vue-next';

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
                <div class="mb-5">
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4"
                        >
                            <h5
                                class="text-xl font-semibold leading-8 text-gray-900"
                            >
                                {{ year }}年{{ month }}月
                            </h5>
                            <div class="flex items-center gap-2">
                                <Link :href="previousLink" :as="'button'">
                                    <Button
                                        variant="outline"
                                        class="px-3 py-1 text-sm"
                                        >前へ</Button
                                    >
                                </Link>
                                <Link :href="nextLink" :as="'button'">
                                    <Button
                                        variant="outline"
                                        class="px-3 py-1 text-sm"
                                        >次へ</Button
                                    >
                                </Link>
                                <Link
                                    v-if="!isCurrentYearMonth"
                                    :href="route('study-logs.index')"
                                    :as="'button'"
                                >
                                    <Button
                                        variant="outline"
                                        class="px-3 py-1 text-sm"
                                        >今月</Button
                                    >
                                </Link>
                            </div>
                        </div>
                        <Link :href="route('study-logs.create')" :as="'button'">
                            <Button class="w-full sm:w-auto">学習を記録</Button>
                        </Link>
                    </div>
                </div>

                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Clock class="h-5 w-5 text-blue-600" />
                            合計学習時間
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ formatMinutes(totalMinutes) }}
                        </p>
                    </CardContent>
                </Card>

                <div v-if="logs.length" class="space-y-4">
                    <Link
                        v-for="log in logs"
                        :key="log.id"
                        :href="route('study-logs.show', log.id)"
                        :as="'button'"
                        class="block w-full"
                    >
                        <Card
                            class="transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
                        >
                            <CardContent class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="mb-3 flex items-center gap-3"
                                        >
                                            <Badge
                                                variant="outline"
                                                class="flex items-center gap-1"
                                            >
                                                <Calendar class="h-3 w-3" />
                                                {{ log.studied_at }}
                                            </Badge>
                                            <Badge
                                                v-if="
                                                    log.started_at &&
                                                    log.ended_at
                                                "
                                                variant="secondary"
                                                class="flex items-center gap-1"
                                            >
                                                <Clock class="h-3 w-3" />
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
                                            </Badge>
                                        </div>
                                        <div
                                            class="mb-3 flex items-center gap-2"
                                        >
                                            <BookOpen
                                                class="h-5 w-5 text-gray-600"
                                            />
                                            <h3
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{ log.subject }}
                                            </h3>
                                        </div>
                                        <div
                                            v-if="
                                                log.started_at && log.ended_at
                                            "
                                            class="h-2 w-full max-w-md rounded-full bg-gray-100"
                                        >
                                            <div
                                                class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-blue-600"
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
                                    <div class="ml-6 flex-shrink-0 text-right">
                                        <div
                                            class="text-2xl font-bold text-blue-600"
                                        >
                                            {{
                                                formatMinutes(
                                                    log.duration_minutes,
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
                <Card v-else class="py-12 text-center">
                    <CardContent>
                        <BookOpen
                            class="mx-auto mb-4 h-12 w-12 text-gray-400"
                        />
                        <p class="text-lg text-gray-500">
                            この月の学習記録はありません。
                        </p>
                        <Link
                            :href="route('study-logs.create')"
                            :as="'button'"
                            class="mt-4 inline-block"
                        >
                            <Button>最初の学習を記録する</Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
