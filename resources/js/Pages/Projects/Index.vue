<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

type Flag = { id: number; name: string; flag_key: string; rollout_percentage: number; is_enabled: boolean };
type Project = {
    id: number;
    name: string;
    project_key: string;
    description: string | null;
    feature_flags_count: number;
    feature_flags: Flag[];
};

const props = defineProps<{ projects: Project[] }>();

const projectForm = ref({
    name: '',
    project_key: '',
    description: '',
});

const createProject = () => {
    router.post(route('projects.store'), projectForm.value);
};
</script>

<template>
    <Head title="Projects" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Projects</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="mb-3 font-semibold text-gray-900">Create project</h3>
                    <div class="grid gap-2 md:grid-cols-3">
                        <input v-model="projectForm.name" placeholder="Project name" class="rounded border px-3 py-2 text-sm" />
                        <input v-model="projectForm.project_key" placeholder="project_key" class="rounded border px-3 py-2 text-sm" />
                        <input v-model="projectForm.description" placeholder="Description" class="rounded border px-3 py-2 text-sm" />
                    </div>
                    <button @click="createProject" class="mt-3 rounded bg-indigo-600 px-3 py-2 text-sm text-white">
                        Save Project
                    </button>
                </div>

                <div
                    v-for="project in props.projects"
                    :key="project.id"
                    class="rounded-lg bg-white p-6 shadow-sm"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ project.name }}</h3>
                            <p class="text-xs text-gray-500">{{ project.project_key }}</p>
                        </div>
                        <span class="text-xs text-gray-600">{{ project.feature_flags_count }} flags</span>
                    </div>
                    <div class="space-y-2">
                        <div v-for="flag in project.feature_flags" :key="flag.id" class="flex items-center justify-between rounded border p-2">
                            <div>
                                <p class="text-sm font-medium">{{ flag.name }}</p>
                                <p class="text-xs text-gray-500">{{ flag.flag_key }}</p>
                            </div>
                            <Link :href="route('flags.show', [project.project_key, flag.flag_key])" class="text-xs text-indigo-600">
                                Open
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
