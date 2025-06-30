<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex w-52 h-10 items-center px-1 border-l-4 border-emerald-700 text-sm text-white hover:bg-emerald-800 hover:border-gray-300 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'inline-flex w-52 h-10 items-center px-1 border-l-4 border-transparent text-sm text-white hover:bg-emerald-800 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
});


</script>

<template>
    <Link :href="href" class="w-100 overflow-hidden hover:overflow-visible">
        <div :class="classes">
            <slot />
        </div>
    </Link>
</template>
