<script setup>
import AppMain from '@/Layouts/AppMain.vue';

import ButtonGroup from '@/Components/ButtonGroup.vue';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faPen, faTrash, faAdd, faUsers} from '@fortawesome/free-solid-svg-icons'

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-dt';
import DataTableEs from '@/Composables/datatableEs.js';

import { useForm } from '@inertiajs/vue3';
import {onMounted, ref} from "vue";

DataTable.use(DataTablesCore);

const columns = [
    { data: 'id', title: 'Nº',width:'1%'},
    { data: 'name', title: 'Nombre' },
    { data: 'email', title: 'Correo'},
    { data: 'roles',render:'#roles', title: 'Roles' },
    { data: null,render: '#action', title: 'Acción',width:'1%', className: 'ip-0' }
];

const form = useForm ({
    name: "",
});

let dt;
const table = ref();
 
onMounted(() => {
    dt = table.value.dt;
});

</script>
<template>
    <AppMain title="Usuarios">
        <div class="bg-white w-max sm:w-auto mx-auto max-w-7xl mt-2 rounded-lg shadow-md">
            <div class="flex flex-row items-center justify-between p-2">
                <h1 class="font-semibold text-xl text-gray-800 leading-tight grow">
                    <FontAwesomeIcon :icon="faUsers"/>
                    Listado de Usuarios
                </h1>
                <a :href="route('users/add')" class="bg-green-600 text-white px-2 py-1 rounded flex items-center">
                    <FontAwesomeIcon :icon="faAdd" class="size-3 sm:size-4"/>
                    <span class="text-sm sm:text-base">Agregar usuario</span>
                </a>
            </div>
            <div class="px-2">
                <DataTable :ajax="route('users/table')" :columns="columns" ref="table" :options="{select: true,serverSide: true,language:DataTableEs}" class="cell-border compact">
                    <template #roles="props">
                        <ul>
                            <li v-for="role in props.rowData.roles" :key="role.id">
                                {{ role.name }}
                            </li>
                        </ul>

                    </template>
                    <template #action="props">                    
                        <ButtonGroup>
                            <a
                                :href="route('users/select', { id: props.rowData.id })"
                                title="Editar"
                                class="bg-blue-500 text-white py-1 px-2 rounded"
                                >
                                <FontAwesomeIcon :icon="faPen" class="size-4"/>
                            </a>
                            <button
                                @click="edit(props.rowData)"
                                title="Eliminar"
                                class="bg-red-500 text-white py-1 px-2 rounded"
                                >
                                <FontAwesomeIcon :icon="faTrash" class="size-4"/>
                            </button>
                        </ButtonGroup>
                    </template>
                </DataTable>
            </div>
        </div>
    </AppMain>
</template>
<script>
import { Inertia } from '@inertiajs/inertia';

export default {
  props: {
    users: Object,
  },
  methods: {
    goTo(link) {
      Inertia.get(link.url);
    },    
    edit(userId) {
      Inertia.get(`/users/${userId}`);
    },
  },
};
</script>

<style scoped>
    table {
        border-collapse: collapse;
    }
    th,
    td {
        text-align: left;
    }
</style>
