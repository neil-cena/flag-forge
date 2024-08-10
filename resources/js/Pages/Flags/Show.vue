<script setup lang="ts">
import AuditTimeline from '@/Components/AuditTimeline.vue';
import RolloutSlider from '@/Components/RolloutSlider.vue';
import RuleBuilder, { type Rule } from '@/Components/RuleBuilder.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps<{
    project: { project_key: string };
    featureFlag: {
        flag_key: string;
        name: string;
        description: string | null;
        is_enabled: boolean;
        rollout_percentage: number;
        targeting_rules: Rule[];
    };
    auditLogs: Array<{ id: number; action: string; created_at: string }>;
}>();

const form = ref({
    name: props.featureFlag.name,
    description: props.featureFlag.description ?? '',
    is_enabled: props.featureFlag.is_enabled,
    rollout_percentage: props.featureFlag.rollout_percentage,
    targeting_rules: props.featureFlag.targeting_rules ?? [],
});

const save = () => {
    router.patch(route('flags.update', [props.project.project_key, props.featureFlag.flag_key]), form.value);
};

onMounted(() => {
    const channelName = `projects.${props.project.project_key}.flags`;
    (window.Echo as any)?.channel(channelName).listen('.flag.updated', (event: any) => {
        if (event.flag_key === props.featureFlag.flag_key) {
            router.reload({ only: ['featureFlag', 'auditLogs'] });
        }
    });
});

onBeforeUnmount(() => {
    const channelName = `projects.${props.project.project_key}.flags`;
    (window.Echo as any)?.leave(channelName);
});
</script>

<template>
    <Head :title="featureFlag.name" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Flag: {{ featureFlag.name }}</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto grid max-w-7xl gap-6 sm:px-6 lg:grid-cols-3 lg:px-8">
                <div class="space-y-4 rounded-lg bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Flag name</label>
                        <input v-model="form.name" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="form.description" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input v-model="form.is_enabled" type="checkbox" />
                        <label class="text-sm text-gray-700">Flag enabled</label>
                    </div>

                    <RolloutSlider v-model="form.rollout_percentage" />
                    <RuleBuilder v-model="form.targeting_rules" />

                    <button @click="save" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white">
                        Save changes
                    </button>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <AuditTimeline :items="auditLogs" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
