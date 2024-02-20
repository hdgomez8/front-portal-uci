<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from '@/store'; // Importa tu store Pinia
import axios from 'axios';

const email = ref('');
const password = ref('');

const router = useRouter();
const store = useStore(); // Accede al store Pinia

const handleLogin = async () => {
    try {
        const response = await axios.post('/login', {
            email: email.value,
            password: password.value
        });

        if (response.data.token) {
            // Guarda el token en localStorage
            localStorage.setItem('token', response.data.token);
            // Guarda el user en localStorage
            const user = response.data.user;
            const userJSON = JSON.stringify(user);
            localStorage.setItem('user', userJSON);
            // localStorage.setItem('user', response.data.user.id);
            // Configura el encabezado de autorización para futuras solicitudes
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
            // Guarda el estado de autenticación en el store
            store.isAuthenticated = true;

            // Redirige a la dashboard
            router.push({ name: 'dashboard' });
        } else {
            console.log('Credenciales incorrectas');
        }
    } catch (error) {
        console.error('Error al iniciar sesión:', error);
    }
};
</script>

<template>
    <div class="login-container surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                    <div class="text-center mb-5">
                        <img src="/demo/images/login/uci-logo.jpeg" alt="Image" height="50" class="mb-3" />
                        <div class="text-900 text-3xl font-medium mb-3">Portal UCI</div>
                    </div>

                    <div>
                        <label for="email1" class="block text-900 text-xl font-medium mb-2">Correo Electrónico</label>
                        <InputText id="email1" type="text" placeholder="Dirección Correo Electrónico" class="w-full md:w-30rem mb-5" style="padding: 1rem" v-model="email" />

                        <label for="password1" class="block text-900 font-medium text-xl mb-2">Contraseña</label>
                        <Password id="password1" v-model="password" placeholder="Ingresa Contraseña" :appendSlot="null" :toggleMask="true" :feedbackMode="null"  class="w-full mb-3" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>

                        <div class="flex align-items-center justify-content-between mb-5 gap-5"></div>
                        <Button label="Iniciar Sesión" class="w-full p-3 text-xl" @click="handleLogin"></Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}

.login-container {
    background-image: url('/demo/images/login/uci-fondo.jpg');
    background-size: cover;
    background-position: center;
    padding: 0; /* Asegura que no haya padding en el contenedor */
}
</style>
