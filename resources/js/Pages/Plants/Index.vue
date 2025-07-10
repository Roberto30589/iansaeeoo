<script setup>
import AppMain from '@/Layouts/AppMain.vue';
import { useForm } from '@inertiajs/vue3';

import ButtonGroup from '@/Components/ButtonGroup.vue';
import ButtonColor from '@/Components/ButtonColor.vue';

import Modal from '@/Components/Modal.vue';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faPen, faTrash, faAdd, faBuilding} from '@fortawesome/free-solid-svg-icons'

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-dt';
import {DataTableEs} from '@/Composables/datatableEs.js';

import {onMounted, ref} from "vue";

DataTable.use(DataTablesCore);

const columns = [
    { data: 'id', title: 'Nº',width:'1%'},
    { data: 'name', title: 'Nombre' },
    { data: 'areas',render: (data) => {
        return data.map(area => area.name).join(', ');
    } , title: 'Áreas' },
    { data: null,render: '#action', title: 'Acción',width:'1%', className: 'ip-0' }
];

let dt;
const table = ref();
 
onMounted(() => {
    dt = table.value.dt;
});

let modalOpen = ref(false);
let selectedRow = ref(null);

const deleteShow = (rowData) => {
    modalOpen.value = true;
    selectedRow.value = rowData;
};

const deleteRow = () => {
    const form = useForm ({});
    form.delete(route('plants/delete', { id: selectedRow.value.id }), {
        onSuccess: () => {
            modalOpen.value = false;
            dt.ajax.reload();
        },
        onError: () => {
            modalOpen.value = false;
        }
    });
};
</script>
<template>
    <AppMain title="Usuarios">
        <div class="bg-white w-max sm:w-auto mx-auto max-w-7xl mt-2 rounded-lg shadow-md">
            <div class="flex flex-row items-center justify-between p-2">
                <h1 class="font-semibold text-xl text-gray-800 leading-tight grow">
                    <FontAwesomeIcon :icon="faBuilding"/>
                    Listado de Plantas
                </h1>
                <ButtonColor :href="route('plants/add')" class="bg-green-600 text-white px-2 py-1 rounded flex items-center">
                    <FontAwesomeIcon :icon="faAdd" class="size-4 sm:pe-2"/>
                    <span class="hidden sm:inline">Agregar Planta</span>
                </ButtonColor>
            </div>
            <div class="px-2">
                <DataTable :ajax="route('plants/table')" :columns="columns" ref="table" :options="{select: true,serverSide: true,language:DataTableEs}" class="cell-border compact">
                    <template #areas="props">
                            <span v-for="area in props.rowData.areas" :key="area.id">
                                {{ area.name }}
                            </span>
                    </template>
                    <template #action="props">                    
                        <ButtonGroup>
                            <ButtonColor color="blue"
                                :href="route('plants/select', { id: props.rowData.id })"
                                title="Editar"
                                >
                                <FontAwesomeIcon :icon="faPen" class="size-4"/>
                            </ButtonColor>
                            <ButtonColor color="red"
                                @click="deleteShow(props.rowData)"
                                title="Eliminar"
                                >
                                <FontAwesomeIcon :icon="faTrash" class="size-4"/>
                            </ButtonColor>
                        </ButtonGroup>
                    </template>
                </DataTable>
            </div>
        </div>
        <Modal :title="'Eliminar Planta'" :show="modalOpen" @close="modalOpen = false">
            <div class="flex items-center justify-between border-b p-3 bg-red-700 text-white">
                <h3>Eliminar Planta</h3>
                <div class="cursor-pointer text-[16px] hover:shadow-xl/20" @click="modalOpen = false">X</div>
            </div>
            <div class="p-3">
                <p>¿Está seguro de que desea eliminar la planta <b>{{selectedRow ? selectedRow.name : ''}}</b>?</p>
            </div>
            <div class="flex items-center justify-between border-t p-3">
                <ButtonColor @click="modalOpen = false" color="gray">Cancelar</ButtonColor>
                <ButtonColor @click="deleteRow" color="red">Eliminar</ButtonColor>
            </div>
        </Modal>
    </AppMain>
</template>
<style scoped>
    table {
        border-collapse: collapse;
    }
    th,
    td {
        text-align: left;
    }
</style>
