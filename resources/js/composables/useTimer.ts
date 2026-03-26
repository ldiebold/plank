import { ref, computed, type ComputedRef } from 'vue';

export interface UseTimerReturn {
    elapsedSeconds: ReturnType<typeof ref<number>>;
    isRunning: ReturnType<typeof ref<boolean>>;
    isPaused: ReturnType<typeof ref<boolean>>;
    isComplete: ReturnType<typeof ref<boolean>>;
    formattedTime: ComputedRef<string>;
    startTimer: () => void;
    pauseTimer: () => void;
    resumeTimer: () => void;
    stopTimer: () => void;
    resetTimer: () => void;
    countdown: (seconds: number) => Promise<void>;
}

export function useTimer(): UseTimerReturn {
    const elapsedSeconds = ref(0);
    const isRunning = ref(false);
    const isPaused = ref(false);
    const isComplete = ref(false);

    let intervalId: ReturnType<typeof setInterval> | null = null;
    let startTime = 0;
    let elapsedBeforePause = 0;

    const formattedTime = computed(() => {
        const minutes = Math.floor(elapsedSeconds.value / 60);
        const seconds = elapsedSeconds.value % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    });

    function startTimer() {
        if (isRunning.value) return;

        isRunning.value = true;
        isPaused.value = false;
        isComplete.value = false;
        startTime = Date.now() - elapsedBeforePause * 1000;

        intervalId = setInterval(() => {
            const now = Date.now();
            elapsedSeconds.value = Math.floor((now - startTime) / 1000);
        }, 100);
    }

    function pauseTimer() {
        if (!isRunning.value || isPaused.value) return;

        isPaused.value = true;
        elapsedBeforePause = elapsedSeconds.value;

        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }

    function resumeTimer() {
        if (!isPaused.value) return;

        isPaused.value = false;
        startTime = Date.now() - elapsedBeforePause * 1000;

        intervalId = setInterval(() => {
            const now = Date.now();
            elapsedSeconds.value = Math.floor((now - startTime) / 1000);
        }, 100);
    }

    function stopTimer() {
        isRunning.value = false;
        isPaused.value = false;
        isComplete.value = true;

        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }

    function resetTimer() {
        stopTimer();
        elapsedSeconds.value = 0;
        elapsedBeforePause = 0;
        isComplete.value = false;
    }

    function countdown(seconds: number): Promise<void> {
        return new Promise((resolve) => {
            let remaining = seconds;

            const countInterval = setInterval(() => {
                remaining--;

                if (remaining <= 0) {
                    clearInterval(countInterval);
                    resolve();
                }
            }, 1000);
        });
    }

    return {
        elapsedSeconds,
        isRunning,
        isPaused,
        isComplete,
        formattedTime,
        startTimer,
        pauseTimer,
        resumeTimer,
        stopTimer,
        resetTimer,
        countdown,
    };
}
