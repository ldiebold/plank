<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, PlusCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import challenges from '@/routes/challenges';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';

const form = useForm({
    name: '',
    description: '',
    starting_time_seconds: '60',
    daily_increment_seconds: '10',
    goal_time_seconds: '',
    start_date: new Date().toISOString().split('T')[0],
    is_active: true,
});

const timeOptions = [
    { value: '30', label: '30 seconds' },
    { value: '60', label: '1 minute' },
    { value: '120', label: '2 minutes' },
    { value: '300', label: '5 minutes' },
];

const incrementOptions = [
    { value: '5', label: '5 seconds' },
    { value: '10', label: '10 seconds' },
    { value: '15', label: '15 seconds' },
    { value: '30', label: '30 seconds' },
];

const goalOptions = [
    { value: '', label: 'No goal (unlimited)' },
    { value: '300', label: '5 minutes' },
    { value: '600', label: '10 minutes' },
    { value: '900', label: '15 minutes' },
    { value: '1800', label: '30 minutes' },
];

function submit() {
    form.post(challenges.store());
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Challenge',
                href: challenges.index(),
            },
            {
                title: 'Create',
                href: challenges.create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Create Challenge" />

    <div class="space-y-6 p-4">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="sm" as-child>
                <Link :href="challenges.index()">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back
                </Link>
            </Button>
        </div>

        <Heading
            title="Create Challenge"
            description="Set up a new planking challenge for you and your friends"
        />

        <Card>
            <CardHeader>
                <CardTitle>Challenge Details</CardTitle>
                <CardDescription
                    >Configure your challenge settings</CardDescription
                >
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="name">Challenge Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            required
                            placeholder="e.g., 30 Day Plank Challenge"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description (Optional)</Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            placeholder="Describe your challenge..."
                        />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label>Starting Time</Label>
                            <Select v-model="form.starting_time_seconds">
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Select starting time"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in timeOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError
                                :message="form.errors.starting_time_seconds"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label>Daily Increment</Label>
                            <Select v-model="form.daily_increment_seconds">
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Select daily increment"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in incrementOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError
                                :message="form.errors.daily_increment_seconds"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label>Goal Time (Optional)</Label>
                            <Select v-model="form.goal_time_seconds">
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Select goal time"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in goalOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError
                                :message="form.errors.goal_time_seconds"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="start_date">Start Date</Label>
                            <Input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                required
                            />
                            <InputError :message="form.errors.start_date" />
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="is_active"
                            v-model:checked="form.is_active"
                        />
                        <Label for="is_active" class="font-normal"
                            >Challenge is active</Label
                        >
                    </div>

                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <PlusCircle class="mr-2 h-4 w-4" />
                            Create Challenge
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="challenges.index()">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
