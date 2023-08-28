import { defineStore } from 'pinia';

export const useStore = defineStore({
  id: 'app', // Identificador único para tu store
  state: () => ({
    isAuthenticated: false, // Estado de autenticación
    // Otros estados que necesites
  }),
  // Definición de acciones o mutaciones aquí si es necesario
});
