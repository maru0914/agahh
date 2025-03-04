<script setup lang="ts">
import ChatMessage from '@/Components/ChatMessage.vue';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Message } from '@/types/chat';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, nextTick, onMounted, ref } from 'vue';

const props = defineProps<{
    messagesInToday?: Message[];
}>();

const messages = ref<Message[]>([
    {
        id: 1,
        role: 'assistant',
        content: 'やあ！僕はアガーだよ',
    },
]);

const content = ref('');
const isComposing = ref(false); // IME入力中かどうか

const userAgent = navigator.userAgent.toLowerCase();
const isMac = computed(() => userAgent.includes('mac'));
const isWindows = computed(() => userAgent.includes('win'));

const sendShortcutMessage = computed(() => {
    if (isMac.value) return 'Cmd + Enter で送信';
    if (isWindows.value) return 'Ctrl + Enter で送信';
    return '';
});

const scrollToBottom = () => {
    nextTick(() => {
        window.scrollTo({
            top: document.documentElement.scrollHeight,
            behavior: 'smooth',
        });
    });
};

const sendMessage = async () => {
    if (!content.value.trim()) return;

    messages.value.push({
        id: messages.value.length + 1,
        role: 'user',
        content: content.value,
    });
    const sentMessage = content.value;
    content.value = '';

    // アガーからの返信
    const response = await axios.post('/api/chat', {
        content: sentMessage,
    });
    messages.value.push({
        id: messages.value.length + 1,
        role: 'assistant',
        content: response.data,
    });
    scrollToBottom();
};

const handleKeyDown = (event: KeyboardEvent) => {
    if (isComposing.value) return;

    if ((event.metaKey || event.ctrlKey) && event.key === 'Enter') {
        event.preventDefault();
        sendMessage();
    }
};

onMounted(() => {
    messages.value.push(
        ...(props.messagesInToday?.map((message, index) => ({
            id: index + 1,
            content: message.content,
            role: message.role,
        })) ?? []),
    );
});
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
                    @keydown="handleKeyDown"
                    @compositionstart="isComposing = true"
                    @compositionend="isComposing = false"
                />
                <div class="flex items-center justify-end gap-3">
                    <span
                        v-if="content"
                        class="text-right text-sm text-gray-500"
                    >
                        {{ sendShortcutMessage }}
                    </span>
                    <Button>送信</Button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
