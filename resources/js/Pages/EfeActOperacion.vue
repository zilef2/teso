<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {onMounted, reactive, ref, watchEffect, nextTick} from 'vue';
import '@vuepic/vue-datepicker/dist/main.css'
import {Chart} from 'chart.js';

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
});


onMounted(async () => {});
const chartCanvasHijaEntrada = ref(null);

const MontarGrafica = async () => {
    await nextTick();
    const ctx4 = chartCanvasHijaEntrada.value.getContext('2d');
    new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: ['2023', '2024'],

            datasets: [
                {
                    label: 'Total',
                    data: [12903980072, 9468610942],
                    borderWidth: 1,
                    borderColor: 'rgb(243,244,244)',
                    backgroundColor: 'rgba(11,241,178,0.98)',
                    yAxisID: 'y',
                },
                {
                    label: 'Ingreso para ejecución de convenios', //1
                    data: [9993901734, 4593745432],
                    borderWidth: 2,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    yAxisID: 'y',
                },
                {
                    label: 'Transferencias inversión', //2
                    data: [0, 1770615434],
                    borderWidth: 2,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                },
                {
                    label: 'Transferencias funcionamiento', //3
                    data: [797038599, 680458218],
                    borderWidth: 2,
                    borderColor: 'rgba(91,245,11,0.63)',
                    backgroundColor: 'rgba(95,244,52,0.73)',
                },
                {
                    label: 'Matrículas académicas', //4
                    data: [257653708, 1505885670],
                    borderWidth: 2,
                    borderColor: 'rgba(255,0,46,0.78)',
                    backgroundColor: 'rgba(255, 162, 235, 0.2)',
                },
                {
                    label: 'Rendimientos financieros', //5
                    data: [784383978, 629782338],
                    borderWidth: 2,
                    borderColor: 'rgba(255,0,46,0.78)',
                    backgroundColor: 'rgba(11,88,228,0.51)',
                },
                {
                    label: 'Otras entradas', //6
                    data: [1071002053, 288123850],
                    borderWidth: 2,
                    borderColor: 'rgba(3,142,205,0.98)',
                    backgroundColor: 'rgba(255, 162, 235, 0.2)',
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
                    text: 'AGOSTO'
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
}

watchEffect(() => {
    if (props.show) {
        MontarGrafica()
    }
})
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

                <p class="my-2 text-lg">Aspectos significativos</p>
                <p class="my-2 text-lg">1. Reducción significativa en el total de entradas de efectivo</p>
                <p class="my-2 text-lg">2. Caída drástica en el ingreso para la ejecución de convenios</p>
                <p class="my-2 text-lg">3. Incremento importante en las matrículas académicas</p>
                <p class="my-2 text-lg">4. Introducción de transferencias para inversión</p>
                <p class="my-2 text-lg">5. Caída en otras fuentes de ingresos clave</p>

                <p class="my-4 text-xl font-bold">Conclusiones</p>
                <p class="my-2 text-lg">Disminución general en los ingresos, impulsada principalmente por la caída en convenios y otras entradas no recurrentes.</p>
                <p class="my-2 text-lg">Crecimiento notable en matrículas académicas, lo que puede ser un signo positivo de expansión en ese sector.</p>
                <p class="my-2 text-lg">Nuevas transferencias para inversión, lo que podría fortalecer la capacidad de crecimiento en el largo plazo.</p>
                <p class="my-2 text-lg">Es necesario profundizar en la estrategia de convenios para revertir las caídas en los ingresos por ejecución de convenios y buscar alternativas para estabilizar las entradas totales.</p>


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
