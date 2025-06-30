<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    aside: {
        type: Boolean,
        default: false,
    },
});

let open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const classes = computed(() => {
    return open.value
        ? 'inline-flex w-52 h-10 items-center px-1 border-l-4 border-emerald-700 text-sm text-white bg-emerald-800 hover:border-gray-300 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'inline-flex w-52 h-10 items-center px-1 border-l-4 border-transparent text-sm text-white hover:bg-emerald-800 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
});
</script>

<template>
    <div class="relative w-100 overflow-hidden hover:overflow-visible">
        <div @click="open = ! open" :class="classes">
            <slot name="trigger" />
            <svg
                class="size-4 transition-transform duration-300"
                :class="open ? 'rotate-0' : 'rotate-90'"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 24 24"
            >
                <path
                    class="inline-flex"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 6L16 12L8 18"
                />
            </svg>
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="fixed inset-0 z-51" @click="open = false" />

        <div
            v-show="open"
            class="w-52"
            :class="props.aside ? 'relative sm:absolute sm:z-50 sm:left-10' : 'relative'"
            @click="open = false"
        >
            <div class="ring-1 ring-black ring-opacity-5 bg-emerald-950" :class="open ? 'min-h-10' : 'min-h-0'">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>
