<script setup>
import AppMain from '@/Layouts/AppMain.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import ButtonColor from '@/Components/ButtonColor.vue';
import ButtonGroup from '@/Components/ButtonGroup.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faAdd,faTrash,faPen } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    actionplan: Object,
    plants: Array,
    users: Array,
});

const form = useForm({
    id: props.actionplan?.id || null,
    plant_id: props.actionplan?.id || null,
    leader_id: props.actionplan?.leader_id || null,
    user_id: props.actionplan?.user_id || null,
    created_id: props.actionplan?.created_id || null,
    date_start: props.actionplan?.date_start || null,
    date_end: props.actionplan?.date_end || null,
    description: props.actionplan?.description || '',
    status: props.actionplan?.status || 'pending',
    priority: props.actionplan?.priority || 'low',
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

</script>
<template>
    <AppMain title="Plan de Acción">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg sm:p-6 lg:p-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                        Nuevo Plan de Acción
                    </h2>
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="plant_id" value="Planta" />
                            <SelectInput
                                id="plant_id"
                                v-model="form.actionplan_id"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="plant_id"
                            >
                                <option value="">Seleccione una planta</option>
                                <option v-for="plant in props.plants" :key="plant.id" :value="plant.id">
                                    {{ plant.name }}
                                </option>
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors.actionplan_id" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="leader_id" value="Líder" />
                            <SelectInput
                                id="leader_id"
                                v-model="form.leader_id"
                                class="mt-1 block w-full"
                                required
                                autocomplete="leader_id"
                            >
                                <option value="">Seleccione un líder</option>
                                <option v-for="user in props.users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors.leader_id" />    
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

    </AppMain>
</template>
