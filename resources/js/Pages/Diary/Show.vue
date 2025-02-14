<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Diary } from '@/types/diary';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    diary: Diary;
}>();

const form = useForm({
    content: props.diary.content,
});

const isEditing = ref(false);

const updateDiary = () => {
    isEditing.value = false;
    console.log(props.diary);
    form.patch(route('diaries.update', props.diary.date));
};

const deleteDiary = () => {
    if (!confirm('本当に削除しますか？')) {
        return;
    }
    router.delete(route('diaries.destroy', props.diary.date));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`${diary.date}の日記`" />
        <section class="relative py-8 sm:p-8">
            <div class="mx-auto w-full max-w-3xl px-4 lg:px-8">
                <Link :href="route('diaries.index')" :as="'button'">
                    <button
                        class="text-sm font-medium text-gray-500 hover:text-gray-700"
                    >
                        ← 一覧に戻る
                    </button>
                </Link>
                <h1 class="mt-4 text-2xl font-semibold text-gray-900">
                    {{ diary.date }}
                </h1>
                <div class="mt-4 rounded-lg bg-white p-6 shadow">
                    <p class="mt-1 text-end text-sm text-gray-500">
                        作成日時: {{ diary.created_at }}
                    </p>
                    <p class="mt-1 text-end text-sm text-gray-500">
                        更新日時: {{ diary.updated_at }}
                    </p>
                    <template v-if="isEditing">
                        <form @submit.prevent="updateDiary">
                            <div class="mt-5">
                                <Textarea v-model="form.content" />
                                <div v-if="form.errors.content">
                                    {{ form.errors.content }}
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="isEditing = false"
                                >
                                    キャンセル
                                </Button>
                                <Button type="submit">保存</Button>
                            </div>
                        </form>
                    </template>

                    <template v-else>
                        <div class="mt-5">
                            <p class="leading-6 text-gray-800">
                                {{ diary.content }}
                            </p>
                        </div>
                        <div class="mt-6 flex justify-end gap-4">
                            <Button @click="isEditing = true">編集</Button>
                            <form @submit.prevent="deleteDiary">
                                <Button variant="destructive" type="submit">
                                    削除
                                </Button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
