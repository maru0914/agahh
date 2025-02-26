<script setup lang="ts">
import { Message } from '@/types/chat';

defineProps<{
    message: Message;
}>();
</script>

<template>
    <div class="flex" :class="{ 'justify-end': message.isSent }">
        <img
            v-if="!message.isSent"
            src="/images/agahh.jpg"
            alt="Agahh"
            class="mr-3 h-12 w-12 rounded-full"
        />
        <div
            class="leading-1.5 relative flex w-full max-w-[320px] flex-col rounded-e-xl rounded-s-xl border-gray-200 p-4"
            :class="{
                'bg-blue-100': message.isSent,
                'bg-gray-100': !message.isSent,
            }"
        >
            <div class="flex items-center">
                <p
                    class="whitespace-pre-wrap py-2.5 text-sm font-normal text-gray-900 dark:text-white"
                >
                    {{ message.content }}
                </p>
            </div>
        </div>
    </div>
</template>
