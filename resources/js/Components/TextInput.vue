<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="border-stone-300 focus:border-stone-500 focus:ring-stone-500 rounded-md shadow-sm bg-white"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
