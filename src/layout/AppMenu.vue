<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

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
                to: '/pages/crudPermisos'
            },
            {
                label: 'Vacaciones',
                icon: 'pi pi-fw pi-car',
                to: '/pages/crudVacaciones'
            },
            {
                label: 'Turnos',
                icon: 'pi pi-fw pi-users',
                to: '/pages/crudTurnos'
            },
            {
                label: 'Cesantias',
                icon: 'pi pi-fw pi-money-bill',
                to: '/pages/crudCesantias'
            }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
