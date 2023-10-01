<script setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import PermissionsService from '@/service/PermissionsService';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const permissions = ref(null);
const permissionDialog = ref(false);
const deletePermissionDialog = ref(false);
const deletePermissionsDialog = ref(false);
const calendarValueApplicationDate = ref(new Date());
const calendarValuePermissionDate = ref(new Date());
const permission = ref({});
const selectedPermissions = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);
const tiposDePermisos = ref([{ value: 'CALAMIDAD DOMESTICA' }, { value: 'LICENCIA NO REMUNERADA' }, { value: 'LICENCIA REMUNERADA' }, { value: 'CONSULTA MEDICA' }, { value: 'ASUNTO PERSONAL' }, { value: 'ASUNTOS LABORALES' }]);

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
const onUpload = () => {
    toast.add({ severity: 'info', summary: 'Success', detail: 'File Uploaded', life: 3000 });
};

const openNew = () => {
    const today = new Date();
    const day = today.getDate().toString().padStart(2, '0'); // Obtiene el día y lo formatea a dos dígitos (por ejemplo, 08)
    const month = (today.getMonth() + 1).toString().padStart(2, '0'); // Obtiene el mes (se suma 1 ya que los meses en JavaScript comienzan en 0) y lo formatea a dos dígitos (por ejemplo, 08)
    const year = today.getFullYear(); // Obtiene el año

    // Obtener la fecha del calendario y formatearla
    const selectedDate = calendarValuePermissionDate.value;
    const formattedDate = selectedDate ? selectedDate.toLocaleDateString('es-CO', { day: '2-digit', month: '2-digit', year: '2-digit' }) : null;

    permission.value = {
        name: 'Hector Gomez',
        fechaSolicitud: '20-08-2023',
        fechaPermiso: formattedDate,
        horaPermiso: '10:00',
        category: 'Accessories',
        duracionPermiso: 2,
        Status: 'PENDIENTE',
        tipoPermiso: 'ASUNTO PERSONAL'
    };
    submitted.value = false;

    // Construye la fecha en el formato deseado
    permission.value.fechaSolicitud = `${day}-${month}-${year}`;
    permissionDialog.value = true;
};

const hideDialog = () => {
    permissionDialog.value = false;
    submitted.value = false;
};

const saveProduct = () => {
    console.log('Datos ingresados en el formulario:', permission.value);

    submitted.value = true;
    if (permission.value.fechaSolicitud && permission.value.fechaPermiso) {
        if (permission.value.id) {
            console.log('aqui 1');
            permission.value.Status = permission.value.Status.value ? permission.value.Status.value : permission.value.Status;
            permission.value[findIndexById(permission.value.id)] = permission.value;
            toast.add({ severity: 'success', summary: 'Successful', detail: 'permission Updated', life: 3000 });
        } else {
            console.log('aqui 2');
            permission.value.id = createId();
            console.log('Antes del push', permissions);
            permissions.value.push({ ...permission.value });
            console.log('Despues del push', permissions);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'permission Created', life: 3000 });
        }
        permissionDialog.value = false;
        permission.value = {};
    }
};

const editProduct = (editProduct) => {
    permission.value = { ...editProduct };
    console.log(permission);
    permissionDialog.value = true;
};

const confirmDeleteProduct = (editProduct) => {
    permission.value = editProduct;
    deletePermissionDialog.value = true;
};

const deleteProduct = () => {
    permissions.value = permissions.value.filter((val) => val.id !== permission.value.id);
    deletePermissionDialog.value = false;
    permission.value = {};
    toast.add({ severity: 'success', summary: 'Successful', detail: 'permission Deleted', life: 3000 });
};

const findIndexById = (id) => {
    let index = -1;
    for (let i = 0; i < permissions.value.length; i++) {
        if (permissions.value[i].id === id) {
            index = i;
            break;
        }
    }
    return index;
};

const createId = () => {
    let id = '';
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (let i = 0; i < 5; i++) {
        id += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return id;
};

const deleteSelectedProducts = () => {
    permissions.value = permissions.value.filter((val) => !selectedPermissions.value.includes(val));
    deletePermissionsDialog.value = false;
    selectedPermissions.value = null;
    toast.add({ severity: 'success', summary: 'Successful', detail: 'permissions Deleted', life: 3000 });
};

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
};
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <Toast />
                <Toolbar class="mb-4">
                    <template v-slot:start>
                        <div class="my-2">
                            <Button label="Solicitar Permiso" icon="pi pi-plus" class="p-button-success mr-2" @click="openNew" />
                        </div>
                    </template>
                </Toolbar>

                <DataTable
                    ref="dt"
                    :value="permissions"
                    v-model:selection="selectedPermissions"
                    dataKey="id"
                    :paginator="true"
                    :rows="10"
                    :filters="filters"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} permissions"
                    responsiveLayout="scroll"
                >
                    <template #header>
                        <div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
                            <h5 class="m-0">Permisos</h5>
                            <span class="block mt-2 md:mt-0 p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filters['global'].value" placeholder="Search..." />
                            </span>
                        </div>
                    </template>

                    <Column field="code" header="Empleado" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Empleado</span>
                            {{ slotProps.data.name }}
                        </template>
                    </Column>
                    <Column field="category" header="Fecha Solicitud" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Fecha Solicitud</span>
                            {{ slotProps.data.fechaSolicitud }}
                        </template>
                    </Column>
                    <Column field="category" header="Fecha Permiso" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Fecha Permiso</span>
                            <span>{{ slotProps.data.fechaPermiso }}</span>
                        </template>
                    </Column>
                    <Column field="Status" header="Estado De Solicitud" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Estado De Solicitud</span>
                            <span :class="'permission-badge status-' + (slotProps.data.Status ? slotProps.data.Status.toLowerCase() : '')">{{ slotProps.data.Status }}</span>
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editProduct(slotProps.data)" />
                            <Button icon="pi pi-trash" class="p-button-rounded p-button-warning mt-2" @click="confirmDeleteProduct(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="permissionDialog" :style="{ width: '950px' }" header="Solicitud De Permiso" :modal="true" class="p-fluid">
                    <div class="formgrid grid">
                        <div class="field col-3">
                            <label for="fechaSolicitud">Fecha Solicitud</label>
                            <Calendar id="fechaSolicitud" :showIcon="true" :showButtonBar="true" v-model="calendarValueApplicationDate" dateFormat="dd/mm/yy" readonly disabled></Calendar>
                        </div>
                        <div class="field col-3">
                            <label for="fechaPermiso">Fecha Permiso</label>
                            <Calendar id="fechaPermiso" :showIcon="true" :showButtonBar="true" v-model="calendarValuePermissionDate" dateFormat="dd/mm/yy"></Calendar>
                        </div>
                        <div class="field col-2">
                            <label for="horaPermiso">Hora Permiso:</label>
                            <InputText id="horaPermiso" v-model="permission.horaPermiso" type="time" />
                        </div>
                        <div class="field col-4">
                            <label for="duracionPermiso">Duración Del Permiso(Horas):</label>
                            <InputNumber id="duracionPermiso" v-model="permission.duracionPermiso" showButtons :min="1" mode="decimal"></InputNumber>
                        </div>
                    </div>

                    <div class="field">
                        <label for="tipoPermiso" class="mb-3">Tipo De Permiso</label>
                        <Dropdown id="tipoPermiso" v-model="permission.tipoPermiso" :options="tiposDePermisos" optionLabel="value" placeholder="Selecciona Tipo De Permiso"> </Dropdown>
                    </div>

                    <div class="field">
                        <h5>Soportes Presentados</h5>
                        <FileUpload name="demo[]" @uploader="onUpload" :multiple="true" accept="image/*,.pdf" :maxFileSize="1000000" customUpload />
                    </div>
                    <div class="field">
                        <label for="observaciones">Observaciones</label>
                        <Textarea id="observaciones" v-model="permission.observaciones" required="true" rows="3" cols="20" />
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                        <Button label="Solicitar" icon="pi pi-check" class="p-button-text" @click="saveProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deletePermissionDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="permission"
                            >Are you sure you want to delete <b>{{ permission.name }}</b
                            >?</span
                        >
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" class="p-button-text" @click="deletePermissionDialog = false" />
                        <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deletePermissionsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="permission">Are you sure you want to delete the selected permissions?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" class="p-button-text" @click="deletePermissionsDialog = false" />
                        <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteSelectedProducts" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss"></style>
