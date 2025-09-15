<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, ChevronLeft, ChevronRight, PenTool } from 'lucide-vue-next';

import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { CalendarDate } from '@/types/calendar';

const weekDays = ['月', '火', '水', '木', '金', '土', '日'];

defineProps<{
    year: number;
    month: number;
    isCurrentYearMonth: boolean;
    previousLink: string;
    nextLink: string;
    calendarDates?: CalendarDate[];
}>();

const getDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.getDate();
};

const isTodayOrBefore = (calendarDate: CalendarDate) => {
    return calendarDate.date <= new Date().toISOString().slice(0, 10);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="日記" />
        <section class="relative py-8 sm:p-8">
            <div class="mx-auto w-full max-w-7xl px-4 lg:px-8 xl:px-14">
                <Card class="mb-6">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center gap-3">
                                <Calendar class="h-6 w-6 text-green-600" />
                                {{ year }}年{{ month }}月の日記
                            </CardTitle>
                            <div class="flex items-center gap-2">
                                <Link :href="previousLink" :as="'button'">
                                    <Button variant="outline" size="icon">
                                        <ChevronLeft class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Link :href="nextLink" :as="'button'">
                                    <Button variant="outline" size="icon">
                                        <ChevronRight class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Link
                                    v-if="!isCurrentYearMonth"
                                    :href="route('diaries.index')"
                                    :as="'button'"
                                >
                                    <Button variant="outline">今月</Button>
                                </Link>
                            </div>
                        </div>
                    </CardHeader>
                </Card>
                <Card>
                    <CardContent class="p-0">
                        <div class="grid grid-cols-7 border-b">
                            <div
                                class="flex justify-center border-r bg-gradient-to-b from-slate-50 to-slate-100 p-4"
                                v-for="(day, index) in weekDays"
                                :key="index"
                                :class="{ 'border-r-0': index === 6 }"
                            >
                                <span
                                    class="text-sm font-semibold text-slate-600"
                                >
                                    {{ day }}
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-7">
                            <Link
                                v-for="calendarDate in calendarDates"
                                :key="calendarDate.id"
                                :href="
                                    calendarDate.diaryId
                                        ? route(
                                              'diaries.show',
                                              calendarDate.date,
                                          )
                                        : route(
                                              'diaries.create',
                                              calendarDate.date,
                                          )
                                "
                                :as="'button'"
                                :disabled="!isTodayOrBefore(calendarDate)"
                            >
                                <div
                                    class="group relative flex min-h-[80px] flex-col justify-between border-b border-r p-3 transition-all duration-300 hover:bg-gradient-to-br hover:from-blue-50 hover:to-indigo-50 md:min-h-[90px] lg:h-32"
                                    :class="{
                                        'cursor-pointer':
                                            calendarDate.isCurrentMonth &&
                                            isTodayOrBefore(calendarDate),
                                        'bg-slate-50/50':
                                            !calendarDate.isCurrentMonth,
                                        'border-r-0': calendarDate.id % 7 === 0,
                                        'border-b-0':
                                            calendarDate.id >
                                            calendarDates!.length - 7,
                                    }"
                                >
                                    <div
                                        class="flex items-start justify-between"
                                    >
                                        <Badge
                                            :variant="
                                                calendarDate.isToday
                                                    ? 'default'
                                                    : 'outline'
                                            "
                                            :class="{
                                                'bg-green-500 hover:bg-green-600':
                                                    calendarDate.isToday,
                                                'text-slate-400':
                                                    !calendarDate.isCurrentMonth,
                                            }"
                                            class="text-xs font-medium"
                                        >
                                            {{ getDate(calendarDate.date) }}
                                        </Badge>
                                        <PenTool
                                            v-if="calendarDate.diaryId"
                                            class="h-4 w-4 text-green-600 opacity-70 transition-opacity group-hover:opacity-100"
                                        />
                                    </div>
                                    <div
                                        v-if="calendarDate.diaryId"
                                        class="flex justify-end opacity-80 transition-opacity group-hover:opacity-100"
                                    >
                                        <ApplicationLogo
                                            :buddy="$page.props.auth.user.buddy"
                                            class="h-8 w-8 md:h-10 md:w-10"
                                        />
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
