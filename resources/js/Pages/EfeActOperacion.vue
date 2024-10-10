<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {onMounted, reactive, ref, watchEffect, nextTick} from 'vue';
import '@vuepic/vue-datepicker/dist/main.css'
import {Chart} from 'chart.js';
import {number_format} from "@/global";

const props = defineProps({
    show: Boolean,
    chartCanvasHijaEntrada: Boolean,
    ctxEntrada: Boolean,
})
const emit = defineEmits(["close"]);
const data = reactive({
    params: {
        pregunta: ''
    },
    GrafNumbers: [
        [12903980072, 9468610942],
        [9993901734, 4593745432],
        [0, 1770615434],
        [797038599, 680458218],
        [257653708, 1505885670],
        [784383978, 629782338],
        [1071002053, 288123850],
    ],
    difTotal: [0, 0, 0, 0, 0, 0, 0],
});


onMounted(async () => {
    TrazarLinea()
});
const chartCanvasHijaEntrada = ref(null);

const MontarGrafica = async () => {
    await nextTick();
    const ctx4 = chartCanvasHijaEntrada.value.getContext('2d');
    new Chart(ctx4, {
        type: 'bar',
        data: {
            datasets: [
                {
                    label: 'Total',
                    data: data.GrafNumbers[0],
                    borderWidth: 1,
                    borderColor: 'rgba(22,106,0,1)',
                    backgroundColor: 'rgba(22,106,0,1)',
                    yAxisID: 'y',
                },
                {
                    label: 'Ingreso para ejecución de convenios', //1
                    data: data.GrafNumbers[1],
                    borderWidth: 2,
                    borderColor: 'rgba(246,106,0,1)',
                    backgroundColor: 'rgba(246,106,0,1)',
                    yAxisID: 'y',
                },
                {
                    label: 'Transferencias inversión', //2
                    data: data.GrafNumbers[2],
                    borderWidth: 2,
                    borderColor: 'rgba(246,167,0,1)',
                    backgroundColor: 'rgba(246,167,0,1)',
                },
                {
                    label: 'Transferencias funcionamiento', //3
                    data: data.GrafNumbers[3],
                    borderWidth: 2,
                    borderColor: 'rgb(0,5,5)',
                    backgroundColor: 'rgb(248,251,251)',
                },
                {
                    label: 'Matrículas académicas', //4
                    data: data.GrafNumbers[4],
                    borderWidth: 2,
                    borderColor: 'rgb(248,251,251)',
                    backgroundColor: 'rgb(0,5,5)',
                },
                {
                    label: 'Rendimientos financieros', //5
                    data: data.GrafNumbers[5],
                    borderWidth: 2,
                    borderColor: 'rgba(4,12,0,0.63)',
                    backgroundColor: 'rgb(135,118,4)',
                },
                {
                    label: 'Otras entradas', //6
                    data: data.GrafNumbers[6],
                    borderWidth: 2,
                    borderColor: 'rgb(135,118,4)',
                    backgroundColor: 'rgb(11,48,1)',
                },
                {
                    type: 'line',
                    label: 'Diferencias',
                    data: data.difTotal,
                    borderColor: 'rgba(22,106,0,1)',
                    yAxisID: 'y1',
                    xAxisID: 'x1',
                }
            ],
            labels: ['2023', '2024'],
    // { x: '2024-01-01', y: 10 },
        },
        options: {
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'AGOSTO'
                }
            },
            scales: {
                x1:{
                    type: 'linear',
                    display: true,
                    position: 'bottom',
                    ticks: {
                        color: 'rgba(22,106,0,1)',
                    }
                },
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
                    beginAtZero: false,  // Eje Y para Dataset 2
                    grid: {
                        drawOnChartArea: false,  // Esto evita que el grid de y1 se superponga con el de y
                    },
                }
            },
            responsive: true,
        }
    });
}

watchEffect(() => {
    if (props.show) {
        MontarGrafica()
    }
})

const TrazarLinea = () => {

    data.GrafNumbers.forEach((arrNumber,inde) => data.difTotal[inde] = arrNumber[0] - arrNumber[1])
    console.log("=>(EfeActOperacion.vue:147) data.difTotal", data.difTotal);
}
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" max-width="xl7">
            <div class="px-6 pt-6 pb-12">
                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100">ANÁLISIS ENTRADAS DE EFECTIVO
                    AGOSTO 2023 - 2024
                </h3>
                <div class="mt-6 mb-20">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mb-2 w-full">
                            <canvas ref="chartCanvasHijaEntrada"></canvas>
                        </div>
                    </div>
                </div>
                <div class="my-2 grid grid-cols-2 gap-4">
                    <div @click="TrazarLinea" class=" cursor-pointer ">
                        <div class="mb-2 w-full">
                            Diferencia total: {{ number_format(data.difTotal[0], 0, 1) }}
                        </div>
                        <div class="mb-2 w-full">
                            Diferencia ingresos para ejecucion de convienios: {{ number_format(data.difTotal[1], 0, 1) }}
                        </div>
                        <div class="mb-2 w-full">
                            Diferencia ingresos para ejecucion de convienios: {{ number_format(data.difTotal[2], 0, 1) }}
                        </div>
                    </div>
                </div>

                <p class="my-4 text-xl font-bold">Aspectos significativos</p>
                <p class="my-2 text-lg">1. Caída drástica en el ingreso para la ejecución de convenios</p>
                <p class="my-2 text-lg">2. Introducción de transferencias para inversión</p>
                <p class="my-2 text-lg">3. Caída en otras fuentes de ingresos clave</p>

                <p class="my-4 text-xl font-bold">Conclusiones</p>
                <p class="my-2 text-lg">1. Nuevas transferencias para inversión, lo que podría fortalecer la capacidad
                    de crecimiento en el largo plazo.</p>
                <p class="my-2 text-lg">2. Es necesario profundizar en la estrategia de convenios para revertir las
                    caídas en los ingresos por ejecución de convenios y buscar alternativas para estabilizar las
                    entradas totales.</p>


                <div class="flex justify-end">
                    <SecondaryButton @click="emit('close')"> {{
                            lang().button.close
                        }}
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
