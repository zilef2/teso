import {ref} from "vue";
import {Chart} from 'chart.js';

export let charConteoEntities = ref(null);
let ctx_entidades
let charInstance = []
let tipoVar = 'bar'

export async function buildCharEnti(numeroEntidades) {
    console.log('aqui ta',numeroEntidades)

    let transaccion2023 = numeroEntidades.transaccion2023
    let transaccion2024 = numeroEntidades.transaccion2024
    let Comprobanteci2023 = numeroEntidades.Comprobanteci2023
    let Comprobanteci2024 = numeroEntidades.Comprobanteci2024
    // let Comprobantece2023 = numeroEntidades.Comprobantece2023
    // let Comprobantece2024 = numeroEntidades.Comprobantece2024

    // let afectacion2023 = numeroEntidades.afectacion2023
    // let afectacion2024 = numeroEntidades.afectacion2024
    //
    // let asientos2023 = numeroEntidades.asientos2023
    // let asientos2024 = numeroEntidades.asientos2024

    await new Promise(resolve => setTimeout(resolve, 101));

    ctx_entidades = charConteoEntities.value.getContext('2d');
    charInstance = new Chart(ctx_entidades, {
        type: tipoVar,
        data: {
            labels: [
                'transacciones',
                'CI',
                // 'CE',
                'Afectacion',
                'Asientos',
            ],
            datasets: [
                {
                    label: '2023',
                    data: [
                        transaccion2023,
                        Comprobanteci2023,
                        // Comprobantece2023,
                        // afectacion2023,
                        // asientos2023
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
                        // Comprobantece2024,
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
