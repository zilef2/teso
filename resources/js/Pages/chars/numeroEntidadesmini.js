import {ref} from "vue";
import {Chart} from 'chart.js';

export let charConteomini = ref(null);
let ctx_entidades
let charInstance = []
let tipoVar = 'bar'

export async function buildMini(numeroEntidades) {

    let Comprobanteaj2023 = numeroEntidades.Comprobanteaj2023
    let Comprobanteaj2024 = numeroEntidades.Comprobanteaj2024
    let Comprobantean2023 = numeroEntidades.Comprobantean2023
    let Comprobantean2024 = numeroEntidades.Comprobantean2024
    let Comprobanteca2023 = numeroEntidades.Comprobanteca2023
    let Comprobanteca2024 = numeroEntidades.Comprobanteca2024

    await new Promise(resolve => setTimeout(resolve, 10));
    ctx_entidades = charConteomini.value.getContext('2d');
    charInstance = new Chart(ctx_entidades, {
        type: tipoVar,
        data: {
            labels: [
                'AJ',
                'AN',
                'CA',
            ],
            datasets: [
                {
                    label: '2023',
                    data: [
                        Comprobanteaj2023,
                        Comprobantean2023,
                        Comprobanteca2023,
                    ],
                    borderWidth: 1,
                    borderColor: 'rgb(0,14,14)',
                    backgroundColor: 'rgb(0, 86, 82)',
                    yAxisID: 'y',
                },
                {
                    label: '2024',
                    data: [
                        Comprobanteaj2024,
                        Comprobantean2024,
                        Comprobanteca2024
                    ],
                    borderWidth: 1,
                    borderColor: 'rgb(0,14,14)',
                    backgroundColor: 'rgb(40,179,0)',
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
                    text: 'Conteo de entidades menores'
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
