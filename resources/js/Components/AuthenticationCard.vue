<script setup lang="ts">
import WhiteLogo from './WhiteLogo.vue';

</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[url(/img/DSC09441.jpg)] bg-no-repeat bg-cover bg-center">
        <div class="text-[#00616d]">
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-stone-50 rounded-md bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-70 shadow-lg overflow-hidden sm:rounded-lg z-10 relative">
            <slot />
        </div>
    </div>
</template>
