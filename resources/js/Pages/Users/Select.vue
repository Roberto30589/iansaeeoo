<script setup>
import AppMain from '@/Layouts/AppMain.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
    roles: Array,
    plants: Array, // Si necesitas manejar plantas
});

const form = useForm({
    name: props.user.name     || '', // Asegúrate de manejar valores nulos o no definidos
    email: props.user.email   || '', // Asegúrate de manejar valores nulos o no definidos
    //obtener los id los roles del usuario
    roles: props.user.roles.map(role => role.id),    
    plants: props.user.plants ? props.user.plants.map(plant => plant.id) : [], // Si necesitas manejar plantas
    updatePassword: false, // Indica si se actualizará la contraseña
    password: '',
    password_confirmation: '',
});

// Función para actualizar el usuario
const updateUser = () => {
    form.post(route('users/update', { id: props.user.id }), {
        onSuccess: () => {

        },
    });
};

const toggleRole = (roleId) => {
    if (form.roles.includes(roleId)) {
        form.roles = form.roles.filter(id => id !== roleId);
    } else {
        form.roles.push(roleId);
    }
};

const togglePlant = (plantId) => {
    if (form.plants.includes(plantId)) {
        form.plants = form.plants.filter(id => id !== plantId);
    } else {
        form.plants.push(plantId);
    }
};

</script>
<template>
    <AppMain title="Usuario">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg sm:p-6 lg:p-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                        Actualizar Usuario
                    </h2>
                    <form @submit.prevent="updateUser">
                        <div>
                            <InputLabel for="name" value="Nombre" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                                autocomplete="username"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="my-4">
                            <h2><b>Roles del usuario</b></h2>
                            <div class="flex flex-wrap gap-4 pt-2">
                                <label v-for="(rol,index) in roles" :key="rol.id" class="flex w-52 items-center border px-2 py-1 rounded">
                                    <input 
                                    type="checkbox" 
                                    :checked="form.roles.includes(rol.id)"
                                    @change="toggleRole(rol.id)"
                                    v-bind:value="rol.id"
                                    />
                                    <span class="ml-2">{{ rol.name }}</span>
                                </label>
                            </div>
                        </div>
                        <hr>
                        <div class="my-4">
                            <h2><b>Plantas del usuario</b></h2>
                            <div class="flex flex-wrap gap-4 pt-2">
                                <label v-for="(plant,index) in plants" :key="plant.id" class="flex w-52 items-center border px-2 py-1 rounded">
                                    <input 
                                    type="checkbox" 
                                    :checked="form.plants.includes(plant.id)"
                                    @change="togglePlant(plant.id)"
                                    v-bind:value="plant.id"
                                    />
                                    <span class="ml-2">{{ plant.name }}</span>
                                </label>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.updatePassword" />
                                <span class="ml-2">Actualizar Contraseña</span>
                            </label>
                        </div>

                        <div v-if="form.updatePassword" class="mt-4">
                            <InputLabel for="password" value="Nueva Contraseña" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div v-if="form.updatePassword" class="mt-4">
                            <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton class="ms-4" :disabled="form.processing">
                                Actualizar
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppMain>
</template>