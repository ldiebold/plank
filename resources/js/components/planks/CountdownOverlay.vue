<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    seconds: number;
    isActive: boolean;
}>();

const emit = defineEmits<{
    complete: [];
}>();

const currentCount = ref(props.seconds);
const showGo = ref(false);

// Audio context for beep
let audioContext: AudioContext | null = null;

function playBeep() {
    try {
        if (!audioContext) {
            audioContext = new (
                window.AudioContext || (window as any).webkitAudioContext
            )();
        }

        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.value = 800;
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(
            0.01,
            audioContext.currentTime + 0.5,
        );

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
    } catch (e) {
        console.warn('Audio not supported:', e);
    }
}

watch(
    () => props.isActive,
    (active) => {
        if (active) {
            currentCount.value = props.seconds;
            showGo.value = false;
            startCountdown();
        }
    },
);

function startCountdown() {
    const interval = setInterval(() => {
        currentCount.value--;

        if (currentCount.value <= 0) {
            clearInterval(interval);
            showGo.value = true;
            playBeep(); // Beep when GO! appears
            setTimeout(() => {
                showGo.value = false;
                emit('complete');
            }, 1000);
        }
    }, 1000);
}
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        leave-active-class="transition-opacity duration-500"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isActive && (currentCount > 0 || showGo)"
            class="fixed inset-0 z-50 flex items-center justify-center bg-background/95 backdrop-blur-sm"
        >
            <Transition
                mode="out-in"
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="scale-50 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition-all duration-300 ease-in"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-150 opacity-0"
            >
                <div
                    v-if="!showGo"
                    :key="currentCount"
                    class="text-9xl font-bold"
                    :class="currentCount <= 1 ? 'text-red-500' : 'text-primary'"
                >
                    {{ currentCount }}
                </div>
                <div v-else key="go" class="text-9xl font-bold text-green-500">
                    GO!
                </div>
            </Transition>
        </div>
    </Transition>
</template>
