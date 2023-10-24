<script setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import VacationsService from '@/service/VacationsService';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const vacations = ref(null);
const vacationsDialog = ref(false);
const deletePermissionDialog = ref(false);
const deletePermissionsDialog = ref(false);
const calendarValueApplicationDate = ref(new Date());
const calendarValuePermissionDate = ref(new Date());
const vacation = ref({});
const selectedPermissions = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);
const radioValue = ref(null);
const tiposDePermisos = ref([{ value: 'CALAMIDAD DOMESTICA' }, { value: 'LICENCIA NO REMUNERADA' }, { value: 'LICENCIA REMUNERADA' }, { value: 'CONSULTA MEDICA' }, { value: 'ASUNTO PERSONAL' }, { value: 'ASUNTOS LABORALES' }]);

const vacationsService = new VacationsService();

onBeforeMount(() => {
    initFilters();
});
onMounted(() => {
    vacationsService.getVacations().then((data) => {
        vacations.value = data;
        // Agrega un console.log para verificar la estructura de vacations.value
        console.log('Estructura de vacations.value:', vacations.value);
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

    vacation.value = {
        name: 'Hector Gomez',
        cargo: 'Desarrollador',
        area: 'TI',
        jefeInmediato: 'Paula Londoño',
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
    vacation.value.fechaSolicitud = `${day}-${month}-${year}`;
    vacationsDialog.value = true;
};

const hideDialog = () => {
    vacationsDialog.value = false;
    submitted.value = false;
};

const saveProduct = () => {
    console.log('Datos ingresados en el formulario:', vacation.value);

    submitted.value = true;
    if (vacation.value.fechaSolicitud && vacation.value.fechaPermiso) {
        if (vacation.value.id) {
            console.log('aqui 1');
            vacation.value.Status = vacation.value.Status.value ? vacation.value.Status.value : vacation.value.Status;
            vacation.value[findIndexById(vacation.value.id)] = vacation.value;
            toast.add({ severity: 'success', summary: 'Successful', detail: 'vacation Updated', life: 3000 });
        } else {
            console.log('aqui 2');
            vacation.value.id = createId();
            console.log('Antes del push', vacations);
            vacations.value.push({ ...vacation.value });
            console.log('Despues del push', vacations);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'vacation Created', life: 3000 });
        }
        vacationsDialog.value = false;
        vacation.value = {};
    }
};

const editProduct = (editProduct) => {
    vacation.value = { ...editProduct };
    console.log(vacation);
    vacationsDialog.value = true;
};

const deleteProduct = () => {
    vacations.value = vacations.value.filter((val) => val.id !== vacation.value.id);
    deletePermissionDialog.value = false;
    vacation.value = {};
    toast.add({ severity: 'success', summary: 'Successful', detail: 'vacation Deleted', life: 3000 });
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

const deleteSelectedProducts = () => {
    vacations.value = vacations.value.filter((val) => !selectedPermissions.value.includes(val));
    deletePermissionsDialog.value = false;
    selectedPermissions.value = null;
    toast.add({ severity: 'success', summary: 'Successful', detail: 'vacations Deleted', life: 3000 });
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
                            <Button label="Solicitar Vacaciones" icon="pi pi-plus" class="p-button-success mr-2" @click="openNew" />
                        </div>
                    </template>
                </Toolbar>

                <DataTable
                    ref="dt"
                    :value="vacations"
                    v-model:selection="selectedPermissions"
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
                            <h5 class="m-0">Vacaciones</h5>
                            <span class="block mt-2 md:mt-0 p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filters['global'].value" placeholder="Search..." />
                            </span>
                        </div>
                    </template>

                    <Column field="code" header="Empleado" :sortable="true" headerStyle="width:30%; min-width:10rem;">
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
                            <span :class="'vacation-badge status-' + (slotProps.data.Status ? slotProps.data.Status.toLowerCase() : '')">{{ slotProps.data.Status }}</span>
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editProduct(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="vacationsDialog" :style="{ width: '950px' }" header="Solicitud De Vacaciones" :modal="true" class="p-fluid">
                    <div class="formgrid grid">
                        <div class="field col-3">
                            <label for="ciudadDepartamento">Ciudad/Departamento</label>
                            <InputText id="ciudadDepartamento" :showIcon="true" :showButtonBar="true" v-model="vacation.ciudadDepartamento" readonly disabled />
                        </div>
                        <div class="field col-3">
                            <label for="fechaSolicitud">Fecha Solicitud</label>
                            <Calendar id="fechaSolicitud" :showIcon="true" :showButtonBar="true" v-model="calendarValueApplicationDate" dateFormat="dd/mm/yy" readonly disabled></Calendar>
                        </div>
                        <div class="field col-3">
                            <label for="nombreEmpleado">Nombre Empleado</label>
                            <InputText id="nombreEmpleado" :showIcon="true" :showButtonBar="true" v-model="vacation.name" readonly disabled />
                        </div>
                        <div class="field col-3">
                            <label for="identificacionEmpleado">Identificación Empleado</label>
                            <InputText id="identificacionEmpleado" :showIcon="true" :showButtonBar="true" v-model="vacation.identificacion" readonly disabled />
                        </div>
                    </div>

                    <div class="formgrid grid">
                        <div class="field col-3">
                            <label for="cargo">Cargo</label>
                            <InputText id="cargo" :showIcon="true" :showButtonBar="true" v-model="vacation.cargo" readonly disabled />
                        </div>
                        <div class="field col-3">
                            <label for="area">Area</label>
                            <InputText id="area" :showIcon="true" :showButtonBar="true" v-model="vacation.area" readonly disabled />
                        </div>
                        <div class="field col-3">
                            <label for="jefeInmediato">Jefe Inmediato</label>
                            <InputText id="jefeInmediato" :showIcon="true" :showButtonBar="true" v-model="vacation.jefeInmediato" readonly disabled />
                        </div>
                    </div>

                    <div class="card p-fluid">
                        <h5>Periodo De Vacaciones Cumplidas</h5>
                        <div class="formgrid grid">
                            <div class="field col">
                                <label for="name2">Desde</label>
                                <InputText id="name2" type="text" />
                            </div>
                            <div class="field col">
                                <label for="email2">Hasta</label>
                                <InputText id="email2" type="text" />
                            </div>
                            <div class="field col">
                                <label for="email2">Dias Totales a los que tiene derecho</label>
                                <InputText id="email2" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="card p-fluid">
                        <h5>Periodo en el cual disfrutara las vacaciones</h5>
                        <div class="formgrid grid">
                            <div class="field col">
                                <label for="name2">Desde</label>
                                <InputText id="name2" type="text" />
                            </div>
                            <div class="field col">
                                <label for="email2">Hasta</label>
                                <InputText id="email2" type="text" />
                            </div>
                            <div class="field col">
                                <label for="email2">Dias Totales de disfrute</label>
                                <InputText id="email2" type="text" />
                            </div>
                            <div class="field col">
                                <label for="email2">Dias solicitados con pagos</label>
                                <div class="field">
                                    <div class="col-12 md:col-4">
                                        <div class="field-radiobutton mb-0">
                                            <RadioButton id="option1" name="option" value="Si" v-model="radioValue" />
                                            <label for="option1">Si</label>
                                        </div>
                                        <div class="field" v-if="radioValue === 'Si'">
                                            <div class="field">
                                                <label for="numero">Dias:</label>
                                                <input type="text" id="numero" v-model="numeroValue" class="small-input"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 md:col-4">
                                        <div class="field-radiobutton mb-0">
                                            <RadioButton id="option2" name="option" value="No" v-model="radioValue" />
                                            <label for="option2">N/A</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                        <Button label="Solicitar" icon="pi pi-check" class="p-button-text" @click="saveProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deletePermissionDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="vacation"
                            >Are you sure you want to delete <b>{{ vacation.name }}</b
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
                        <span v-if="vacation">Are you sure you want to delete the selected vacations?</span>
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
