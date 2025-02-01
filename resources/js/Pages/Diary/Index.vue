<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Diary } from '@/types/diary';

const props = defineProps<{
    diaries: Diary[];
}>();
</script>

<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-4">
            <div class="flex flex-col">
                <div class="w-full">
                    <table class="min-w-full border border-gray-200 bg-white">
                        <thead>
                            <tr>
                                <th class="border-b px-4 py-2">日付</th>
                                <th class="border-b px-4 py-2">本文</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="diary in props.diaries"
                                :key="diary.id"
                                class="hover:bg-gray-100"
                            >
                                <td class="border-b px-4 py-2">
                                    {{ diary.date }}
                                </td>
                                <td class="border-b px-4 py-2">
                                    {{ diary.content }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
