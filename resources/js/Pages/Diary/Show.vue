<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    diary: {
        date: string;
        content: string;
        created_at: string;
        updated_at: string;
    };
}>();
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
                    <div class="mt-5">
                        <p class="leading-6 text-gray-800">
                            {{ diary.content }}
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-4">
                    <Link
                        :href="route('diaries.edit', diary.date)"
                        :as="'button'"
                    >
                        <Button> 編集 </Button>
                    </Link>
                    <form
                        :action="route('diaries.destroy', diary.date)"
                        method="post"
                        onsubmit="return confirm('本当に削除しますか？');"
                    >
                        <input type="hidden" name="_method" value="delete" />
                        <Button variant="destructive" type="submit">
                            削除
                        </Button>
                    </form>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
