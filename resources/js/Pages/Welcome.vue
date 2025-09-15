<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ChatMessage from '@/Components/ChatMessage.vue';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types';
import { Message } from '@/types/chat';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, nextTick, onMounted, ref } from 'vue';

const props = defineProps<{
    auth: {
        user: User;
    };
    messagesInToday?: Message[];
}>();
const buddy = computed(() => {
    return props.auth.user.buddy === 'agahh' ? 'アガー' : 'マル';
});

const messages = ref<Message[]>([
    {
        id: 1,
        role: 'assistant',
        content: `やあ、${buddy.value}だよ。元気？`,
    },
]);

const content = ref('');
const isComposing = ref(false); // IME入力中かどうか
const messagesContainer = ref<HTMLElement | null>(null);

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
        if (messagesContainer.value) {
            messagesContainer.value.scrollTo({
                top: messagesContainer.value.scrollHeight,
                behavior: 'smooth',
            });
        }
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

    // ユーザーメッセージ追加後にスクロール
    scrollToBottom();

    // アガーからの返信
    const response = await axios.post('/api/chat', {
        content: sentMessage,
    });
    messages.value.push({
        id: messages.value.length + 1,
        role: 'assistant',
        content: response.data,
    });

    // 返信後にもスクロール
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
    // 初期メッセージロード後にスクロール
    scrollToBottom();
});
</script>

<style scoped>
/* カスタムスクロールバー - 非表示 */
.scrollbar-hidden::-webkit-scrollbar {
    display: none;
}

.scrollbar-hidden {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<template>
    <Head title="Welcome" />
    <AuthenticatedLayout>
        <!-- シンプルなグラデーション背景 -->
        <div
            class="relative min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-purple-900 dark:to-blue-900"
        >
            <!-- 静的な装飾要素 -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute -right-10 -top-10 h-72 w-72 rounded-full bg-gradient-to-r from-purple-200 to-pink-200 opacity-30"
                ></div>
                <div
                    class="absolute bottom-10 left-1/4 h-64 w-64 rounded-full bg-gradient-to-r from-blue-200 to-purple-200 opacity-20"
                ></div>
            </div>

            <!-- チャットメッセージエリア（フル画面） -->
            <div
                class="h-[calc(100vh-130px)] border-b border-white/10 bg-white/85 backdrop-blur-lg dark:border-gray-700/10 dark:bg-gray-800/85"
            >
                <div
                    class="scrollbar-hidden h-full overflow-y-auto px-5 py-6"
                    ref="messagesContainer"
                >
                    <!-- ヘッダー部分（スクロール可能） -->
                    <div class="mb-10 py-6 text-center">
                        <div class="relative inline-block">
                            <ApplicationLogo
                                :buddy="$props.auth.user.buddy"
                                class="h-16 w-16 rounded-full shadow-xl ring-2 ring-white/80 transition-transform duration-300 hover:scale-105 dark:ring-gray-700/80"
                            />
                        </div>
                        <h1
                            class="mt-4 bg-gradient-to-r from-purple-600 via-purple-700 to-pink-600 bg-clip-text text-xl font-bold tracking-wide text-transparent"
                        >
                            {{ buddy }}
                        </h1>
                        <p
                            class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            今日も一緒に楽しく会話しましょう ✨
                        </p>
                    </div>

                    <!-- メッセージ一覧 -->
                    <div class="mx-auto max-w-4xl space-y-6">
                        <transition-group
                            enter-active-class="transition-all duration-500 ease-out"
                            enter-from-class="opacity-0 translate-y-4 scale-95"
                            leave-active-class="transition-all duration-300 ease-in"
                            leave-to-class="opacity-0 -translate-y-4 scale-95"
                            move-class="transition-transform duration-300 ease-in-out"
                            tag="div"
                            class="space-y-4"
                        >
                            <ChatMessage
                                v-for="message in messages"
                                :message="message"
                                :key="message.id"
                            />
                        </transition-group>
                    </div>

                    <!-- スクロール後の余白 -->
                    <div class="h-8"></div>
                </div>
            </div>

            <!-- 固定された入力フォーム -->
            <div
                class="via-white/98 dark:via-gray-900/98 fixed bottom-0 left-0 right-0 border-t border-gray-200/30 bg-gradient-to-t from-white to-white/90 p-5 shadow-2xl shadow-gray-900/5 backdrop-blur-xl dark:border-gray-700/30 dark:from-gray-900 dark:to-gray-900/90 dark:shadow-black/20"
            >
                <div class="mx-auto max-w-4xl">
                    <form @submit.prevent="sendMessage()">
                        <div class="relative">
                            <Textarea
                                class="bg-white/96 dark:bg-gray-800/96 resize-none rounded-2xl !border !border-gray-200/60 py-4 pr-16 font-medium leading-relaxed text-gray-800 shadow-lg backdrop-blur-md transition-all duration-300 placeholder:text-gray-400 hover:bg-white hover:shadow-xl focus:!border-2 focus:!border-purple-400 focus:shadow-2xl focus:!outline-none focus:!ring-0 focus-visible:!outline-none focus-visible:!ring-0 focus-visible:!ring-offset-0 dark:!border-gray-600/60 dark:text-gray-100 dark:placeholder:text-gray-500 dark:hover:bg-gray-800 dark:focus:!border-purple-500"
                                :placeholder="`${buddy}にメッセージを送る...`"
                                v-model="content"
                                @keydown="handleKeyDown"
                                @compositionstart="isComposing = true"
                                @compositionend="isComposing = false"
                                rows="3"
                            />

                            <!-- 送信ボタン（テキストエリア内） -->
                            <Button
                                type="submit"
                                class="absolute bottom-3 right-3 flex h-10 w-10 transform items-center justify-center rounded-full border-0 bg-gradient-to-br from-purple-500 via-purple-600 to-pink-500 text-white shadow-lg backdrop-blur-sm transition-all duration-200 hover:scale-105 hover:from-purple-600 hover:via-purple-700 hover:to-pink-600 hover:shadow-xl focus:outline-none active:scale-95 active:from-purple-700 active:via-purple-800 active:to-pink-700 disabled:scale-100 disabled:cursor-not-allowed disabled:opacity-20 disabled:hover:scale-100"
                                :disabled="!content.trim()"
                            >
                                <!-- 上矢印アイコン -->
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18"
                                    />
                                </svg>
                            </Button>

                            <!-- ショートカット表示（控えめ） -->
                            <transition
                                enter-active-class="transition-all duration-300 ease-out"
                                enter-from-class="opacity-0 scale-95 translate-y-1"
                                leave-active-class="transition-all duration-200 ease-in"
                                leave-to-class="opacity-0 scale-95 translate-y-1"
                            >
                                <div
                                    v-if="content && content.length > 0"
                                    class="absolute bottom-3 left-3 flex items-center gap-1 rounded-md bg-gray-50/80 px-2 py-1 text-xs font-medium text-gray-400 backdrop-blur-sm dark:bg-gray-800/60 dark:text-gray-500"
                                >
                                    <span class="text-xs">{{
                                        sendShortcutMessage
                                    }}</span>
                                </div>
                            </transition>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
