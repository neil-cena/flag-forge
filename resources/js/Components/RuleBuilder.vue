<script setup lang="ts">
import { ref } from 'vue';

export type Rule = {
    name: string;
    attribute: string;
    operator: string;
    values: string[];
    is_enabled: boolean;
    priority: number;
};

const model = defineModel<Rule[]>({ default: [] });
const draft = ref<Rule>({
    name: 'New rule',
    attribute: 'country',
    operator: 'in',
    values: ['PH'],
    is_enabled: true,
    priority: 100,
});

const addRule = () => {
    model.value = [...model.value, { ...draft.value }];
};

const removeRule = (index: number) => {
    model.value = model.value.filter((_, idx) => idx !== index);
};
</script>

<template>
    <div class="space-y-3">
        <h3 class="text-sm font-semibold text-gray-700">Targeting rules</h3>
        <div class="rounded border p-3">
            <div class="grid gap-2 md:grid-cols-4">
                <input v-model="draft.name" class="rounded border px-2 py-1 text-sm" placeholder="Rule name" />
                <input v-model="draft.attribute" class="rounded border px-2 py-1 text-sm" placeholder="Attribute" />
                <select v-model="draft.operator" class="rounded border px-2 py-1 text-sm">
                    <option value="in">in</option>
                    <option value="eq">eq</option>
                    <option value="neq">neq</option>
                    <option value="not_in">not_in</option>
                </select>
                <input
                    :value="draft.values.join(',')"
                    @input="draft.values = (($event.target as HTMLInputElement).value || '').split(',').map(v => v.trim()).filter(Boolean)"
                    class="rounded border px-2 py-1 text-sm"
                    placeholder="PH,beta"
                />
            </div>
            <button type="button" @click="addRule" class="mt-2 rounded bg-gray-900 px-3 py-1 text-xs text-white">
                Add rule
            </button>
        </div>
        <div
            v-for="(rule, index) in model"
            :key="index"
            class="flex items-center justify-between rounded border bg-white p-2 text-sm"
        >
            <span>{{ rule.name }}: {{ rule.attribute }} {{ rule.operator }} {{ rule.values.join(', ') }}</span>
            <button type="button" @click="removeRule(index)" class="text-xs text-red-600">Remove</button>
        </div>
    </div>
</template>
