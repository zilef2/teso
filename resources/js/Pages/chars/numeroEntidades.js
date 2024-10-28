import {ref} from "vue";
import {Chart} from 'chart.js';

export let chart10 = ref(null);
let ctx_entidades
let charInstance = []
let tipoVar = 'bar'

export async function buildCharEnti(numeroEntidades) {

    let transaccion2023 = numeroEntidades.transaccion2023
    let transaccion2024 = numeroEntidades.transaccion2024
    let Comprobanteci2023 = numeroEntidades.Comprobanteci2023
    let Comprobanteci2024 = numeroEntidades.Comprobanteci2024
    let Comprobantece2023 = numeroEntidades.Comprobantece2023
    let Comprobantece2024 = numeroEntidades.Comprobantece2024
    let Comprobanteaj2023 = numeroEntidades.Comprobanteaj2023
    let Comprobanteaj2024 = numeroEntidades.Comprobanteaj2024
    let Comprobantean2023 = numeroEntidades.Comprobantean2023
    let Comprobantean2024 = numeroEntidades.Comprobantean2024
    let Comprobanteca2023 = numeroEntidades.Comprobanteca2023
    let Comprobanteca2024 = numeroEntidades.Comprobanteca2024

    await new Promise(resolve => setTimeout(resolve, 10));
    ctx_entidades = chart10.value.getContext('2d');
    charInstance = new Chart(ctx_entidades, {
        type: tipoVar,
        data: {
            labels: [
                'transacciones',
                'CI',
                'AJ',
                'AN',
                'CE',
                'CA',
            ],

            datasets: [
                {
                    label: '2023',
                    data: [
                        transaccion2023,
                        Comprobanteci2023,
                        Comprobanteaj2023,
                        Comprobantean2023,
                        Comprobantece2023,
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
                        transaccion2024,
                        Comprobanteci2024,
                        Comprobanteaj2024,
                        Comprobantean2024,
                        Comprobantece2024,
                        Comprobanteca2024,
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
                    text: 'Conteo de entidades'
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
