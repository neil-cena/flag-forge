<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();
</script>

<template>
    <Head title="FlagForge" />
    <div class="min-h-screen bg-gray-100">
        <div class="flex min-h-screen flex-col items-center justify-center px-4">
            <header class="absolute right-6 top-6">
                <nav v-if="canLogin" class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    FlagForge
                </h1>
                <p class="mt-4 text-lg text-gray-600 max-w-xl mx-auto">
                    Feature flags, targeting rules, and percentage rollouts. Manage projects and evaluate flags via API.
                </p>
                <div v-if="canLogin && !$page.props.auth.user" class="mt-10 flex justify-center gap-4">
                    <Link
                        :href="route('login')"
                        class="rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow hover:bg-indigo-700"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        Register
                    </Link>
                </div>
                <div v-if="$page.props.auth.user" class="mt-10">
                    <Link
                        :href="route('dashboard')"
                        class="rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow hover:bg-indigo-700"
                    >
                        Go to Dashboard
                    </Link>
                </div>
            </main>

            <footer class="absolute bottom-6 text-sm text-gray-500">
                FlagForge — feature flags made simple
            </footer>
        </div>
    </div>
</template>
