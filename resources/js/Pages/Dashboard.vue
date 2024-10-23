<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon} from '@heroicons/vue/24/solid';
import {Head} from '@inertiajs/vue3';
import Chart from 'chart.js/auto';
import {ref, onMounted, watchEffect, reactive} from 'vue';
import EfeActOperacion from "@/Pages/EfeActOperacion.vue";
import {buildCharEnti, chart10} from "@/Pages/chars/numeroEntidades.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// <!--<editor-fold desc=" lo demas">-->
// roles: Number,
const props = defineProps({
    users: Number,
    roles: Number,
    rolesNameds: Object,
    numberPermissions: Number,
    numeroEntidades: Object,
})

const data = reactive({
    IngresosOpen: false,
    editOpen: false,
    deleteOpen: false,
    ArchivoNombre: '',
    tipoVar: ['bar', 'bar', 'bar'],
})
// <!--</editor-fold>-->


const chartCanvas1 = ref(null);
const chartCanvas2 = ref(null);
const chartCanvasEfec = ref(null);
const char10 = chart10;
let ctx_a, ctx_b, ctx_c
let charInstance = []
onMounted(async () => {
    await buildCharEnti(props.numeroEntidades)

    ctx_b = chartCanvas1.value.getContext('2d');
    buildChar2(1)//la2

    ctx_c = chartCanvas2.value.getContext('2d'); // la3
    new Chart(ctx_c, {
        data: {
            datasets: [{
                type: data.tipoVar[2],
                label: 'Saldo',
                data: [
                    46460515985,
                    17326519250,
                    14777044642,
                    11770888208,
                    523875033,
                ],
                backgroundColor: 'rgba(246, 167, 0,0.4)',
                yAxisID: 'y',

            }, {
                type: 'line',
                label: 'Participacion',
                data: [51.13, 19.07, 16.26, 12.96, 0.58],
                borderColor: 'rgba(22,106,0,1)',
                yAxisID: 'y1',
            }],
            labels: [
                'Recursos Propios',
                'Inversión',
                'Convenios y Contratos Interadministrativos*',
                'Nación',
                'Fundación Secretos Para Contar'
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
                    text: 'ORIGEN DE LOS RECURSOS'
                }
            }
        }
    });


    ctx_a = chartCanvasEfec.value.getContext('2d');
    buildChar1(0)
});
const buildChar1 = (numeracio) => charInstance[numeracio] = new Chart(ctx_a, {
    type: data.tipoVar[numeracio],
    data: {
        labels: ['2023', '2024'],

        datasets: [
            {
                label: 'Saldo Inicial de la Vigencia',
                data: [93358379599, 92351501438],
                borderWidth: 1,
                borderColor: 'rgb(0,14,14)',
                backgroundColor: 'rgb(0, 86, 82)',
                yAxisID: 'y',
            },
            {
                label: 'Entradas',
                data: [12903980072, 9468610942],
                borderWidth: 0,
                borderColor: 'rgb(0,5,5)',
                backgroundColor: 'rgb(0,0,122)',
                yAxisID: 'y',
            },
            {
                label: 'Salidas',
                data: [16180065448, 10961269262],
                borderWidth: 0,
                borderColor: 'rgb(0, 86, 82)',
                backgroundColor: 'rgb(246, 167, 0)',
            },
            {
                label: 'Disminucion Del Efectivo Agosto',
                data: [3276085376, 1492658320],
                borderWidth: 0,
                borderColor: 'rgba(4,12,0,0.63)',
                backgroundColor: 'rgb(135,118,4)',
            },
            {
                label: 'Saldo de efectivo al fin de la vigencia',
                data: [90082294223, 90858843118],
                borderWidth: 2,
                borderColor: 'rgba(18,0,2,0.98)',
                backgroundColor: 'rgb(25,182,2)',
            },
        ],
    },
    options: {
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Efectivo en actividades de operación'
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,  // Eje Y para Dataset 1
            },
            // y1: {
            //     type: 'linear',
            //     display: true,
            //     position: 'right',
            //     beginAtZero: true,  // Eje Y para Dataset 2
            //     grid: {
            //         drawOnChartArea: false,  // Esto evita que el grid de y1 se superponga con el de y
            //     },
            // }
        },
        responsive: true,
    }
});

const buildChar2 = (numeracio) => charInstance[numeracio] = new Chart(ctx_b, {
    type: data.tipoVar[numeracio],
    data: {
        labels: ['ITAU BANCO CORPBANCA S.A', 'BANCOLOMBIA', 'BANCO POPULAR', 'DAVIVIENDA', 'BANCO DE BOGOTÁ', 'BBVA'],
        datasets: [
            {
                label: 'Saldo',
                data: [22312133127, 28047972772, 29806392530, 7644769753, 1886605760, 384420281],
                borderWidth: 2,
                borderColor: 'rgb(0, 86, 82)',
                backgroundColor: 'rgb(0, 86, 82,0.8)',
                yAxisID: 'y',
            },
            {
                label: 'Participación % saldo total',
                data: [24.77, 31.14, 33.09, 8.49, 2.09, 0.43],
                borderWidth: 0,
                borderColor: 'rgba(0,159,152,0.83)',
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


const changeChar = (numeracion) => {
    if (charInstance[numeracion])
        charInstance[numeracion].destroy()
    data.tipoVar[numeracion] = data.tipoVar[numeracion] === 'doughnut' ? 'bar' : 'doughnut';
    if (numeracion === 1)
        buildChar2(numeracion)
    if (numeracion === 0)
        buildChar1(numeracion)
}
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <Breadcrumb :title="'Resumen'" :breadcrumbs="[]" />
        <EfeActOperacion :show="data.IngresosOpen" @close="data.IngresosOpen = false"
                         :Chart="Chart"
                         :chartCanvasHijaEntrada="chartCanvasHijaEntrada"
                         :ctxEntrada="ctxEntrada"
        />
        <PrimaryButton class="rounded-lg mx-2" @click="changeChar(0)">
            Dona 1
        </PrimaryButton>
        <PrimaryButton class="rounded-lg mx-2" @click="changeChar(1)">
            Dona 2
        </PrimaryButton>
        <PrimaryButton class="rounded-lg mx-2" @click="data.IngresosOpen = true">
            Entradas
        </PrimaryButton>

        <div class="grid grid-cols-1 4xl:grid-cols-2">
            <div class="mb-20 w-full md:w-5/6">
                <canvas ref="char10"></canvas>
            </div>
            <div class="my-20 w-full md:w-5/6">
                <canvas ref="chartCanvasEfec"></canvas>
            </div>
            <div class="my-20 w-full md:w-5/6">
                <canvas ref="chartCanvas1"></canvas>
            </div>
            <div class="my-20 w-full md:w-5/6">
                <canvas ref="chartCanvas2"></canvas>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
