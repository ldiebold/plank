<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { Timer } from 'lucide-vue-next';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <Timer :class="className" v-bind="$attrs" />
</template>
