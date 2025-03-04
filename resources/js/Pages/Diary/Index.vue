<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

import { Button } from '@/Components/ui/button';
import { CalendarDate } from '@/types/calendar';

const weekDays = ['月', '火', '水', '木', '金', '土', '日'];

const props = defineProps<{
    year: number;
    month: number;
    isCurrentYearMonth: boolean;
    previousLink: string;
    nextLink: string;
    calendarDates?: CalendarDate[];
}>();

const formatDate = (date: number) => {
    const padZero = (value: number) => String(value).padStart(2, '0');
    return `${props.year}-${padZero(props.month)}-${padZero(date)}`;
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="日記" />
        <section class="relative py-8 sm:p-8">
            <div class="mx-auto w-full max-w-7xl px-4 lg:px-8 xl:px-14">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <h5
                            class="text-xl font-semibold leading-8 text-gray-900"
                        >
                            {{ year }}年{{ month }}月
                        </h5>
                        <div class="flex items-center gap-2">
                            <Link :href="previousLink" :as="'button'">
                                <ChevronLeft
                                    class="cursor-pointer rounded text-gray-500 transition-all duration-300 hover:bg-gray-100 hover:text-gray-900"
                                />
                            </Link>
                            <Link :href="nextLink" :as="'button'">
                                <ChevronRight
                                    class="cursor-pointer rounded text-gray-500 transition-all duration-300 hover:bg-gray-100 hover:text-gray-900"
                                />
                            </Link>
                        </div>
                        <Link
                            v-if="!isCurrentYearMonth"
                            :href="route('diaries.index')"
                            :as="'button'"
                        >
                            <Button variant="outline"> 今月</Button>
                        </Link>
                    </div>
                </div>
                <div class="border border-gray-200">
                    <div
                        class="grid grid-cols-7 divide-gray-200 border-b border-gray-200 bg-gray-50"
                    >
                        <div
                            class="flex justify-center border-r border-gray-200 p-3.5"
                            v-for="(day, index) in weekDays"
                            :key="index"
                        >
                            <span class="text-sm font-medium text-gray-500">
                                {{ day }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 divide-gray-200">
                        <Link
                            v-for="calendarDate in calendarDates"
                            :key="calendarDate.id"
                            :href="
                                calendarDate.diaryId
                                    ? route(
                                          'diaries.show',
                                          formatDate(calendarDate.date),
                                      )
                                    : route(
                                          'diaries.create',
                                          formatDate(calendarDate.date),
                                      )
                            "
                            :as="'button'"
                        >
                            <div
                                class="flex min-h-[70px] flex-col justify-between border-b border-r border-gray-200 p-2 transition-all duration-300 max-lg:items-center md:min-h-[80px] lg:h-28 xl:aspect-auto"
                                :class="{
                                    'cursor-pointer bg-gray-50 hover:bg-white':
                                        calendarDate.isCurrentMonth,
                                    'bg-gray-300/20':
                                        !calendarDate.isCurrentMonth,
                                }"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold text-gray-500"
                                    :class="{
                                        'bg-green-200': calendarDate.isToday,
                                    }"
                                >
                                    {{ calendarDate.date }}
                                </span>
                                <div
                                    v-if="calendarDate.diaryId"
                                    class="flex justify-end"
                                >
                                    <img
                                        src="/images/agahh-icon.png"
                                        alt="application logo"
                                        class="h-6 w-6 md:h-8 md:w-8"
                                    />
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
