<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    seconds: number;
    targetSeconds: number;
    isRunning: boolean;
}>();

const displayTime = computed(() => {
    const minutes = Math.floor(props.seconds / 60);
    const secs = props.seconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
});

// Progress based on target time (100% when target reached)
const progress = computed(() => {
    const target = props.targetSeconds || 60;
    return Math.min((props.seconds / target) * 100, 100);
});

// Color changes based on progress
const statusColor = computed(() => {
    if (props.seconds >= props.targetSeconds) return 'text-green-500';
    if (props.isRunning) return 'text-primary';
    return 'text-muted-foreground';
});

const statusText = computed(() => {
    if (props.seconds >= props.targetSeconds) return 'Target Reached!';
    if (props.isRunning) return 'Recording';
    return 'Ready';
});
</script>

<template>
    <div class="relative flex flex-col items-center justify-center">
        <!-- Progress Ring Background -->
        <div class="relative h-64 w-64">
            <svg
                class="h-full w-full -rotate-90 transform"
                viewBox="0 0 100 100"
            >
                <!-- Background circle -->
                <circle
                    cx="50"
                    cy="50"
                    r="45"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="4"
                    class="text-muted/20"
                />
                <!-- Progress circle -->
                <circle
                    cx="50"
                    cy="50"
                    r="45"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="4"
                    stroke-linecap="round"
                    :stroke-dasharray="283"
                    :stroke-dashoffset="283 - (283 * progress) / 100"
                    class="transition-all duration-500"
                    :class="statusColor"
                />
            </svg>

            <!-- Time Display -->
            <div
                class="absolute inset-0 flex flex-col items-center justify-center"
            >
                <span
                    class="font-mono text-6xl font-bold tracking-tighter"
                    :class="statusColor"
                >
                    {{ displayTime }}
                </span>
                <span class="mt-2 text-sm text-muted-foreground">
                    {{ statusText }}
                </span>
                <span
                    v-if="seconds >= targetSeconds"
                    class="mt-1 text-xs font-medium text-green-500"
                >
                    Keep going!
                </span>
            </div>
        </div>
    </div>
</template>
