<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

defineExpose({ focus: () => select.value.focus() });
</script>

<template>
    <select
        ref="select"
        class="border-stone-300 focus:border-stone-500 focus:ring-stone-500 rounded-md shadow-sm bg-white"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <slot></slot>
    </select>
</template>