<script setup lang="ts">
import ChatMessage from '@/Components/ChatMessage.vue';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Message } from '@/types/chat';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { nextTick, ref } from 'vue';

const messages = ref<Message[]>([
    {
        id: 1,
        isSent: false,
        content: 'やあ！僕はアガーだよ',
    },
]);

const content = ref('');

const scrollToBottom = () => {
    nextTick(() => {
        window.scrollTo({
            top: document.documentElement.scrollHeight,
            behavior: 'smooth',
        });
    });
};

const sendMessage = async () => {
    messages.value.push({
        id: messages.value.length + 1,
        isSent: true,
        content: content.value,
    });

    // アガーからの返信
    const response = await axios.post('/api/chat', {
        content: content.value,
    });
    content.value = '';
    messages.value.push({
        id: messages.value.length + 1,
        isSent: false,
        content: response.data,
    });
    scrollToBottom();
};
</script>

<template>
    <Head title="Welcome" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-3xl px-3">
            <div class="mt-10 flex flex-col items-center">
                <img
                    src="/images/agahh.jpg"
                    alt="Agahh"
                    class="h-32 w-32 rounded-full"
                />
                <h2 class="text-xl font-bold">アガー</h2>
            </div>
            <div class="mt-4 space-y-4">
                <ChatMessage
                    v-for="message in messages"
                    :message="message"
                    :key="message.id"
                />
            </div>
            <form @submit.prevent="sendMessage()" class="mt-4">
                <Textarea
                    class="my-4"
                    placeholder="アガーにメッセージを送る"
                    v-model="content"
                />
                <div class="flex justify-end">
                    <Button>送信</Button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
