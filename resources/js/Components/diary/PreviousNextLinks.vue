<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    previousLink: string;
    nextLink: string | null;
}>();
</script>

<template>
    <div class="flex justify-between">
        <Link :href="previousLink" :as="'button'">
            <button
                v-if="previousLink"
                class="text-md font-medium text-gray-500 hover:text-gray-700"
            >
                ← 前日
            </button>
        </Link>
        <Link :href="nextLink ?? ''" :as="'button'">
            <button
                v-if="nextLink"
                class="text-md font-medium text-gray-500 hover:text-gray-700"
            >
                翌日 →
            </button>
        </Link>
    </div>
</template>
