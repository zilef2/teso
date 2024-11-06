<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import Chart from 'chart.js/auto';
import {ref, onMounted, watchEffect, reactive} from 'vue';
import EfeActOperacion from "@/Pages/EfeActOperacion.vue";
import {buildCharEnti, chart10} from "@/Pages/chars/numeroEntidades.js";
import {buildMini, charConteomini} from "@/Pages/chars/numeroEntidadesmini.js";
import {buildCharCPnull, verificadorCP} from "@/Pages/chars/ComparacionContrapartidaNull.js";
import {ResumenCI,ResumenCI2, chart12,chart13} from "@/Pages/chars/ResumenCI.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {Link} from '@inertiajs/vue3';

// <!--<editor-fold desc=" lo demas">-->
// roles: Number,
const props = defineProps({
    users: Number,
    roles: Number,
    rolesNameds: Object,
    numberPermissions: Number,
    ConteoEntidades: Object,
    ComparacionCP: Object,

    //ResumenCI
    ResumenCI: Object,
    conceptos: Object,
    ResumenCI2: Object,
    conceptos2: Object,
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
const charConteomin = charConteomini;
let ctx_a, ctx_b, ctx_c
let charInstance = []
onMounted(async () => {
    await buildCharEnti(props.ConteoEntidades[0])
    await buildMini(props.ConteoEntidades[1])
    await buildCharCPnull(props.ComparacionCP)
    await ResumenCI(props.ResumenCI,props.conceptos)
    await ResumenCI2(props.ResumenCI2,props.conceptos2)

    // ctx_b = chartCanvas1.value.getContext('2d');
    // buildChar2(1)//la2
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
});
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
<!--        <PrimaryButton class="rounded-lg mx-2" @click="changeChar(0)">-->
<!--            Dona 1-->
<!--        </PrimaryButton>-->
        <Link :href="route('jobs')">
            <PrimaryButton class="rounded-lg mx-2">
                Ver Cruces
            </PrimaryButton>
        </Link>
        <PrimaryButton class="rounded-lg mx-2" @click="data.IngresosOpen = true">
            Entradas
        </PrimaryButton>

        <div class="grid grid-cols-1 4xl:grid-cols-2">
            <div class="mb-20 w-4/6 2xl:w-5/6"><canvas ref="chart12"></canvas></div>
            <div class="mb-20 w-4/6 2xl:w-5/6"><canvas ref="chart13"></canvas></div>
        </div>
        <div class="grid grid-cols-1 3xl:grid-cols-2">
            <div class="mb-20 w-full md:w-5/6 3xl:w-full"><canvas ref="charConteomin"></canvas></div>
            <div class="mb-20 w-full md:w-5/6 3xl:w-full"><canvas ref="char10"></canvas></div>
<!--            <div class="mb-20 w-full md:w-4/6 3xl:w-full"><canvas ref="chartCanvasEfec"></canvas></div>-->
<!--            <div class="my-20 w-full md:w-5/6"><canvas ref="chartCanvas1"></canvas></div>-->
            <div class="my-20 w-full md:w-4/6 3xl:w-full"><canvas ref="chartCanvas2"></canvas></div>
            <div class="my-20 w-1/2 "><canvas ref="verificadorCP"></canvas></div>
        </div>
    </AuthenticatedLayout>
</template>
