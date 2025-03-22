<template>
    <img :src="imageSrc" alt="application logo" />
</template>
<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{
    buddy: 'agahh' | 'maru';
}>();

const imageSrc =
    props.buddy === 'maru' ? '/images/maru-icon.png' : '/images/agahh-icon.png';
</script>
