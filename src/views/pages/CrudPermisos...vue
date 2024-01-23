<script setup>
import { ref, onMounted, onBeforeMount } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from 'primevue/api';
import PermissionsService from '@/service/PermissionsService';
// ... (otras importaciones)

const toast = useToast();
const permissions = ref(null);
const permissionDialog = ref(false);
const deletePermissionDialog = ref(false);
const deletePermissionsDialog = ref(false);
const calendarValueApplicationDate = ref(new Date());
const calendarValuePermissionDate = ref(null);
const inputNumberPermitDuration = ref(null);
const selectedTimeLeaveTime = ref(null);
const permission = ref({});
const selectedPermissions = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);
const statuses = ref([
    // ... (definir los objetos de estado)
]);
const tiposDePermisos = ref([
    // ... (definir los tipos de permisos)
]);
const permissionsService = new PermissionsService();

onBeforeMount(() => {
    initFilters();
});

onMounted(() => {
    permissionsService.getEmpleados().then((data) => {
        permissions.value = data;
        // Agrega un console.log para verificar la estructura de permissions.value
        console.log('Estructura de permissions.value:', permissions.value);
    });
});

const openNew = () => {
    // Lógica para abrir el diálogo de solicitud de permiso aquí
};

const hideDialog = () => {
    permissionDialog.value = false;
    submitted.value = false;
};
</script>

<template>
    <div class="grid">
        <!-- Aquí puedes agregar la estructura de la página, como encabezados y botones -->
    </div>
</template>
