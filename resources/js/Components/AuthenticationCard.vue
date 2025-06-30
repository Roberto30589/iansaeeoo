<script setup lang="ts">
import WhiteLogo from './WhiteLogo.vue';

</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#005856]">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg z-10 relative">
            <slot />
        </div>
        <WhiteLogo class="w-1/2 md:w-1/3 h-auto absolute z-0 -left-0 bottom-0" />
    </div>
</template>
