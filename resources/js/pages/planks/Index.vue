<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Play, CheckCircle2, Trophy, RotateCcw } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import planks from '@/routes/planks';
import Heading from '@/components/Heading.vue';
import TimerDisplay from '@/components/planks/TimerDisplay.vue';
import TimerControls from '@/components/planks/TimerControls.vue';
import CountdownOverlay from '@/components/planks/CountdownOverlay.vue';
import { useTimer } from '@/composables/useTimer';
import { formatDuration } from '@/lib/utils';

const props = defineProps<{
    challenge: {
        id: number;
        name: string;
    };
    todayTarget: number;
    countdownSeconds: number;
    hasCompletedToday: boolean;
}>();

const { elapsedSeconds, isRunning, formattedTime, startTimer, stopTimer } =
    useTimer();

const showCountdown = ref(false);
const showCompleteDialog = ref(false);
const isTimerStopped = ref(false);

const form = useForm({
    challenge_id: props.challenge.id,
    duration_seconds: 0,
});

function handleStart() {
    showCountdown.value = true;
    isTimerStopped.value = false;
}

function onCountdownComplete() {
    showCountdown.value = false;
    startTimer();
}

function handleComplete() {
    // Stop the timer first
    stopTimer();
    isTimerStopped.value = true;
    form.duration_seconds = elapsedSeconds.value;
    showCompleteDialog.value = true;
}

function handleStop() {
    stopTimer();
    isTimerStopped.value = true;
}

function submitPlank() {
    form.post(planks.store(), {
        onSuccess: () => {
            showCompleteDialog.value = false;
        },
    });
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Plank',
                href: planks.index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Plank" />

    <CountdownOverlay
        :seconds="countdownSeconds"
        :is-active="showCountdown"
        @complete="onCountdownComplete"
    />

    <div class="space-y-6 p-4">
        <Heading
            :title="challenge.name"
            description="Hold your plank position until you reach today's target"
        />

        <!-- Target Card -->
        <Card>
            <CardHeader class="pb-2">
                <CardDescription class="flex items-center gap-2">
                    <Trophy class="h-4 w-4" />
                    Today's Target
                </CardDescription>
                <CardTitle class="text-3xl">{{
                    formatDuration(todayTarget)
                }}</CardTitle>
            </CardHeader>
        </Card>

        <!-- Already Completed State -->
        <Card
            v-if="hasCompletedToday"
            class="border-green-500/50 bg-green-50 dark:bg-green-950"
        >
            <CardContent class="flex items-center gap-4 py-6">
                <CheckCircle2 class="h-12 w-12 text-green-500" />
                <div>
                    <h3
                        class="text-lg font-semibold text-green-700 dark:text-green-300"
                    >
                        Already Completed!
                    </h3>
                    <p class="text-muted-foreground">
                        You've already completed today's plank. Great job!
                    </p>
                </div>
            </CardContent>
        </Card>

        <!-- Timer Section -->
        <Card v-else class="py-8">
            <CardContent class="flex flex-col items-center gap-8">
                <TimerDisplay
                    :seconds="elapsedSeconds"
                    :target-seconds="todayTarget"
                    :is-running="isRunning"
                />

                <TimerControls
                    :is-running="isRunning"
                    :has-completed-today="hasCompletedToday"
                    @start="handleStart"
                    @stop="handleStop"
                    @complete="handleComplete"
                />
            </CardContent>
        </Card>

        <!-- Complete Dialog -->
        <Dialog v-model:open="showCompleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <CheckCircle2 class="h-6 w-6 text-green-500" />
                        Complete Plank
                    </DialogTitle>
                    <DialogDescription>
                        You held the plank for {{ formattedTime }}. Save your
                        progress?
                    </DialogDescription>
                </DialogHeader>

                <div class="py-4 text-center">
                    <div class="font-mono text-5xl font-bold text-primary">
                        {{ formattedTime }}
                    </div>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Target was {{ formatDuration(todayTarget) }}
                    </p>
                    <Badge
                        v-if="elapsedSeconds >= todayTarget"
                        variant="default"
                        class="mt-2"
                    >
                        Target Reached!
                    </Badge>
                    <Badge v-else variant="secondary" class="mt-2">
                        {{ formatDuration(todayTarget - elapsedSeconds) }} under
                        target
                    </Badge>
                </div>

                <DialogFooter class="flex gap-2">
                    <Button
                        variant="outline"
                        @click="showCompleteDialog = false"
                    >
                        Cancel
                    </Button>
                    <Button :disabled="form.processing" @click="submitPlank">
                        Save Progress
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
