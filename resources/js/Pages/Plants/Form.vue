<script setup>
import AppMain from '@/Layouts/AppMain.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ButtonColor from '@/Components/ButtonColor.vue';
import ButtonGroup from '@/Components/ButtonGroup.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';


import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faAdd,faTrash,faPen } from '@fortawesome/free-solid-svg-icons';
import { ref } from 'vue';

const props = defineProps({
    plant: Object,
});

const form = useForm({
    id: props.plant?.id || null,
    name: props.plant?.name || '',
    areas: props.plant?.areas || [],
});

const submit = () => {
    if(form.id){
        form.post(route('plants/update', { id: form.id }), {
            
        });
    }else{
        form.post(route('plants/create'), {

        });
    }
};


let areaModal=ref(false);

const areaForm = useForm({
    id: null,
    name: '',
    enabled: true,
});

const areaEdit = (area) => {
    areaForm.id = area.id;
    areaForm.name = area.name;
    areaModal.value = true;
};

const areaDelete = (area) => {
    if(area.id) {
        area.enabled = 0;
    }else{
        const index = form.areas.findIndex(a => a.id === area.id);
        form.areas.splice(index, 1);
    }
};

const areaStore = () => {
    //valida si el nombre no esta ocupado en la lista de areas
    if (form.areas.some(a => a.name === areaForm.name && a.id !== areaForm.id)) {
        areaForm.errors.name = 'El nombre del área ya está en uso.';
        return;
    } else {
        areaForm.errors.name = '';
    }
    if (areaForm.id) {
        // Update existing area
        const index = form.areas.findIndex(a => a.id === areaForm.id);
        if (index !== -1) {
            form.areas[index].name = areaForm.name;
        }
        areaForm.id = null; // Reset ID after update
        areaForm.name = ''; // Reset name after update
        areaModal.value = false; // Close modal after update
    } else {
        // Add new area
        form.areas.push({
            id: null,
            name: areaForm.name,
            enabled: 1,
        });
        areaForm.id = null; // Reset ID after update
        areaForm.name = ''; // Reset name after update
        areaModal.value = false; // Close modal after update
    }
};

</script>
<template>
    <AppMain title="Planta">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg sm:p-6 lg:p-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                        Nueva Planta
                    </h2>
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Nombre" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="my-4">
                            <h2 class="flex items-center">
                                <b>Áreas de planta</b>
                                <ButtonColor :color="'green'" @click="areaModal = true" class="ml-2"  title="Agregar Área">
                                    <FontAwesomeIcon :icon="faAdd" class="pe-2"/>
                                    Agregar Área
                                </ButtonColor>
                            </h2>
                            <div class="flex flex-wrap gap-2 pt-2">
                                <div v-for="(area,index) in form.areas.filter(e=> e.enabled)" :key="index" class="flex items-center border rounded">
                                    <span class="ml-2 w-52">{{ area.name }}</span>
                                    <ButtonGroup  class="ml-2">
                                        <ButtonColor type="button" :color="'blue'" @click="areaEdit(area)" title="Editar Área">
                                            <FontAwesomeIcon :icon="faPen" />
                                        </ButtonColor>
                                        <ButtonColor type="button" :color="'red'" @click="areaDelete(area)" title="Eliminar Área">
                                            <FontAwesomeIcon :icon="faTrash" />
                                        </ButtonColor>
                                    </ButtonGroup>
                                </div>
                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton class="ms-4" :disabled="form.processing" v-bind:class="{'opacity-25': form.processing}">
                                Guardar
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <Modal :show="areaModal" @close="areaModal = false">
            <div class="p-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                    Agregar Área
                </h2>
                <form @submit.prevent="areaStore">
                    <input type="hidden" name="id" v-model="areaForm.id"/>
                    <div>
                        <InputLabel for="name" value="Nombre del Área" />
                        <TextInput
                            id="name"
                            v-model="areaForm.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="areaForm.errors.name" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton class="ms-4" :disabled="areaForm.processing" v-bind:class="{'opacity-25': areaForm.processing}">
                            Agregar Área
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppMain>
</template>
