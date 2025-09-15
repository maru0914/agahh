<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Message } from '@/types/chat';
import { computed } from 'vue';

const props = defineProps<{
    message: Message;
}>();

const isSent = computed(() => props.message.role === 'user');
</script>

<template>
    <div class="group flex" :class="{ 'justify-end': isSent }">
        <!-- アシスタントのアバター -->
        <div v-if="!isSent" class="mr-3 flex-shrink-0">
            <ApplicationLogo
                :buddy="$page.props.auth.user.buddy"
                class="h-10 w-10 rounded-full shadow-lg ring-2 ring-purple-200 transition-transform duration-200 group-hover:scale-105 dark:ring-purple-700"
            />
        </div>

        <!-- メッセージバブル -->
        <div
            class="relative max-w-[85%] transform transition-all duration-300 hover:scale-[1.01] sm:max-w-[75%] md:max-w-[65%]"
            :class="{
                'order-1': isSent,
                'order-2': isSent,
            }"
        >
            <!-- メッセージコンテンツ -->
            <div
                class="rounded-2xl border-0 px-5 py-4 shadow-md transition-all duration-300"
                :class="{
                    'bg-gradient-to-br from-purple-500 to-pink-500 text-white shadow-purple-200/40 dark:shadow-purple-900/40':
                        isSent,
                    'bg-white/98 dark:bg-gray-700/98 text-gray-800 shadow-gray-200/40 backdrop-blur-sm dark:text-gray-100 dark:shadow-gray-800/40':
                        !isSent,
                }"
            >
                <p
                    class="whitespace-pre-wrap text-sm font-medium leading-relaxed tracking-wide"
                >
                    {{ message.content }}
                </p>
            </div>
        </div>
    </div>
</template>
