<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import ProductService from '@/service/ProductService';
import { useLayout } from '@/layout/composables/layout';

const { isDarkTheme } = useLayout();

const products = ref(null);
const lineData = reactive({
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
        {
            label: 'First Dataset',
            data: [65, 59, 80, 81, 56, 55, 40],
            fill: false,
            backgroundColor: '#2f4860',
            borderColor: '#2f4860',
            tension: 0.4
        },
        {
            label: 'Second Dataset',
            data: [28, 48, 40, 19, 86, 27, 90],
            fill: false,
            backgroundColor: '#00bb7e',
            borderColor: '#00bb7e',
            tension: 0.4
        }
    ]
});
const items = ref([
    { label: 'Add New', icon: 'pi pi-fw pi-plus' },
    { label: 'Remove', icon: 'pi pi-fw pi-minus' }
]);
const lineOptions = ref(null);
const productService = new ProductService();

onMounted(() => {
    productService.getProductsSmall().then((data) => (products.value = data));
});

const formatCurrency = (value) => {
    return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
};
const applyLightTheme = () => {
    lineOptions.value = {
        plugins: {
            legend: {
                labels: {
                    color: '#495057'
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: '#495057'
                },
                grid: {
                    color: '#ebedef'
                }
            },
            y: {
                ticks: {
                    color: '#495057'
                },
                grid: {
                    color: '#ebedef'
                }
            }
        }
    };
};

const applyDarkTheme = () => {
    lineOptions.value = {
        plugins: {
            legend: {
                labels: {
                    color: '#ebedef'
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: '#ebedef'
                },
                grid: {
                    color: 'rgba(160, 167, 181, .3)'
                }
            },
            y: {
                ticks: {
                    color: '#ebedef'
                },
                grid: {
                    color: 'rgba(160, 167, 181, .3)'
                }
            }
        }
    };
};

watch(
    isDarkTheme,
    (val) => {
        if (val) {
            applyDarkTheme();
        } else {
            applyLightTheme();
        }
    },
    { immediate: true }
);
</script>

<template>
    <div class="card">
        <h5>Permisos</h5>
        <div class="grid">
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Abiertos</span>
                            <div class="text-900 font-medium text-xl">5</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-orange-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-send text-orange-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Aprobados</span>
                            <div class="text-900 font-medium text-xl">15</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-cyan-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-thumbs-up text-cyan-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Rechazados</span>
                            <div class="text-900 font-medium text-xl">20</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-purple-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-thumbs-down text-purple-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Total</span>
                            <div class="text-900 font-medium text-xl">20</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-red-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-align-justify text-red-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card" style="display: none;">
        <h5>Vacaciones</h5>
        <div class="grid">
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Pendientes</span>
                            <div class="text-900 font-medium text-xl">5</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-orange-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-map-marker text-orange-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">%52+ </span>
                    <span class="text-500">since last week</span>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Aceptados</span>
                            <div class="text-900 font-medium text-xl">15</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-cyan-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-inbox text-cyan-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">520 </span>
                    <span class="text-500">newly registered</span>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Total</span>
                            <div class="text-900 font-medium text-xl">20</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-purple-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-comment text-purple-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">85 </span>
                    <span class="text-500">responded</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card" style="display: none;">
        <h5>Turnos</h5>
        <div class="grid">
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Pendientes</span>
                            <div class="text-900 font-medium text-xl">5</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-orange-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-map-marker text-orange-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">%52+ </span>
                    <span class="text-500">since last week</span>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Aceptados</span>
                            <div class="text-900 font-medium text-xl">15</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-cyan-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-inbox text-cyan-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">520 </span>
                    <span class="text-500">newly registered</span>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Total</span>
                            <div class="text-900 font-medium text-xl">20</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-purple-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-comment text-purple-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">85 </span>
                    <span class="text-500">responded</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card" style="display: none;">
        <h5>Cesantias</h5>
        <div class="grid">
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Pendientes</span>
                            <div class="text-900 font-medium text-xl">5</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-orange-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-map-marker text-orange-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">%52+ </span>
                    <span class="text-500">since last week</span>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Aceptados</span>
                            <div class="text-900 font-medium text-xl">15</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-cyan-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-inbox text-cyan-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">520 </span>
                    <span class="text-500">newly registered</span>
                </div>
            </div>
            <div class="col-12 lg:col-6 xl:col-3">
                <div class="card mb-0">
                    <div class="flex justify-content-between mb-3">
                        <div>
                            <span class="block text-500 font-medium mb-3">Total</span>
                            <div class="text-900 font-medium text-xl">20</div>
                        </div>
                        <div class="flex align-items-center justify-content-center bg-purple-100 border-round" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-comment text-purple-500 text-xl"></i>
                        </div>
                    </div>
                    <span class="text-green-500 font-medium">85 </span>
                    <span class="text-500">responded</span>
                </div>
            </div>
        </div>
    </div>
</template>
