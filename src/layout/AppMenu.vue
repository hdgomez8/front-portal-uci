<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

// Obtener el usuario desde localStorage
const usuarioJSON = localStorage.getItem('user');
const usuario = usuarioJSON ? JSON.parse(usuarioJSON) : null;
const userId = usuario?.id || ''; // Asegúrate de que el ID del usuario esté disponible

// Aquí puedes definir tu lógica para decidir si mostrar o no la opción de configuración
const showConfig = usuario?.manager?.id === userId; // Por ejemplo, mostrar solo si el usuario es el manager

const model = ref([
    {
        label: 'PORTAL UCI',
        items: [{ label: 'Panel', icon: 'pi pi-fw pi-home', to: '/' }]
    },
    {
        label: 'MODULOS',
        items: [
            {
                label: 'Permisos',
                icon: 'pi pi-fw pi-pencil',
                to: '/pages/crudPermisos',

            },
            {
                label: 'Vacaciones',
                icon: 'pi pi-fw pi-car',
                to: '/pages/crudVacaciones',
                visible: false
            },
            {
                label: 'Turnos',
                icon: 'pi pi-fw pi-users',
                to: '/pages/crudTurnos',
                visible: false
            },
            {
                label: 'Cesantias',
                icon: 'pi pi-fw pi-money-bill',
                to: '/pages/crudCesantias',
                visible: false
            }
        ]
    },
    {
        label: 'Configuración',
        items: [
            {
                label: 'Usuarios',
                icon: 'pi pi-fw pi-user',
                to: '/pages/configuraciones/crudUsuarios',
                visible: showConfig // Mostrar solo si cumple con la lógica definida
            }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator && (item.visible !== false)" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
