<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ date: string }>();

const form = useForm({
    content: '',
});

const yearMonth = computed(() => {
    const dateObject = new Date(props.date);
    const year = dateObject.getFullYear();
    const month = String(dateObject.getMonth() + 1).padStart(2, '0');
    return year + month;
});

const dayOfWeek = computed(() => {
    const dateObject = new Date(props.date);
    const options: Intl.DateTimeFormatOptions = { weekday: 'short' };
    return new Intl.DateTimeFormat('ja-JP', options).format(dateObject);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`${date} - 日記作成`" />
        <section class="relative py-8 sm:p-8">
            <div class="mx-auto w-full max-w-3xl px-4 lg:px-8">
                <Link
                    :href="route('diaries.index', { yearMonth })"
                    :as="'button'"
                >
                    <button
                        class="text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        ← 一覧に戻る
                    </button>
                </Link>
                <h1 class="mt-4 text-2xl font-semibold text-gray-900">
                    {{ date }} ({{ dayOfWeek }})
                </h1>
                <div class="mt-4 rounded-lg bg-white p-6 shadow">
                    <div class="mt-5">
                        <form
                            @submit.prevent="
                                form.post(route('diaries.store', { date }))
                            "
                        >
                            <Textarea
                                class="my-4"
                                rows="15"
                                placeholder="今日はどんな1日だった？"
                                v-model="form.content"
                            />
                            <div v-if="form.errors.content">
                                {{ form.errors.content }}
                            </div>
                            <div class="flex justify-end">
                                <Button type="submit">保存</Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
