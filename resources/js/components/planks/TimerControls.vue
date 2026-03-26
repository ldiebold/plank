<script setup lang="ts">
import { Play, RotateCcw, CheckCircle2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    isRunning: boolean;
    hasCompletedToday: boolean;
}>();

const emit = defineEmits<{
    start: [];
    stop: [];
    complete: [];
}>();
</script>

<template>
    <div class="flex flex-wrap items-center justify-center gap-4">
        <!-- Start Button (only when not running) -->
        <Button
            v-if="!isRunning"
            size="lg"
            :disabled="hasCompletedToday"
            @click="$emit('start')"
            class="min-w-[160px]"
        >
            <Play class="mr-2 h-5 w-5" />
            Start
        </Button>

        <!-- Complete Button (only when running) -->
        <Button
            v-else
            size="lg"
            class="min-w-[160px] bg-green-600 hover:bg-green-700"
            @click="$emit('complete')"
        >
            <CheckCircle2 class="mr-2 h-5 w-5" />
            Complete
        </Button>

        <!-- Reset Button -->
        <Button
            size="lg"
            variant="outline"
            @click="$emit('stop')"
            class="min-w-[160px]"
        >
            <RotateCcw class="mr-2 h-5 w-5" />
            Reset
        </Button>
    </div>
</template>
