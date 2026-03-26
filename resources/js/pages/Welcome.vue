<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import {
    Timer,
    Trophy,
    Users,
    ArrowRight,
    CheckCircle2,
} from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Planky - Plank Together">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="min-h-screen bg-gradient-to-b from-white to-gray-50 dark:from-gray-950 dark:to-gray-900"
    >
        <!-- Header -->
        <header
            class="border-b border-gray-200 bg-white/80 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-950/80"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-2">
                    <img
                        src="/assets/logos/planky-40.png"
                        alt="Planky"
                        class="size-10 rounded-lg object-contain"
                    />
                    <span class="text-xl font-bold">Planky</span>
                </div>
                <nav class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        Go to Dashboard
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                        >
                            Get Started
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative px-4 pt-20 pb-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6 flex justify-center">
                    <img
                        src="/assets/logos/planky-240.png"
                        alt="Planky"
                        class="h-[200px] w-[200px] rounded-2xl object-contain shadow-lg"
                    />
                </div>
                <h1
                    class="mb-6 text-5xl font-bold tracking-tight text-gray-900 sm:text-6xl dark:text-white"
                >
                    Plank Together with <span class="text-primary">Planky</span>
                </h1>
                <p
                    class="mx-auto max-w-2xl text-lg text-gray-600 dark:text-gray-400"
                >
                    Build core strength with friends! Create challenges, track
                    your progress, and motivate each other to reach your plank
                    goals.
                </p>
                <div class="mt-10 flex justify-center gap-4">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="register()"
                        class="inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3 text-base font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        Start Your Challenge
                        <ArrowRight class="h-5 w-5" />
                    </Link>
                    <Link
                        v-else
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3 text-base font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        Continue Your Journey
                        <ArrowRight class="h-5 w-5" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section
            class="border-y border-gray-200 bg-white px-4 py-16 sm:px-6 lg:px-8 dark:border-gray-800 dark:bg-gray-950"
        >
            <div class="mx-auto max-w-7xl">
                <h2
                    class="mb-12 text-center text-3xl font-bold text-gray-900 dark:text-white"
                >
                    How It Works
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30"
                        >
                            <Trophy
                                class="h-7 w-7 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <h3
                            class="mb-2 text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            Create a Challenge
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Set your starting time, daily increment, and goal.
                            Invite friends with a simple code.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30"
                        >
                            <Timer
                                class="h-7 w-7 text-green-600 dark:text-green-400"
                            />
                        </div>
                        <h3
                            class="mb-2 text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            Do Your Daily Plank
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Use our timer with countdown and hold that position!
                            Track your time automatically.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30"
                        >
                            <Users
                                class="h-7 w-7 text-purple-600 dark:text-purple-400"
                            />
                        </div>
                        <h3
                            class="mb-2 text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            Stay Motivated
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            See who else completed today's plank. Build
                            consistency together!
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Proof Section -->
        <section class="px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h2
                    class="mb-8 text-3xl font-bold text-gray-900 dark:text-white"
                >
                    Why Planky?
                </h2>
                <div class="grid gap-6 sm:grid-cols-2">
                    <div
                        class="flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950"
                    >
                        <CheckCircle2
                            class="mt-0.5 h-5 w-5 shrink-0 text-green-500"
                        />
                        <p class="text-left text-gray-600 dark:text-gray-400">
                            <strong class="text-gray-900 dark:text-white"
                                >Progressive Overload:</strong
                            >
                            Automatically increase your plank time each day to
                            build strength gradually.
                        </p>
                    </div>
                    <div
                        class="flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950"
                    >
                        <CheckCircle2
                            class="mt-0.5 h-5 w-5 shrink-0 text-green-500"
                        />
                        <p class="text-left text-gray-600 dark:text-gray-400">
                            <strong class="text-gray-900 dark:text-white"
                                >Accountability:</strong
                            >
                            Challenge friends keeps you motivated and
                            consistent.
                        </p>
                    </div>
                    <div
                        class="flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950"
                    >
                        <CheckCircle2
                            class="mt-0.5 h-5 w-5 shrink-0 text-green-500"
                        />
                        <p class="text-left text-gray-600 dark:text-gray-400">
                            <strong class="text-gray-900 dark:text-white"
                                >Track Consistency:</strong
                            >
                            See who's done their plank today - it's about
                            showing up!
                        </p>
                    </div>
                    <div
                        class="flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950"
                    >
                        <CheckCircle2
                            class="mt-0.5 h-5 w-5 shrink-0 text-green-500"
                        />
                        <p class="text-left text-gray-600 dark:text-gray-400">
                            <strong class="text-gray-900 dark:text-white"
                                >Customizable Timer:</strong
                            >
                            Set your own countdown and plank at your own pace.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-primary px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="mb-4 text-3xl font-bold text-primary-foreground">
                    Ready to Strengthen Your Core?
                </h2>
                <p class="mb-8 text-lg text-primary-foreground/80">
                    Join Planky today and start your plank journey with friends.
                </p>
                <div class="flex justify-center gap-4">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="register()"
                        class="inline-flex items-center gap-2 rounded-md bg-white px-6 py-3 text-base font-medium text-primary hover:bg-gray-100"
                    >
                        Get Started Free
                        <ArrowRight class="h-5 w-5" />
                    </Link>
                    <Link
                        v-else
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-md bg-white px-6 py-3 text-base font-medium text-primary hover:bg-gray-100"
                    >
                        Go to Dashboard
                        <ArrowRight class="h-5 w-5" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="border-t border-gray-200 bg-white px-4 py-8 sm:px-6 lg:px-8 dark:border-gray-800 dark:bg-gray-950"
        >
            <div class="mx-auto max-w-7xl">
                <div
                    class="flex flex-col items-center justify-between gap-4 sm:flex-row"
                >
                    <div class="flex items-center gap-2">
                        <img
                            src="/assets/logos/planky-32.png"
                            alt="Planky"
                            class="size-8 rounded-md object-contain"
                        />
                        <span class="text-lg font-semibold">Planky</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        &copy; 2026 Planky. Build strength together.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
