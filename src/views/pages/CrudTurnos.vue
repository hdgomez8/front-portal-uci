<script setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import ProductService from '@/service/ProductService';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const vacations = ref(null);
const vacationDialog = ref(false);
const deleteVacationDialog = ref(false);
const deleteVacationsDialog = ref(false);
const calendarValueApplicationDate = ref(new Date());
const calendarValueVacationDate = ref(null);
const inputNumberVacationDuration = ref(null);
const selectedTimeLeaveTime = ref(null);
const vacation = ref({});
const selectedVacations = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);
const statuses = ref([
    { label: 'APROBADA', value: 'aprobada' },
    { label: 'PENDIENTE', value: 'pendiente' },
    { label: 'RECHAZADA', value: 'rechazada' }
]);
const tiposDeVacaciones = ref([
    { label: 'VACACIONES PAGADAS', value: 'vacaciones_pagadas' },
    { label: 'VACACIONES SIN PAGO', value: 'vacaciones_sin_pago' },
    { label: 'PERMISO CON SUELDO', value: 'permiso_con_sueldo' },
    { label: 'PERMISO SIN SUELDO', value: 'permiso_sin_sueldo' },
    { label: 'OTRO', value: 'otro' }
]);
const desdeCuando = ref(null); // Agregar referencia para "Desde cuando"
const hastaCuando = ref(null); // Agregar referencia para "Hasta cuando"
const duracionVacaciones = ref(0); // Variable para calcular la duración de las vacaciones

const productService = new ProductService();

onBeforeMount(() => {
    initFilters();
});
onMounted(() => {
    productService.getEmpleados().then((data) => (vacations.value = data));
});
const onUpload = () => {
    toast.add({ severity: 'info', summary: 'Success', detail: 'File Uploaded', life: 3000 });
};
const formatCurrency = (value) => {
    return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
};

const openNew = () => {
    vacation.value = {};
    submitted.value = false;
    vacation.fechaSolicitud = new Date();
    vacationDialog.value = true;
};

const hideDialog = () => {
    vacationDialog.value = false;
    submitted.value = false;
};

const saveVacation = () => {
    submitted.value = true;
    if (vacation.value.fechaSolicitud && vacation.value.fechaVacacion) {
        if (vacation.value.id) {
            vacation.value.inventoryStatus = vacation.value.inventoryStatus.value ? vacation.value.inventoryStatus.value : vacation.value.inventoryStatus;
            vacation.value[findIndexById(vacation.value.id)] = vacation.value;
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Vacation Updated', life: 3000 });
        } else {
            vacation.value.id = createId();
            vacation.value.inventoryStatus = vacation.value.inventoryStatus ? vacation.value.inventoryStatus.value : 'PENDIENTE';
            vacation.value.push(vacation.value);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Vacation Created', life: 3000 });
        }
        vacationDialog.value = false;
        vacation.value = {};
    }
};

const editVacation = (editVacation) => {
    vacation.value = { ...editVacation };
    vacationDialog.value = true;
};

const confirmDeleteVacation = (editVacation) => {
    vacation.value = editVacation;
    deleteVacationDialog.value = true;
};

const deleteVacation = () => {
    vacations.value = vacations.value.filter((val) => val.id !== vacation.value.id);
    deleteVacationDialog.value = false;
    vacation.value = {};
    toast.add({ severity: 'success', summary: 'Successful', detail: 'Vacation Deleted', life: 3000 });
};

const findIndexById = (id) => {
    let index = -1;
    for (let i = 0; i < vacations.value.length; i++) {
        if (vacations.value[i].id === id) {
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

const exportCSV = () => {
    dt.value.exportCSV();
};

const confirmDeleteSelected = () => {
    deleteVacationsDialog.value = true;
};

const deleteSelectedVacations = () => {
    vacations.value = vacations.value.filter((val) => !selectedVacations.value.includes(val));
    deleteVacationsDialog.value = false;
    selectedVacations.value = null;
    toast.add({ severity: 'success', summary: 'Successful', detail: 'Vacations Deleted', life: 3000 });
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
                            <Button label="Solicitar Cambio Turno" icon="pi pi-plus" class="p-button-success mr-2" @click="openNew" />
                        </div>
                    </template>
                </Toolbar>

                <DataTable
                    ref="dt"
                    :value="vacations"
                    v-model:selection="selectedVacations"
                    dataKey="id"
                    :paginator="true"
                    :rows="10"
                    :filters="filters"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} vacations"
                    responsiveLayout="scroll"
                >
                    <template #header>
                        <div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
                            <h5 class="m-0">Cambio De Turnos</h5>
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
                    <Column field="category" header="Fecha Vacación" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Fecha Vacación</span>
                            {{ slotProps.data.fechaVacacion }}
                        </template>
                    </Column>
                    <Column field="inventoryStatus" header="Tipo De Solicitud" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Tipo De Solicitud</span>
                            <span :class="'product-badge status-' + (slotProps.data.inventoryStatus ? slotProps.data.inventoryStatus.toLowerCase() : '')">{{ slotProps.data.inventoryStatus }}</span>
                        </template>
                    </Column>
                    <Column field="code" header="Tipo De Permiso" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Tipo De Permiso</span>
                            {{ slotProps.data.tipoPermiso }}
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editVacation(slotProps.data)" />
                            <Button icon="pi pi-trash" class="p-button-rounded p-button-warning mt-2" @click="confirmDeleteVacation(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="vacationDialog" :style="{ width: '850px' }" header="Solicitud De Vacaciones" :modal="true" class="p-fluid">
                    <div class="formgrid grid">
                        <div class="field col-3">
                            <label for="fechaSolicitud">Fecha Solicitud</label>
                            <Calendar id="fechaSolicitud" :showIcon="true" :showButtonBar="true" v-model="calendarValueApplicationDate" dateFormat="dd/mm/yy" readonly></Calendar>
                        </div>
                        <div class="field col-3">
                            <label for="fechaVacacion">Fecha Vacación</label>
                            <Calendar id="fechaVacacion" :showIcon="true" :showButtonBar="true" v-model="vacation.fechaVacacion"></Calendar>
                        </div>
                        <!-- Otras entradas para la solicitud de vacaciones -->
                    </div>

                    <div class="field">
                        <label for="tipoVacacion" class="mb-3">Tipo De Vacación</label>
                        <Dropdown id="tipoVacacion" v-model="vacation.tipoVacacion" :options="tiposDeVacaciones" optionLabel="label" placeholder="Selecciona Tipo De Vacación">
                            <template #value="slotProps">
                                <div v-if="slotProps.value && slotProps.value.value">
                                    <span :class="'product-badge status-' + slotProps.value.value">{{ slotProps.value.label }}</span>
                                </div>
                                <div v-else-if="slotProps.value && !slotProps.value.value">
                                    <span :class="'product-badge status-' + slotProps.value.toLowerCase()">{{ slotProps.value }}</span>
                                </div>
                                <span v-else>
                                    {{ slotProps.placeholder }}
                                </span>
                            </template>
                        </Dropdown>
                    </div>
                    <!-- Otras entradas para la solicitud de vacaciones -->
                </Dialog>

                <Dialog v-model:visible="deleteVacationDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="vacation"
                            >Are you sure you want to delete <b>{{ vacation.name }}</b
                            >?</span
                        >
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteVacationDialog = false" />
                        <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteVacation" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteVacationsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="vacation">Are you sure you want to delete the selected vacations?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteVacationsDialog = false" />
                        <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteSelectedVacations" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss"></style>
