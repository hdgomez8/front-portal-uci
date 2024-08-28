<script setup>
// Importaciones
import { ref, onMounted, onBeforeMount } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from 'primevue/api';

// Variables
const toast = useToast();
const employees = ref(null);
const employeeDialog = ref(false);
const editEmployeeDialog = ref(false);
const selectedEmployee = ref(null);
const newEmployee = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    dni: '',
    identity_type_id: '',
    first_name: '',
    last_name: '',
    phone: '',
    title: '',
    department_id: '',
    role: ''
});
const editEmployee = ref({
    id: null,
    name: '',
    email: '',
    dni: '',
    identity_type_id: '',
    first_name: '',
    last_name: '',
    phone: '',
    title: '',
    department_id: '',
    role: ''
});
const filters = ref({});
const isLoading = ref(false);

// Ciclos de Vida
onBeforeMount(() => {
    initFilters();
});

onMounted(async () => {
    try {
        const responseEmployees = await axios.get('/employees');
        employees.value = responseEmployees.data.data;
    } catch (error) {
        console.error('Error al obtener datos:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al obtener datos', life: 3000 });
    }
});

// Funciones
const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
};

const openNewEmployeeDialog = () => {
    newEmployee.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        dni: '',
        identity_type_id: '',
        first_name: '',
        last_name: '',
        phone: '',
        title: '',
        department_id: '',
        role: ''
    };
    employeeDialog.value = true;
};

const hideEmployeeDialog = () => {
    employeeDialog.value = false;
    editEmployeeDialog.value = false;
};

const saveEmployee = async () => {
    try {
        await axios.post('/register', newEmployee.value);
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Empleado Creado', life: 3000 });
        employeeDialog.value = false;
        window.location.reload();
    } catch (error) {
        console.error('Error al crear empleado:', error);
        console.log(error.response.data); // Imprime la respuesta del servidor
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al crear empleado', life: 3000 });
    }
};

const editEmployeeInfo = async () => {
    try {
        await axios.put(`/employees/${editEmployee.value.id}`, editEmployee.value);
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Empleado Actualizado', life: 3000 });
        editEmployeeDialog.value = false;
        window.location.reload();
    } catch (error) {
        console.error('Error al actualizar empleado:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al actualizar empleado', life: 3000 });
    }
};

const openEditEmployeeDialog = (employee) => {
    editEmployee.value = { ...employee };
    editEmployeeDialog.value = true;
};

const formatDate = (dateString) => {
    const [year, month, day] = dateString.split('-').map((num) => parseInt(num, 10));
    const dateObject = new Date(year, month - 1, day);
    const formattedDay = dateObject.getDate().toString().padStart(2, '0');
    const formattedMonth = (dateObject.getMonth() + 1).toString().padStart(2, '0');
    const formattedYear = dateObject.getFullYear();
    return `${formattedDay}/${formattedMonth}/${formattedYear}`;
};

const formatHour = (timeString) => {
    const timeObject = new Date(timeString);
    const hours = timeObject.getUTCHours();
    const minutes = timeObject.getUTCMinutes();
    const formattedHours = hours.toString().padStart(2, '0');
    const formattedMinutes = minutes.toString().padStart(2, '0');
    return `${formattedHours}:${formattedMinutes}`;
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
                            <Button label="Crear Empleado" icon="pi pi-user-plus" class="p-button-success" @click="openNewEmployeeDialog" />
                        </div>
                    </template>
                </Toolbar>

                <!-- Tabla de Empleados -->
                <DataTable
                    ref="employeeTable"
                    :value="employees"
                    :paginator="true"
                    :rows="10"
                    :filters="filters"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} empleados"
                    responsiveLayout="scroll"
                >
                    <template #header>
                        <div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
                            <h5 class="m-0">Empleados</h5>
                        </div>
                    </template>
                    <Column field="name" header="Nombre" :sortable="true" headerStyle="width:25%; min-width:15rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Nombre</span>
                            {{ slotProps.data.first_name }} {{ slotProps.data.last_name }}
                        </template>
                    </Column>
                    <Column field="title" header="Título" :sortable="true" headerStyle="width:25%; min-width:15rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Título</span>
                            {{ slotProps.data.title }}
                        </template>
                    </Column>
                    <Column field="department" header="Departamento" :sortable="true" headerStyle="width:25%; min-width:15rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Departamento</span>
                            {{ slotProps.data.department }}
                        </template>
                    </Column>
                    <Column headerStyle="width:25%; min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="p-button-rounded p-button-warning mr-2" @click="openEditEmployeeDialog(slotProps.data)" />
                            <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="deleteEmployee(slotProps.data.id)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Diálogo para Crear Empleado -->
                <Dialog header="Crear Empleado" v-model:visible="employeeDialog" modal :style="{ width: '800px' }" @hide="hideEmployeeDialog">
                    <div class="formgrid grid">
                        <div class="field col-3">
                            <label for="name">Nombre</label>
                            <InputText id="name" v-model="newEmployee.name" />
                        </div>
                        <div class="field col-3">
                            <label for="email">Correo Electrónico</label>
                            <InputText id="email" v-model="newEmployee.email" />
                        </div>
                        <div class="field col-3">
                            <label for="password">Contraseña</label>
                            <InputText id="password" v-model="newEmployee.password" type="password" />
                        </div>
                        <div class="field col-3">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <InputText id="password_confirmation" v-model="newEmployee.password_confirmation" type="password" />
                        </div>
                        <div class="field col-3">
                            <label for="dni">DNI</label>
                            <InputText id="dni" v-model="newEmployee.dni" />
                        </div>
                        <div class="field col-3">
                            <label for="identity_type_id">Tipo de Identidad</label>
                            <InputText id="identity_type_id" v-model="newEmployee.identity_type_id" />
                        </div>
                        <div class="field col-3">
                            <label for="first_name">Primer Nombre</label>
                            <InputText id="first_name" v-model="newEmployee.first_name" />
                        </div>
                        <div class="field col-3">
                            <label for="last_name">Apellido</label>
                            <InputText id="last_name" v-model="newEmployee.last_name" />
                        </div>
                        <div class="field col-3">
                            <label for="phone">Teléfono</label>
                            <InputText id="phone" v-model="newEmployee.phone" />
                        </div>
                        <div class="field col-3">
                            <label for="title">Título</label>
                            <InputText id="title" v-model="newEmployee.title" />
                        </div>
                        <div class="field col-3">
                            <label for="department_id">ID del Departamento</label>
                            <InputText id="department_id" v-model="newEmployee.department_id" />
                        </div>
                        <div class="field col-3">
                            <label for="role">Rol</label>
                            <InputText id="role" v-model="newEmployee.role" />
                        </div>
                    </div>
                    <div class="flex justify-content-end mt-3">
                        <Button label="Guardar" icon="pi pi-check" class="p-button-success mr-2" @click="saveEmployee" />
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="hideEmployeeDialog" />
                    </div>
                </Dialog>

                <!-- Diálogo para Editar Empleado -->
                <Dialog header="Editar Empleado" v-model:visible="editEmployeeDialog" modal :style="{ width: '800px' }" @hide="hideEmployeeDialog">
                    <div class="formgrid grid">
                        <div class="field col-3">
                            <label for="editName">Nombre</label>
                            <InputText id="editName" v-model="editEmployee.name" />
                        </div>
                        <div class="field col-3">
                            <label for="editEmail">Correo Electrónico</label>
                            <InputText id="editEmail" v-model="editEmployee.email" />
                        </div>
                        <div class="field col-3">
                            <label for="editDni">DNI</label>
                            <InputText id="editDni" v-model="editEmployee.dni" />
                        </div>
                        <div class="field col-3">
                            <label for="editIdentityType">Tipo de Identidad</label>
                            <InputText id="editIdentityType" v-model="editEmployee.identity_type_id" />
                        </div>
                        <div class="field col-3">
                            <label for="editFirstName">Primer Nombre</label>
                            <InputText id="editFirstName" v-model="editEmployee.first_name" />
                        </div>
                        <div class="field col-3">
                            <label for="editLastName">Apellido</label>
                            <InputText id="editLastName" v-model="editEmployee.last_name" />
                        </div>
                        <div class="field col-3">
                            <label for="editPhone">Teléfono</label>
                            <InputText id="editPhone" v-model="editEmployee.phone" />
                        </div>
                        <div class="field col-3">
                            <label for="editTitle">Título</label>
                            <InputText id="editTitle" v-model="editEmployee.title" />
                        </div>
                        <div class="field col-3">
                            <label for="editDepartmentId">ID del Departamento</label>
                            <InputText id="editDepartmentId" v-model="editEmployee.department_id" />
                        </div>
                        <div class="field col-3">
                            <label for="editRole">Rol</label>
                            <InputText id="editRole" v-model="editEmployee.role" />
                        </div>
                    </div>
                    <div class="flex justify-content-end mt-3">
                        <Button label="Guardar" icon="pi pi-check" class="p-button-success mr-2" @click="editEmployeeInfo" />
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="hideEmployeeDialog" />
                    </div>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Estilos personalizados para mejorar la presentación */
.field {
    margin-bottom: 1rem;
}

.formgrid .field {
    flex: 0 0 25%; /* Ocupa 25% del ancho, equivalente a 4 campos por línea */
}

@media screen and (max-width: 960px) {
    .formgrid .field {
        flex: 0 0 50%; /* Ocupa 50% del ancho en pantallas más pequeñas, equivalente a 2 campos por línea */
    }
}

@media screen and (min-width: 960px) {
    .formgrid .field {
        flex: 0 0 25%; /* Ocupa 25% del ancho en pantallas más grandes, equivalente a 4 campos por línea */
    }
}
</style>

