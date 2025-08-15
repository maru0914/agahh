<script setup lang="ts">
import TimelineDaySelector from '@/Components/studylog/TimelineDaySelector.vue';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps<{
    defaultDate: string;
    occupiedRanges?: Array<{ start: string; end: string }>;
}>();

const form = useForm({
    studied_at: props.defaultDate,
    subject: '',
    started_time: '' as string | null,
    ended_time: '' as string | null,
    notes: '' as string,
});

const occupied = computed(() => props.occupiedRanges ?? []);

watch(
    () => form.studied_at,
    (d) => {
        router.get(
            route('study-logs.create', { date: d }),
            {},
            {
                only: ['defaultDate', 'occupiedRanges'],
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    },
);

function submit() {
    form.post(route('study-logs.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="学習を記録" />
        <section class="relative py-8 sm:p-8">
            <div class="mx-auto w-full max-w-3xl px-4 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow">
                    <form @submit.prevent="submit">
                        <div class="grid gap-4">
                            <div>
                                <label
                                    for="studied_at"
                                    class="block text-sm text-gray-700"
                                    >学習日</label
                                >
                                <input
                                    id="studied_at"
                                    type="date"
                                    v-model="form.studied_at"
                                    class="mt-1 w-full rounded border border-gray-300 p-2"
                                />
                                <div
                                    v-if="form.errors.studied_at"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.studied_at }}
                                </div>
                            </div>
                            <div>
                                <label
                                    for="subject"
                                    class="block text-sm text-gray-700"
                                    >内容</label
                                >
                                <input
                                    id="subject"
                                    type="text"
                                    v-model="form.subject"
                                    class="mt-1 w-full rounded border border-gray-300 p-2"
                                    placeholder="例: 英語 単語学習"
                                />
                                <div
                                    v-if="form.errors.subject"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.subject }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700"
                                    >時間帯</label
                                >
                                <div class="mt-2">
                                    <TimelineDaySelector
                                        :startTime="form.started_time"
                                        :endTime="form.ended_time"
                                        :busyRanges="occupied"
                                        @update:startTime="
                                            (v) => (form.started_time = v)
                                        "
                                        @update:endTime="
                                            (v) => (form.ended_time = v)
                                        "
                                    />
                                </div>
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    <div class="flex items-center gap-2">
                                        <label
                                            class="w-10 text-xs text-gray-500"
                                            >開始</label
                                        >
                                        <input
                                            type="time"
                                            step="60"
                                            v-model="form.started_time"
                                            class="w-full rounded border border-gray-300 p-2"
                                        />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label
                                            class="w-10 text-xs text-gray-500"
                                            >終了</label
                                        >
                                        <input
                                            type="time"
                                            step="60"
                                            v-model="form.ended_time"
                                            class="w-full rounded border border-gray-300 p-2"
                                        />
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    タイムラインをドラッグ、もしくは時間欄で微調整してください。選択中の学習時間は上部に動的に表示されます。
                                </p>
                                <div
                                    v-if="form.errors.started_time"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.started_time }}
                                </div>
                                <div
                                    v-if="form.errors.ended_time"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.ended_time }}
                                </div>
                            </div>
                            <div>
                                <label
                                    for="notes"
                                    class="block text-sm text-gray-700"
                                    >メモ</label
                                >
                                <Textarea
                                    id="notes"
                                    rows="6"
                                    v-model="form.notes"
                                    placeholder="任意"
                                />
                                <div
                                    v-if="form.errors.notes"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.notes }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <Button type="submit">保存</Button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
