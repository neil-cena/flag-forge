<script setup lang="ts">
// TODO: optional step size (e.g. 5% increments) for some flags
const model = defineModel<number>({ default: 0 });
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700">Percentage rollout</label>
            <span class="text-sm text-indigo-700">{{ model }}%</span>
        </div>
        <input
            v-model.number="model"
            type="range"
            min="0"
            max="100"
            class="h-2 w-full cursor-pointer rounded-lg bg-gray-200"
        />
    </div>
</template>
