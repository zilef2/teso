import {ref} from "vue";
import {Chart} from 'chart.js';

export let chart10 = ref(null);
let ctx_entidades
let charInstance = []
let tipoVar = 'bar'

export async function buildCharEnti(numeroEntidades) {

    let transaccion = numeroEntidades.transaccion
    let Comprobanteci = numeroEntidades.Comprobanteci
    let Comprobantece = numeroEntidades.Comprobantece
    let Comprobanteaj = numeroEntidades.Comprobanteaj
    let Comprobantean = numeroEntidades.Comprobantean
    let Comprobanteca = numeroEntidades.Comprobanteca

    await new Promise(resolve => setTimeout(resolve, 10));
    ctx_entidades = chart10.value.getContext('2d');
    charInstance = new Chart(ctx_entidades, {
        type: tipoVar,
        data: {
            labels: [
                'transacciones',
                'CI',
                'CE',
                'AJ',
                'AN',
                'CA',
            ],

            datasets: [
                {
                    label: '# registros',
                    data: [transaccion,Comprobanteci,Comprobantece,Comprobanteaj,Comprobantean,Comprobanteca],
                    borderWidth: 1,
                    borderColor: 'rgb(0,14,14)',
                    backgroundColor: 'rgb(0, 86, 82)',
                    yAxisID: 'y',
                },
                // {
                //     label: '# CI',
                //     data: [0,Comprobanteci,0,0,0,0],
                //     borderWidth: 1,
                //     borderColor: 'rgb(0,14,14)',
                //     backgroundColor: 'rgb(0, 86, 82)',
                //     yAxisID: 'y',
                // },
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
