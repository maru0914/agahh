<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PreviousNextLinks from '@/Components/diary/PreviousNextLinks.vue';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Diary } from '@/types/diary';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    auth: {
        user: {
            buddy: string;
        };
    };
    diary: Diary;
    previousLink: string;
    nextLink: string | null;
}>();

const buddy = computed(() => {
    return props.auth.user.buddy === 'agahh' ? 'アガー' : 'マル';
});

const form = useForm({
    content: props.diary.content,
});

const isEditing = ref(false);

const addDiaryComment = () => {
    router.post(route('diaries.comments.store', props.diary.date));
};

const updateDiary = () => {
    isEditing.value = false;
    form.patch(route('diaries.update', props.diary.date));
};

const deleteDiary = () => {
    if (!confirm('本当に削除しますか？')) {
        return;
    }
    router.delete(route('diaries.destroy', props.diary.date));
};

const dayOfWeek = computed(() => {
    const dateObject = new Date(props.diary.date);
    const options: Intl.DateTimeFormatOptions = { weekday: 'short' };
    return new Intl.DateTimeFormat('ja-JP', options).format(dateObject);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`${diary.date}の日記`" />
        <section class="relative py-8 sm:p-8">
            <div class="mx-auto w-full max-w-3xl px-4 lg:px-8">
                <PreviousNextLinks
                    :previousLink="previousLink"
                    :nextLink="nextLink"
                />
                <h1 class="mt-4 text-2xl font-semibold text-gray-900">
                    {{ diary.date }} ({{ dayOfWeek }})
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
                                <Textarea rows="15" v-model="form.content" />
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
                            <p
                                class="whitespace-pre-wrap leading-6 text-gray-800"
                            >
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
            <div v-if="diary.comment">
                <div class="mx-auto mt-4 w-full max-w-3xl px-4 lg:px-8">
                    <div class="mt-4 rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center">
                            <div class="mr-4 min-w-8 max-w-10">
                                <ApplicationLogo
                                    :buddy="$page.props.auth.user.buddy"
                                />
                            </div>
                            <p
                                class="mt-1 whitespace-pre-wrap rounded-md bg-lime-100 px-3 py-2 text-sm text-gray-800"
                            >
                                {{ diary.comment }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="mt-4 flex justify-center">
                <form @submit.prevent="addDiaryComment">
                    <Button type="submit">{{ buddy }}に見てもらう</Button>
                </form>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
