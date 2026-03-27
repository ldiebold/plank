<script setup lang="ts">
import { Head, Link, Form, useForm } from '@inertiajs/vue3';
import {
    PlusCircle,
    Users,
    ArrowRight,
    Copy,
    CheckCircle2,
    LogOut,
} from 'lucide-vue-next';
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
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import challenges from '@/routes/challenges';
import planks from '@/routes/planks';
import Heading from '@/components/Heading.vue';
import { formatDuration } from '@/lib/utils';
import { ref } from 'vue';

const props = defineProps<{
    challenge: {
        id: number;
        name: string;
        description: string | null;
        invite_code: string;
        start_date: string;
        is_active: boolean;
    };
    todayTarget: number;
    members: Array<{
        id: number;
        name: string;
        pivot: {
            joined_at: string;
        };
    }>;
    todayCompletions: Array<{
        id: number;
        user: {
            id: number;
            name: string;
        };
        duration_seconds: number;
        completed_at: string;
    }>;
}>();

const joinForm = useForm({
    invite_code: '',
});

const copied = ref(false);

function copyInviteCode() {
    navigator.clipboard.writeText(props.challenge.invite_code);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
}

function submitJoin() {
    joinForm.post(challenges.store());
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Challenge',
                href: challenges.index(),
            },
        ],
    },
});
</script>

<template>
    <Head :title="challenge.name" />

    <div class="space-y-6 p-4">
        <Heading
            :title="challenge.name"
            :description="challenge.description || 'No description'"
        />

        <!-- Challenge Stats -->
        <div class="grid gap-4 md:grid-cols-3">
            <Card>
                <CardHeader class="pb-2">
                    <CardDescription>Today's Target</CardDescription>
                    <CardTitle class="text-3xl">{{
                        formatDuration(todayTarget)
                    }}</CardTitle>
                </CardHeader>
            </Card>

            <Card>
                <CardHeader class="pb-2">
                    <CardDescription>Members</CardDescription>
                    <CardTitle class="text-3xl">{{ members.length }}</CardTitle>
                </CardHeader>
            </Card>

            <Card>
                <CardHeader class="pb-2">
                    <CardDescription>Completions Today</CardDescription>
                    <CardTitle class="text-3xl">{{
                        todayCompletions.length
                    }}</CardTitle>
                </CardHeader>
            </Card>
        </div>

        <!-- Invite Code -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <Users class="h-5 w-5" />
                    Invite Members
                </CardTitle>
                <CardDescription
                    >Share this code with friends to join your
                    challenge</CardDescription
                >
            </CardHeader>
            <CardContent>
                <div class="flex items-center gap-2">
                    <code
                        class="rounded bg-muted px-3 py-2 font-mono text-lg"
                        >{{ challenge.invite_code }}</code
                    >
                    <Button variant="outline" size="sm" @click="copyInviteCode">
                        <Copy v-if="!copied" class="mr-2 h-4 w-4" />
                        <CheckCircle2
                            v-else
                            class="mr-2 h-4 w-4 text-green-500"
                        />
                        {{ copied ? 'Copied!' : 'Copy' }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Today's Leaderboard -->
        <Card>
            <CardHeader>
                <CardTitle>Today's Leaderboard</CardTitle>
                <CardDescription
                    >Members who completed today's plank</CardDescription
                >
            </CardHeader>
            <CardContent>
                <div
                    v-if="todayCompletions.length === 0"
                    class="py-8 text-center text-muted-foreground"
                >
                    No one has completed today's plank yet. Be the first!
                </div>
                <div v-else class="space-y-2">
                    <div
                        v-for="(completion, index) in todayCompletions"
                        :key="completion.id"
                        class="flex items-center justify-between rounded-lg border p-3"
                    >
                        <div class="flex items-center gap-3">
                            <Badge variant="outline">#{{ index + 1 }}</Badge>
                            <span class="font-medium">{{
                                completion.user.name
                            }}</span>
                        </div>
                        <span class="font-mono font-semibold">{{
                            formatDuration(completion.duration_seconds)
                        }}</span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <Button as-child size="lg" class="flex-1">
                <Link :href="planks.index()">
                    <PlusCircle class="mr-2 h-5 w-5" />
                    Do Plank
                </Link>
            </Button>

            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="outline" size="lg">
                        <LogOut class="mr-2 h-5 w-5" />
                        Leave
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="challenges.leave.form(challenge.id)"
                        v-slot="{ processing }"
                    >
                        <DialogHeader>
                            <DialogTitle>Leave this challenge?</DialogTitle>
                            <DialogDescription>
                                You will be removed from this challenge. You can
                                rejoin later with the invite code.
                            </DialogDescription>
                        </DialogHeader>
                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>
                            <Button
                                type="submit"
                                variant="destructive"
                                :disabled="processing"
                            >
                                Leave Challenge
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
