    <script setup>
    import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
    import { useLayout } from '@/layout/composables/layout';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { useConfirm } from 'primevue/useconfirm';

    const { layoutConfig, onMenuToggle } = useLayout();
    const popup = ref(null);
    const outsideClickListener = ref(null);
    const topbarMenuActive = ref(false);
    const router = useRouter();
    const toast = useToast();
    const confirmPopup = useConfirm();

    onMounted(() => {
        bindOutsideClickListener();
    });

    onBeforeUnmount(() => {
        unbindOutsideClickListener();
    });

    const logoUrl = computed(() => {
        return `layout/images/${layoutConfig.darkTheme.value ? 'uci-logo' : 'uci-logo'}.jpeg`;
    });

    const onTopBarMenuButton = () => {
        topbarMenuActive.value = !topbarMenuActive.value;
    };
    const onSettingsClick = () => {
        topbarMenuActive.value = false;
        router.push('/documentation');
    };
    const topbarMenuClasses = computed(() => {
        return {
            'layout-topbar-menu-mobile-active': topbarMenuActive.value
        };
    });

    const bindOutsideClickListener = () => {
        if (!outsideClickListener.value) {
            outsideClickListener.value = (event) => {
                if (isOutsideClicked(event)) {
                    topbarMenuActive.value = false;
                }
            };
            document.addEventListener('click', outsideClickListener.value);
        }
    };
    const unbindOutsideClickListener = () => {
        if (outsideClickListener.value) {
            document.removeEventListener('click', outsideClickListener);
            outsideClickListener.value = null;
        }
    };
    const isOutsideClicked = (event) => {
        if (!topbarMenuActive.value) return;

        const sidebarEl = document.querySelector('.layout-topbar-menu');
        const topbarEl = document.querySelector('.layout-topbar-menu-button');

        return !(sidebarEl.isSameNode(event.target) || sidebarEl.contains(event.target) || topbarEl.isSameNode(event.target) || topbarEl.contains(event.target));
    };

    const logout = () => {
        // Elimina los datos del almacenamiento local
        localStorage.removeItem('user');
        localStorage.removeItem('token');

        router.push({ name: 'login' });

        // Muestra un mensaje de confirmación
        toast.add({ severity: 'success', summary: 'Sesión cerrada', detail: 'Se ha cerrado la sesión correctamente', life: 3000 });
    };

    const confirm = (event) => {
        confirmPopup.require({
            target: event.target,
            message: 'Cerrar la sesión',
            icon: 'pi pi-sign-out',
            accept: logout,
            reject: () => {}
        });
    };

    </script>

    <template>
        <div class="layout-topbar">
            <router-link to="/" class="layout-topbar-logo">
                <img :src="logoUrl" alt="logo" />
            </router-link>

            <button class="p-link layout-menu-button layout-topbar-button" @click="onMenuToggle()">
                <i class="pi pi-bars"></i>
            </button>

            <button class="p-link layout-topbar-menu-button layout-topbar-button" @click="onTopBarMenuButton()">
                <i class="pi pi-ellipsis-v"></i>
            </button>

            <div class="layout-topbar-menu" :class="topbarMenuClasses">
                <ConfirmPopup></ConfirmPopup>
                <Toast />
                <Button ref="popup" @click="confirm($event)" icon="pi pi-user" class="mr-2"></Button>
            </div>
        </div>
    </template>

    <style lang="scss" scoped></style>
