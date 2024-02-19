<script setup>
//Importaciones
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';

import { useToast } from 'primevue/usetoast';
import axios from 'axios';
import { useStore } from '@/store';

//Variables
const toast = useToast();
const permissions = ref(null);
const permissionDialog = ref(false);
const deletePermissionDialog = ref(false);
const deletePermissionsDialog = ref(false);
const usuarioJSON = localStorage.getItem('user');
const usuario = JSON.parse(usuarioJSON);

const permission = ref({});
const selectedPermissions = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);
const tiposDePermisos = ref([
    { value: 'SELECCIONE', id: null },
    { value: 'CALAMIDAD DOMESTICA', id: 1 },
    { value: 'LICENCIA NO REMUNERADA', id: 2 },
    { value: 'LICENCIA REMUNERADA', id: 3 },
    { value: 'CONSULTA MEDICA', id: 4 },
    { value: 'ASUNTO PERSONAL', id: 5 },
    { value: 'ASUNTOS LABORALES', id: 6 }
]);
const minDate = ref(new Date());
const soportesPresentados = ref([]);

//Ciclos De Vida
/**/
onBeforeMount(() => {
    initFilters();
});
/*llamar informacion del servicio-obtener permisos*/

onMounted(async () => {
    try {
        const response = await axios.get('/requests');
        const { data } = response.data;
        permissions.value = data;
        const userJSON = localStorage.getItem('user');
        const user = JSON.parse(userJSON);
        console.log(user);
    } catch (error) {
        console.error('Error al obtener permisos:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al obtener permisos', life: 3000 });
    }
});

//Funciones
const onUpload = (files) => {
    // Agrega los archivos cargados al arreglo soportesPresentados
    soportesPresentados.value = files;

    toast.add({ severity: 'info', summary: 'Success', detail: 'File Uploaded', life: 3000 });
};

const convertTimeToSeconds = (timeString) => {
    if (typeof timeString !== 'string' || !timeString.includes(':')) {
        console.error('Invalid time format:', timeString);
        return null; // O maneja el error de alguna otra manera
    }

    const [hours, minutes] = timeString.split(':').map(Number);
    const totalSeconds = hours * 3600 + minutes * 60;
    const formattedTime = new Date(totalSeconds * 1000).toISOString().substr(11, 8);

    return formattedTime;
};

const openNew = () => {
    const userJSON = localStorage.getItem('user');
    const user = JSON.parse(userJSON);

    // Obtener la fecha actual
    const fechaActual = new Date();
    const dia = fechaActual.getDate().toString().padStart(2, '0');
    const mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0'); // Se suma 1 porque los meses van de 0 a 11
    const anno = fechaActual.getFullYear();

    const fechaActualString = `${dia}/${mes}/${anno}`;

    permission.value = {
        employeeFullName: `${user.first_name} ${user.last_name}`,
        employee: {
            title: user.title,
            department: user.department.length > 0 ? [user.department[0]] : []
        },
        jefeInmediato: `${user.manager.first_name} ${user.manager.last_name}`,
        fechaSolicitud: fechaActualString,
        tipoPermiso: { id: null },
        files: [],
        time: '00:00:00',
        long: null,
        observations: '',
        boton: 'nuevo',
    };
    console.log('usuario value', permission.value);
    submitted.value = false;

    permissionDialog.value = true;
};

const hideDialog = () => {
    permissionDialog.value = false;
    submitted.value = false;
};

const handleStatusChange = async (status) => {
    console.log(permission.value);
    permission.value.status = status;
    permission.value.time = convertTimeToSeconds(permission.value.time);
    const response = await axios.put(`/requests/${permission.value.id}`, { ...permission.value });
    const statusValue = status === 'approve' ? 'Aprobado' : 'Rechazado';
    if ((response.status = 200)) {
        toast.add({ severity: 'success', summary: 'Successful', detail: `Permiso ${statusValue}`, life: 3000 });
        permissionDialog.value = false;
        window.location.reload();
    } else {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al crear permiso', life: 3000 });
    }
};

const savePermission = () => {
    const data = new FormData();
    data.append('request_type_id', permission.value.tipoPermiso.id);
    data.append('date', permission.value.fechaPermiso.toISOString().split('T')[0]);
    data.append('time', convertTimeToSeconds(permission.value.time));
    data.append('long', permission.value.long);
    data.append('observations', permission.value.observations);

    if (soportesPresentados.value.length > 0) {
        soportesPresentados.value.files.forEach((file) => {
            data.append('files[]', file);
        });
    }

    axios
        .post('/requests', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(() => {
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Permiso Creado', life: 3000 });
            permissionDialog.value = false;
            window.location.reload();
        })
        .catch((error) => {
            console.error('Error al crear permiso:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Error al crear permiso', life: 3000 });
        });
};

const editPermission = (editPermission) => {
    const userJSON = localStorage.getItem('user');
    const user = JSON.parse(userJSON);

    permission.value = {
        ...editPermission,
        fechaSolicitud: `${formatDate(editPermission.created_at)}`,
        fechaPermiso: `${formatDate(editPermission.date)}`,
        employeeFullName: editPermission.employee ? `${editPermission.employee.first_name} ${editPermission.employee.last_name}` : '',
        time: `${formatHour(editPermission.time)}`,
        tipoPermiso: editPermission.type,
        employee: {
            title: editPermission.employee.title,
            department: editPermission.employee.department.length > 0 ? [editPermission.employee.department[0]] : []
        },
        jefeInmediato: editPermission.employee && editPermission.employee.manager ? `${editPermission.employee.manager.first_name} ${editPermission.employee.manager.last_name}` : '',
        boton: 'editar',
        role: `${user.manager.role}`
    };
    console.log('permiso editar', permission.value.time);
    permissionDialog.value = true;
};

const deleteProduct = () => {
    permissions.value = permissions.value.filter((val) => val.id !== permission.value.id);
    deletePermissionDialog.value = false;
    permission.value = {};
    toast.add({ severity: 'success', summary: 'Successful', detail: 'permission Deleted', life: 3000 });
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

const formatDate = (dateString) => {
    // Crea un objeto Date a partir de la cadena de fecha
    const dateObject = new Date(dateString);

    // Obtiene el día, mes y año
    const day = dateObject.getDate();
    const month = dateObject.getMonth() + 1; // ¡Recuerda sumar 1 al mes!
    const year = dateObject.getFullYear();

    // Formatea los valores para asegurar que tengan dos dígitos
    const formattedDay = day.toString().padStart(2, '0');
    const formattedMonth = month.toString().padStart(2, '0');

    // Devuelve la fecha formateada
    return `${formattedDay}/${formattedMonth}/${year}`;
};

const formatHour = (timeString) => {
    // Crea un objeto Date a partir de la cadena de tiempo
    const timeObject = new Date(timeString);

    // Obtiene la hora y los minutos
    const hours = timeObject.getUTCHours();
    const minutes = timeObject.getUTCMinutes();

    // Formatea los valores para asegurar que tengan dos dígitos
    const formattedHours = hours.toString().padStart(2, '0');
    const formattedMinutes = minutes.toString().padStart(2, '0');

    // Devuelve la hora formateada
    return `${formattedHours}:${formattedMinutes}`;
};

const acceptPermission = async (id) => {
    try {
        const response = await axios.get(`/requests/${id}`);
        const { data } = response.data;
        data.status = 'approve';

        // Realizar la solicitud para actualizar la solicitud con el ID proporcionado y los datos actualizados
        const updateResponse = await axios.put(`/requests/${id}`, data);

        // Manejar la respuesta de la solicitud de actualización
        if (updateResponse.status === 200) {
            // Éxito: la solicitud se actualizó correctamente
            console.log('Solicitud aceptada correctamente');
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Solicitud aceptada correctamente', life: 3000 });
        } else {
            // Error: la solicitud no se actualizó correctamente
            console.error('Error al aceptar la solicitud:', updateResponse.statusText);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Error al aceptar la solicitud', life: 3000 });
        }
    } catch (error) {
        // Capturar y manejar errores de la solicitud
        console.error('Error al actualizar la solicitud:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al actualizar la solicitud', life: 3000 });
    }
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
                            <Button v-if="usuario.department[0].pivot.role !== 'lead'" label="Solicitar Permiso" icon="pi pi-plus" class="p-button-success mr-2" @click="openNew" />
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
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} permisos"
                    responsiveLayout="scroll"
                >
                    <template #header>
                        <div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
                            <h5 class="m-0">Permisos</h5>
                            <span class="block mt-2 md:mt-0 p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                            </span>
                        </div>
                    </template>

                    <Column field="code" header="Empleado" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Empleado</span>
                            {{ slotProps.data.employee.first_name }} {{ slotProps.data.employee.last_name }}
                        </template>
                    </Column>
                    <Column field="category" header="Fecha Solicitud" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Fecha Solicitud</span>
                            <span>{{ formatDate(slotProps.data.created_at) }}</span>
                        </template>
                    </Column>
                    <Column field="category" header="Fecha Permiso" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Fecha Permiso</span>
                            <span>{{ formatDate(slotProps.data.date) }}</span>
                        </template>
                    </Column>
                    <Column field="Status" header="Estado De Solicitud" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Estado De Solicitud</span>
                            <span :class="'permission-badge status-' + (slotProps.data.status ? slotProps.data.status.toLowerCase() : '')">{{ slotProps.data.status === 'open' ? 'ABIERTO' : slotProps.data.status.toUpperCase() }}</span>
                        </template>
                    </Column>
                    <Column field="type" header="Tipo Permiso" :sortable="true" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Tipo Del Permiso</span>
                            <span :class="'permission-badge status-' + (slotProps.data.type.name ? slotProps.data.type.name.toLowerCase() : '')">{{ slotProps.data.type.name.toUpperCase() }}</span>
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button v-if="slotProps.data.status == 'open'" icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2" @click="editPermission(slotProps.data)" />
                            <Button v-if="slotProps.data.status == 'approve'" icon="pi pi-file-pdf" class="p-button-rounded p-button-danger mr-2" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="permissionDialog" :style="{ width: '1100px' }" header="Solicitud De Permiso" :modal="true" class="p-fluid">
                    <div class="formgrid grid">
                        <div class="field col-4">
                            <label for="nombreEmpleado">Nombre Empleado</label>
                            <InputText id="nombreEmpleado" :showIcon="true" :showButtonBar="true" v-model="permission.employeeFullName" readonly disabled />
                        </div>
                        <div class="field col-3">
                            <label for="cargo">Cargo</label>
                            <InputText id="cargo" :showIcon="true" :showButtonBar="true" v-model="permission.employee.title" readonly disabled />
                        </div>
                        <div class="field col-2">
                            <label for="area">Area</label>
                            <InputText id="area" :showIcon="true" :showButtonBar="true" v-model="permission.employee.department[0].name" :value="permission.employee.department[0].name.toUpperCase()" readonly disabled />
                        </div>
                        <div class="field col-3">
                            <label for="jefeInmediato">Jefe Inmediato</label>
                            <InputText id="jefeInmediato" :showIcon="true" :showButtonBar="true" v-model="permission.jefeInmediato" readonly disabled />
                        </div>
                    </div>

                    <div class="formgrid grid">
                        <div class="field col-3">
                            <label for="fechaSolicitud">Fecha Solicitud</label>
                            <Calendar id="fechaSolicitud" :showIcon="true" :showButtonBar="true" v-model="permission.fechaSolicitud" dateFormat="dd/mm/yy" readonly disabled></Calendar>
                        </div>
                        <div class="field col-3">
                            <label for="fechaPermiso">Fecha Permiso</label>
                            <Calendar id="fechaPermiso" :showIcon="true" :minDate="minDate" :showButtonBar="true" v-model="permission.fechaPermiso" dateFormat="dd/mm/yy" required></Calendar>
                        </div>
                        <div class="field col-2">
                            <label for="horaPermiso">Hora Permiso:</label>
                            <InputText id="horaPermiso" v-model="permission.time" type="time" required />
                        </div>
                        <div class="field col-4">
                            <label for="duracionPermiso">Duración Del Permiso(Horas):</label>
                            <InputNumber id="duracionPermiso" v-model="permission.long" showButtons :min="1" mode="decimal" required></InputNumber>
                        </div>
                    </div>

                    <div class="field">
                        <label for="tipoPermiso" class="mb-3">Tipo De Permiso</label>
                        <Dropdown required id="tipoPermiso" v-model="permission.tipoPermiso.id" :options="tiposDePermisos" optionLabel="value" placeholder="Selecciona Tipo De Permiso" optionValue="id"> </Dropdown>
                    </div>

                    <div class="field">
                        <h5>Soportes Presentados</h5>
                        <FileUpload v-model="permission.medias" name="demo[]" @uploader="onUpload" :multiple="true" accept="image/*,.pdf" :maxFileSize="1000000" customUpload chooseLabel="Seleccionar" uploadLabel="Subir" cancelLabel="Cancelar" />
                        <ul>
                            <li v-for="(file, index) in permission.medias" :key="index">
                                <a :href="file.url" target="_blank">{{ file.name }}</a>
                            </li>
                        </ul>
                    </div>

                    <div class="field">
                        <label for="observaciones">Observaciones</label>
                        <Textarea id="observaciones" v-model="permission.observations" required="true" rows="3" cols="20" />
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-replay" class="p-button-text" @click="hideDialog" />
                        <Button v-if="usuario.department[0].pivot.role !== 'staff'" label="rechazar" icon="pi pi-thumbs-down" class="p-button-text" @click="handleStatusChange('rejected')" />
                        <Button v-if="usuario.department[0].pivot.role !== 'staff'" label="aceptar" icon="pi pi-thumbs-up" class="p-button-text" @click="handleStatusChange('approve')" />
                        <Button v-if="permission.boton == 'editar'" label="editar" icon="pi pi-pencil" class="p-button-text" @click="savePermission" />
                        <Button v-if="permission.boton == 'nuevo'" label="Solicitar" icon="pi pi-arrow-right" class="p-button-text" @click="savePermission" />
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

<style scoped lang="scss">
.file-upload-button::before {
    content: 'Seleccionar'; /* Cambia el texto aquí */
}
</style>
