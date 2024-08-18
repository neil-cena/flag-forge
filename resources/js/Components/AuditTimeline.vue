<script setup lang="ts">
defineProps<{
    items: Array<{ id: number; action: string; created_at: string; payload?: Record<string, unknown> }>;
}>();
</script>

<template>
    <div class="space-y-2">
        <h3 class="text-sm font-semibold text-gray-700">Audit log</h3>
        <div v-if="items.length === 0" class="rounded border p-3 text-sm text-gray-500">
            No audit records yet.
        </div>
        <div
            v-for="entry in items"
            :key="entry.id"
            class="rounded border bg-white p-3"
        >
            <p class="text-sm font-medium text-gray-900">{{ entry.action }}</p>
            <p class="text-xs text-gray-500">{{ new Date(entry.created_at).toLocaleString() }}</p>
        </div>
    </div>
</template>
