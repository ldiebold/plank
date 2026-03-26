<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Trophy,
    Users,
    Play,
    CheckCircle2,
    Calendar,
    ArrowRight,
    Trash2,
    Clock,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import challenges from '@/routes/challenges';
import planks from '@/routes/planks';
import Heading from '@/components/Heading.vue';
import { formatDuration } from '@/lib/utils';

const props = defineProps<{
    hasChallenge: boolean;
    challenge?: {
        id: number;
        name: string;
        description: string | null;
    };
    todayTarget?: number;
    todayCompletions: Array<{
        id: number;
        user: {
            id: number;
            name: string;
        };
        duration_seconds: number;
        completed_at: string;
    }>;
    hasCompletedToday: boolean;
}>();

const deleteForm = useForm({});

function deleteTodaySubmission() {
    if (confirm("Are you sure you want to delete today's plank submission?")) {
        deleteForm.delete(planks.destroy());
    }
}

function formatTime(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
    });
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-6 p-4">
        <!-- Welcome Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight">
                Welcome back, {{ $page.props.auth.user.name }}
            </h1>
            <p class="text-muted-foreground">
                {{
                    hasChallenge
                        ? 'Ready to plank today?'
                        : 'Start your planking journey!'
                }}
            </p>
        </div>

        <!-- No Challenge State -->
        <div v-if="!hasChallenge" class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Trophy class="h-5 w-5" />
                        Create Challenge
                    </CardTitle>
                    <CardDescription
                        >Start your own planking challenge and invite
                        friends</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <Button as-child class="w-full">
                        <Link :href="challenges.create()">
                            Create New Challenge
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Link>
                    </Button>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Join Challenge
                    </CardTitle>
                    <CardDescription
                        >Have an invite code? Join an existing
                        challenge</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <Button as-child variant="outline" class="w-full">
                        <Link :href="challenges.index()">
                            Join Challenge
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>

        <!-- Has Challenge State -->
        <template v-else>
            <!-- Today's Plank Card -->
            <Card class="relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 h-32 w-32 translate-x-8 -translate-y-8 rounded-full bg-primary/10"
                />
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Trophy class="h-5 w-5 text-primary" />
                        {{ challenge?.name }}
                    </CardTitle>
                    <CardDescription>{{
                        challenge?.description || 'No description'
                    }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Today's Target
                            </p>
                            <p class="text-4xl font-bold">
                                {{ formatDuration(todayTarget || 0) }}
                            </p>
                        </div>

                        <div
                            v-if="hasCompletedToday"
                            class="flex items-center gap-3"
                        >
                            <div class="flex items-center gap-2 text-green-600">
                                <CheckCircle2 class="h-6 w-6" />
                                <span class="font-medium"
                                    >Completed Today!</span
                                >
                            </div>
                            <Button
                                variant="outline"
                                size="sm"
                                class="text-red-500 hover:bg-red-50 hover:text-red-600"
                                :disabled="deleteForm.processing"
                                @click="deleteTodaySubmission"
                            >
                                <Trash2 class="mr-1 h-4 w-4" />
                                Delete
                            </Button>
                        </div>

                        <Button v-else as-child size="lg">
                            <Link :href="planks.index()">
                                <Play class="mr-2 h-5 w-5" />
                                Do Plank
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Today's Participants -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Calendar class="h-5 w-5" />
                        Who's Done It Today
                    </CardTitle>
                    <CardDescription>
                        {{
                            todayCompletions.length === 0
                                ? 'No one has completed yet. Be the first!'
                                : `${todayCompletions.length} member${todayCompletions.length === 1 ? '' : 's'} completed today`
                        }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="todayCompletions.length === 0"
                        class="py-8 text-center text-muted-foreground"
                    >
                        <Trophy class="mx-auto mb-2 h-12 w-12 opacity-20" />
                        <p>No one has completed today's plank yet.</p>
                        <Button as-child class="mt-4" variant="outline">
                            <Link :href="planks.index()">Be the First!</Link>
                        </Button>
                    </div>
                    <div v-else class="flex flex-wrap gap-2">
                        <div
                            v-for="completion in todayCompletions"
                            :key="completion.id"
                            class="flex items-center gap-2 rounded-full border px-3 py-2"
                            :class="{
                                'border-primary/20 bg-primary/5':
                                    completion.user.id ===
                                    $page.props.auth.user.id,
                            }"
                        >
                            <Avatar class="h-6 w-6">
                                <AvatarFallback>{{
                                    completion.user.name.charAt(0)
                                }}</AvatarFallback>
                            </Avatar>
                            <span class="text-sm font-medium">{{
                                completion.user.name
                            }}</span>
                            <Badge
                                v-if="
                                    completion.user.id ===
                                    $page.props.auth.user.id
                                "
                                variant="secondary"
                                class="text-xs"
                                >You</Badge
                            >
                            <span
                                class="flex items-center gap-1 text-xs text-muted-foreground"
                            >
                                <Clock class="h-3 w-3" />
                                {{ formatTime(completion.completed_at) }}
                            </span>
                            <Button
                                v-if="
                                    completion.user.id ===
                                    $page.props.auth.user.id
                                "
                                variant="ghost"
                                size="sm"
                                class="ml-1 h-6 w-6 p-0 text-red-500 hover:bg-red-50 hover:text-red-600"
                                :disabled="deleteForm.processing"
                                @click="deleteTodaySubmission"
                            >
                                <Trash2 class="h-3 w-3" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
    </div>
</template>
