<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from '@/store'; // Importa tu store Pinia

const email = ref('');
const password = ref('');

const router = useRouter();
const store = useStore(); // Accede al store Pinia

import usersData from '@/json/users.json';

console.log(usersData.users[0])
console.log(store.isAuthenticated)
const handleLogin = () => {
  const loggedInUser = usersData.users.find(user => user.username === email.value && user.password === password.value);
  if (loggedInUser) {
    store.isAuthenticated = true;
    console.log(store.isAuthenticated)
    router.push({ name: 'dashboard' }); // Redirige a la dashboard
    
  } else {
    // Mostrar un mensaje de error o realizar otra acción en caso de inicio de sesión fallido
    console.log("Credenciales incorrectas");
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
                            <Password id="password1" v-model="password" placeholder="Ingresa Contraseña" :toggleMask="true" class="w-full mb-3" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>

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
