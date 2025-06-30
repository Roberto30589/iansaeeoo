<script setup>
import AppMain from '@/Layouts/AppMain.vue';
import Modal from '@/Components/Modal.vue';

import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonGroup from '@/Components/ButtonGroup.vue';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-dt';

import { useForm } from '@inertiajs/vue3';
import {onMounted, ref} from "vue";

DataTable.use(DataTablesCore);

const columns = [
  { data: 'id', title: 'Nº',width:'1%' },
  { data: 'name', title: 'Permiso' },
  { data: null,render: '#action', title: 'Acción',width:'1%',className: 'px-4' }
];

const form = useForm ({
    name: "",
    guard_name: "",
});

let modalform=ref(false);
let id=null;

let dt;
const table = ref();
const formulario = ref(null);
 
onMounted(() => {
    dt = table.value.dt;
});

function rowEdit(row){
    id=row.id;
    form.name=row.name
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'");

    form.guard_name=row.guard_name
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'");
        
    modalform.value=true;
}

function add(){
    id=null;
    form.reset();
    modalform.value=true;
}
 
function store(e){
    if(id==null){
        form.post(route('permissions/create'), {
            onSuccess: () => {
                form.reset();
                dt.ajax.reload();
                modalform.value=false;
            },
        });
    }else{
        form.put(`/permissions/${id}/update`, {
            onSuccess: () => {
                form.reset();
                dt.ajax.reload();
                modalform.value=false;
            },
        });
    }
}
</script>
<template>
    <AppMain title="Permissions">
        <template #header >
            <div class="flex flex-row items-center" >
                <h2 class="font-semibold text-xl text-gray-800 leading-tight grow">
                    Listado de Permisos
                </h2>
                <button @click="add" class="bg-green-600 text-white px-4 py-2 rounded">
                    <font-awesome-icon :icon="fas.faAdd"/>  <span class="hidden sm:inline">Agregar Permisos</span>
                </button>
            </div>
        </template>

        <Modal :show="modalform" ref="formulario">
            <form @submit.prevent="store" class="p-4 sm:p-6 lg:p-8" >
                <h1>Permisos</h1>
                <div class="mt-4">
                    <InputLabel for="name" value="Nombre Permisos" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autocomplete="name"
                    />
                </div>
                <div class="mt-4">
                    <InputLabel for="guard_name" value="Guard Name" />
                    <SelectInput
                        id="guard_name"
                        v-model="form.guard_name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autocomplete="guard_name"
                        >
                        <option value="web">Web</option>
                        <option value="api">API</option>
                    </SelectInput>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <SecondaryButton @click="modalform=false">
                        Cerrar
                    </SecondaryButton>
                    <PrimaryButton>
                        {{id ? "Actualizar" : "Agregar"}}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>

        <div class="bg-white w-max sm:w-auto my-8 mx-auto max-w-7xl">
            <DataTable :ajax="route('permissions/table')" :columns="columns" ref="table" :options="{select: true,serverSide: true,}" class="display cell-border compact">
                <template #action="props">                    
                    <ButtonGroup>
                        <button
                            @click="rowEdit(props.rowData)"
                            title="Editar"
                            class="bg-blue-500 text-white px-4 py-2 rounded"
                            >
                            <font-awesome-icon :icon="fas.faPen"/>
                        </button>
                        <button
                            @click="edit(props.rowData)"
                            title="Eliminar"
                            class="bg-red-500 text-white px-4 py-2 rounded"
                            >
                            <font-awesome-icon :icon="fas.faTrash"/>
                        </button>
                    </ButtonGroup>
                </template>
            </DataTable>
        </div>
    </AppMain>
</template>
<style scoped>
    table {
        border-collapse: collapse;
    }
    th, td {
        text-align: left;
    }
</style>
