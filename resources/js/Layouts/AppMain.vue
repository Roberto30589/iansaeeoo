<script setup>
    import { onUnmounted, ref } from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import { Head, Link, router, usePage } from '@inertiajs/vue3';
    import ApplicationMark from '@/Components/ApplicationMark.vue';
    import Banner from '@/Components/Banner.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import AsideDropdown from '@/Components/AsideDropdown.vue';
    import AsideDropdownLink from '@/Components/AsideDropdownLink.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import AsideLink from '@/Components/AsideLink.vue';
    
    
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { faUser,faHouse,faBuilding,faGear,faUsers,faLock,faChevronDown,faChevronUp } from '@fortawesome/free-solid-svg-icons'

    import ToastContainer from '@/Components/ToastContainer.vue';
    import toast from '@/Stores/toast';

    defineProps({
        title: String,
    });

    const showingNavigationDropdown = ref(false);

    const switchToTeam = (team) => {
        router.put(route('current-team.update'), {
            team_id: team.id,
        }, {
            preserveState: false,
        });
    };

    const logout = () => {
        router.post(route('logout'));
    };


    //Esto es para los mensajes de success y error
    const page = usePage();
    console.log(page.props)

    let removeFinishEventListener = Inertia.on('finish',()=>{
        if(page.props.flash?.success){
            console.log("success",page.props.flash.success);
            toast.add({type:"success",message:page.props.flash.success});
        }
        if(page.props.flash?.error){
            console.log("error",page.props.flash.error);
            toast.add({type:"error",message:page.props.flash.error});
        }
        
        if(page.props.errors){
            console.log("error",page.props.errors);
            Object.keys(page.props.errors).forEach(key => {
                toast.add({type:"error",message:page.props.errors[key]});
            });
        }
        if(page.props.flash?.warning){
            console.log("error",page.props.flash.warning);
            toast.add({type:"warning",message:page.props.flash.warning});
        }
        if(page.props.flash?.info){
            console.log("info",page.props.flash.info);
            toast.add({type:"info",message:page.props.flash.info});
        }
    });

    onUnmounted(()=> removeFinishEventListener());

</script>

<template>
    <Head :title="title" />
    <Banner />
    <nav class="fixed z-50 min-w-full dark:bg-[#01616d] dark:border-[#01616d]">
        <!-- Primary Navigation Menu -->
        <div class="flex justify-between h-12">
            <div class="shrink-0 flex items-center w-52">
                <Link :href="route('dashboard')">
                    <ApplicationMark class="block h-6  w-auto  px-2" />
                </Link>
                <span class="self-center text-lg font-semibold whitespace-nowrap dark:text-white px-2 hidden sm:inline">IANSA EEOO</span>
                <!-- Hamburger -->
                <div class="flex items-center">
                    <button class="inline-flex items-center justify-center p-1 rounded-md text-green-400 hover:text-green-500 hover:bg-[#001113] focus:outline-none focus:bg-[#001113] focus:text-green-500 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                        <svg
                            class="size-7 transition-transform duration-300"
                            stroke="currentColor"
                            :class="showingNavigationDropdown ? 'rotate-180 sm:rotate-0' : 'rotate-0 sm:rotate-180'"
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
                    </button>

                </div>
            </div>
            <div class="flex items-center px-2">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo">
                        </button>
                    </template>

                    <template #content class="my-1">
                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ $page.props.auth.user.name }}
                        </div>

                        <DropdownLink :href="route('profile.show')">
                            Perfil
                        </DropdownLink>
                        <div class="border-t border-gray-200" />

                        <!-- Authentication -->
                        <form @submit.prevent="logout">
                            <DropdownLink as="button">
                                Cerrar sesión
                            </DropdownLink>
                        </form>
                    </template>
                </Dropdown>
            </div>
        </div>
    </nav>

    <aside class="fixed top-0 z-40 h-screen bg-[#001113] transition-all duration-300" :class="{'w-52 left-0 sm:w-10 sm:left-0': showingNavigationDropdown, 'w-52 -left-52 sm:w-52 sm:left-0': ! showingNavigationDropdown }">
        <div class="flex flex-col h-full pt-14">
            <!-- Navigation Links -->
            <AsideLink :href="route('dashboard')" :active="route().current('dashboard')">
                <FontAwesomeIcon :icon="faHouse" size="lg" class="min-w-6 pr-2" />
                Dashboard
            </AsideLink>

            <AsideLink :href="route('users')" :active="route().current('users')">
                <FontAwesomeIcon :icon="faUsers" size="lg" class="min-w-6 pr-2" />
                Usuarios
            </AsideLink>

            <AsideDropdown :aside="showingNavigationDropdown">
                <template #trigger>
                    <FontAwesomeIcon :icon="faGear" size="lg" class="min-w-6 pr-2" />
                    Administración
                </template>
                <template #content>
                    <AsideDropdownLink :href="route('permissions')" :active="route().current('permissions')">
                        <FontAwesomeIcon :icon="faLock" size="lg" class="min-w-7 pr-1" />
                        Pemisos
                    </AsideDropdownLink>
                    <AsideDropdownLink :href="route('roles')" :active="route().current('roles')">
                        <FontAwesomeIcon :icon="faUsers" size="lg" class="min-w-8 pr-1" />
                        Roles
                    </AsideDropdownLink>
                </template>
            </AsideDropdown>
        </div>
    </aside>
    <!-- Page Content -->
    <main class="pt-12" :class="{'ms-0 sm:ms-10': showingNavigationDropdown, 'ms-0 sm:ms-52': ! showingNavigationDropdown }">
        <slot />
    </main>
    <ToastContainer/>
</template>
<style scoped>
    main {
        transition: margin-left 0.3s ease;
    }
</style>
