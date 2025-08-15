<script setup lang="ts">
import TimelineDaySelector from '@/Components/studylog/TimelineDaySelector.vue';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    log: {
        id: number;
        studied_at: string;
        subject: string;
        duration_minutes: number;
        started_at?: string | null;
        ended_at?: string | null;
        notes?: string | null;
    };
}>();

const isEditing = ref(false);

const form = useForm({
    studied_at: props.log.studied_at,
    subject: props.log.subject,
    started_time: (props.log.started_at ?? '').toString().slice(11, 16) as any,
    ended_time: (props.log.ended_at ?? '').toString().slice(11, 16) as any,
    notes: props.log.notes ?? '',
});

function updateLog() {
    isEditing.value = false;
    form.patch(route('study-logs.update', props.log.id));
}

function deleteLog() {
    if (!confirm('本当に削除しますか？')) return;
    router.delete(route('study-logs.destroy', props.log.id));
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="学習記録" />
        <section class="relative py-8 sm:p-8">
            <div class="mx-auto w-full max-w-3xl px-4 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow">
                    <template v-if="isEditing">
                        <form @submit.prevent="updateLog">
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
                                            @update:startTime="
                                                (v) => (form.started_time = v)
                                            "
                                            @update:endTime="
                                                (v) => (form.ended_time = v)
                                            "
                                        />
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">
                                        1日のタイムライン上をドラッグして学習時間帯を選択してください。学習時間は自動計算されます。
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
                                    />
                                    <div
                                        v-if="form.errors.notes"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.notes }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="isEditing = false"
                                    >キャンセル</Button
                                >
                                <Button type="submit">保存</Button>
                            </div>
                        </form>
                    </template>
                    <template v-else>
                        <div class="grid gap-2">
                            <p class="text-sm text-gray-500">
                                {{ props.log.studied_at }}
                            </p>
                            <h1 class="text-2xl font-semibold text-gray-900">
                                {{ props.log.subject }}
                            </h1>
                            <p class="text-gray-700">
                                <span
                                    v-if="
                                        props.log.started_at &&
                                        props.log.ended_at
                                    "
                                >
                                    時間帯:
                                    {{
                                        (props.log.started_at as any)
                                            .toString()
                                            .slice(11, 16)
                                    }}
                                    -
                                    {{
                                        (props.log.ended_at as any)
                                            .toString()
                                            .slice(11, 16)
                                    }}
                                </span>
                                <span v-else>
                                    時間: {{ props.log.duration_minutes }}分
                                </span>
                            </p>
                            <p class="whitespace-pre-wrap text-gray-800">
                                {{ props.log.notes }}
                            </p>
                        </div>
                        <div class="mt-6 flex justify-end gap-4">
                            <Button @click="isEditing = true">編集</Button>
                            <form @submit.prevent="deleteLog">
                                <Button variant="destructive" type="submit"
                                    >削除</Button
                                >
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
