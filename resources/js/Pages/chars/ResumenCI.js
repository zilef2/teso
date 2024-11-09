import {ref} from "vue";
import {Chart} from 'chart.js';

export let chartResumenCI2 = ref(null);
export let chartResumenSinEjecucion = ref(null);
let ctx_entidades
let charInstance = []
let tipoVar = 'bar'

export async function ResumenCI(ObjetoJson,conceptos) {

    await new Promise(resolve => setTimeout(resolve, 2));
    ctx_entidades = chartResumenSinEjecucion.value.getContext('2d');
    charInstance = new Chart(ctx_entidades, {
        type: tipoVar,
        data: {
            labels: Object.values(conceptos),

            datasets: [
                {
                    label: '2023',
                    data:
                        ObjetoJson[2023]
                    ,
                    borderWidth: 1,
                    borderColor: 'rgb(0,14,14)',
                    backgroundColor: 'rgb(0, 86, 82)',
                    yAxisID: 'y',
                },
                {
                    label: '2024',
                    data:
                        ObjetoJson[2024]
                    ,
                    borderWidth: 1,
                    borderColor: 'rgb(0,14,14)',
                    backgroundColor: 'rgb(33,124,10)',
                    yAxisID: 'y',
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
                    text: 'Resumen CI - Sin ingreso para ejecucion de convenios'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,  // Eje Y para Dataset 1
                },
            },
            responsive: true,
        }
    });
}
export async function ResumenCI2(ObjetoJson,conceptos) {

    await new Promise(resolve => setTimeout(resolve, 2));
    ctx_entidades = chartResumenCI2.value.getContext('2d');
    charInstance = new Chart(ctx_entidades, {
        data: {
            labels: Object.values(conceptos),

            datasets: [
                {
                    type: tipoVar,
                    label: '2023',
                    data: ObjetoJson[2023],
                    borderWidth: 1,
                    borderColor: 'rgb(0,14,14)',
                    backgroundColor: 'rgb(0, 86, 82)',
                    yAxisID: 'y',
                },
                {
                    type: tipoVar,
                    label: '2024',
                    data: ObjetoJson[2024],
                    borderWidth: 1,
                    borderColor: 'rgb(0,14,14)',
                    backgroundColor: 'rgb(33,124,10)',
                    yAxisID: 'y',
                },
                // {
                //     type: 'line',
                //     label: 'Linea 2023',
                //     data: ObjetoJson[2023],
                //     borderColor: 'rgba(22,106,0,1)',
                //     // yAxisID: 'y1',
                // }, {
                //     type: 'line',
                //     label: 'Linea 2024',
                //     data: ObjetoJson[2024],
                //     borderColor: 'rgba(22,106,0,1)',
                //     // yAxisID: 'y1',
                // }
            ],
        },
        options: {
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Resumen CI'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,  // Eje Y para Dataset 1
                },
            },
            responsive: true,
        }
    });
}
