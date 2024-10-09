<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon} from '@heroicons/vue/24/solid';
import {Head, Link} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Chart from 'chart.js/auto';
import {ref, onMounted, watchEffect} from 'vue';

// <!--<editor-fold desc=" lo demas">-->
// roles: Number,
const props = defineProps({
    users: Number,
    roles: Number,
    rolesNameds: Object,
    numberPermissions: Number,
})

const dashLinks = [
    // 'Informes',
    // 'roles',
];
const colores = [
    'bg-gray-400',
    // 'bg-gray-500',
    // 'bg-gray-600',
];
const descripcion = [
    'Descripcion',
    // 'Descripcion',
    // 'descripcion',
];
const laImg = [
    'KeyIcon',
    // 'KeyIcon',
    // 'KeyIcon',
    // 'KeyIcon',
];
const downloadAnexos = () => {
    window.open('downloadAnexos', '_blank')
}
// <!--</editor-fold>-->


const chartCanvas = ref(null);
const chartCanvas2 = ref(null);

onMounted(() => {

    const ctx = chartCanvas.value.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['ITAU BANCO CORPBANCA S.A', 'BANCOLOMBIA', 'BANCO POPULAR', 'DAVIVIENDA', 'BANCO DE BOGOTÁ', 'BBVA'],
            datasets: [
                {
                    label: 'Saldo',
                    data: [22312133127, 28047972772, 29806392530, 7644769753, 1886605760, 384420281],
                    borderWidth: 2,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    yAxisID: 'y',
                },
                {
                    label: 'Participación % saldo total',
                    data: [24.77, 31.14, 33.09, 8.49, 2.09, 0.43],
                    borderWidth: 2,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    yAxisID: 'y1',
                }
            ],
        },
        options: {
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,  // Eje Y para Dataset 1
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,  // Eje Y para Dataset 2
                    grid: {
                        drawOnChartArea: false,  // Esto evita que el grid de y1 se superponga con el de y
                    },
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Saldo disponible Bancos'
                }
            }
        }
    });


    const ctx2 = chartCanvas2.value.getContext('2d');
    const mixedChart = new Chart(ctx2, {
        data: {
            datasets: [{
                type: 'bar',
                label: 'Saldo',
                data: [
                    46460515985,
                    17326519250,
                    14777044642,
                    11770888208,
                    523875033,
                ]
            }, {
                type: 'line',
                label: 'Participacion',
                data: [51.13, 19.07, 16.26, 12.96,0.58],
            }],
            labels: [
                'Recursos Propios',
                'Inversión',
                'Convenios y Contratos Interadministrativos*', 
                'Nación',
                'Fundación Secretos Para Contar'
            ]
        },
        options: {
            scales: {
                 y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,  // Eje Y para Dataset 1
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,  // Eje Y para Dataset 2
                    grid: {
                        drawOnChartArea: false,  // Esto evita que el grid de y1 se superponga con el de y
                    },
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'ORIGEN DE LOS RECURSOS'
                }
            }
        }
    });


});
</script>

<template>

    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <Breadcrumb :title="'Resumen'" :breadcrumbs="[]"/>
        <div class="my-20 h-[420px] xl:h-[720px] 4xl:h-[1040px]">
            <canvas ref="chartCanvas"></canvas>
        </div>
        <div class="my-20 h-[420px] xl:h-[720px] 4xl:h-[1040px]">
            <canvas ref="chartCanvas2"></canvas>
        </div>
    </AuthenticatedLayout>
</template>
