<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import PreferencesController from '@/actions/App/Http/Controllers/Settings/PreferencesController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/preferences';

type Props = {
    countdownSeconds: number;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Preference settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Preference settings" />

    <h1 class="sr-only">Preference settings</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Countdown settings"
            description="Set the countdown duration in seconds"
        />

        <Form
            v-bind="PreferencesController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing, recentlySuccessful }"
        >
            <div class="grid gap-2">
                <Label for="countdown_seconds">Countdown seconds</Label>
                <Input
                    id="countdown_seconds"
                    name="countdown_seconds"
                    type="number"
                    min="0"
                    step="1"
                    :default-value="countdownSeconds"
                    required
                />
                <InputError :message="errors.countdown_seconds" />
            </div>

            <div class="flex items-center gap-4">
                <Button
                    :disabled="processing"
                    data-test="update-countdown-button"
                >
                    Save
                </Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-show="recentlySuccessful"
                        class="text-sm text-neutral-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </Form>
    </div>
</template>
