<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Trophy, Users, ArrowRight } from 'lucide-vue-next';
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
import challenges from '@/routes/challenges';
import Heading from '@/components/Heading.vue';

defineProps<{
    hasChallenge: boolean;
}>();

const joinForm = useForm({
    invite_code: '',
});

function submitJoin() {
    joinForm.get(challenges.join(joinForm.invite_code));
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
    <Head title="Challenge" />

    <div class="space-y-6 p-4">
        <Heading
            title="Join the Challenge"
            description="Create a new challenge or join an existing one"
        />

        <div class="grid gap-4 md:grid-cols-2">
            <!-- Create Challenge Card -->
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
                <CardContent class="space-y-4">
                    <p class="text-sm text-muted-foreground">
                        Set your starting time, daily increment, and goal.
                        You'll get an invite code to share.
                    </p>
                    <Button as-child class="w-full">
                        <Link :href="challenges.create()">
                            Create New Challenge
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Link>
                    </Button>
                </CardContent>
            </Card>

            <!-- Join Challenge Card -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5" />
                        Join Challenge
                    </CardTitle>
                    <CardDescription
                        >Enter an invite code to join an existing
                        challenge</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitJoin" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="invite_code">Invite Code</Label>
                            <Input
                                id="invite_code"
                                v-model="joinForm.invite_code"
                                placeholder="Enter invite code..."
                                required
                            />
                        </div>
                        <Button
                            type="submit"
                            class="w-full"
                            :disabled="joinForm.processing"
                        >
                            Join Challenge
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
