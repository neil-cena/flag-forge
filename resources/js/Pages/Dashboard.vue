<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    projects: Array<{ id: number; name: string; project_key: string; feature_flags_count: number }>;
    recentAudits: Array<{ id: number; action: string; created_at: string }>;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                FlagForge Overview
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                        <div class="border-b px-6 py-4">
                            <h3 class="font-semibold text-gray-900">Projects</h3>
                        </div>
                        <div class="space-y-3 p-6">
                            <div
                                v-for="project in projects"
                                :key="project.id"
                                class="flex items-center justify-between rounded border p-3"
                            >
                                <div>
                                    <p class="font-medium text-gray-900">{{ project.name }}</p>
                                    <p class="text-xs text-gray-500">{{ project.project_key }}</p>
                                </div>
                                <span class="text-xs text-gray-600">{{ project.feature_flags_count }} flags</span>
                            </div>
                            <Link
                                :href="route('projects.index')"
                                class="inline-flex rounded bg-indigo-600 px-3 py-2 text-sm font-medium text-white"
                            >
                                Manage Projects
                            </Link>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                        <div class="border-b px-6 py-4">
                            <h3 class="font-semibold text-gray-900">Recent Audit Events</h3>
                        </div>
                        <div class="space-y-2 p-6">
                            <p
                                v-if="recentAudits.length === 0"
                                class="text-sm text-gray-500"
                            >
                                No activity yet.
                            </p>
                            <div
                                v-for="entry in recentAudits"
                                :key="entry.id"
                                class="rounded border p-3 text-sm"
                            >
                                <p class="font-medium text-gray-900">{{ entry.action }}</p>
                                <p class="text-xs text-gray-500">{{ new Date(entry.created_at).toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
