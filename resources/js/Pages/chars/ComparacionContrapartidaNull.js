import {ref} from "vue";
import {Chart} from 'chart.js';

export let chart11 = ref(null);
let ctx_entidades
let charInstance = []
let tipoVar = 'doughnut'

export async function buildCharCPnull(ComparacionCP) {

    let transaccion2023sincp = parseInt(ComparacionCP.Comprobanteci2023sincp)
    let transaccion2024sincp = parseInt(ComparacionCP.Comprobanteci2024sincp)
    let transaccion2023concp = parseInt(ComparacionCP.Comprobanteci2023concp)
    let transaccion2024concp = parseInt(ComparacionCP.Comprobanteci2024concp)

    await new Promise(resolve => setTimeout(resolve, 10));
    ctx_entidades = chart11.value.getContext('2d');
    charInstance = new Chart(ctx_entidades, {
        type: tipoVar,
        data: {
            labels: ['Sin CP','Con CP'],
            datasets: [
            {
                label: '2023',
                data: [transaccion2023sincp, transaccion2023concp],
                backgroundColor: ['rgb(193,139,0)','rgb(40,179,0)'],
                hoverOffset: 4
            },
            {
                label: '2024',
                data: [transaccion2024sincp, transaccion2024concp],
                backgroundColor: ['rgb(193,139,0)','rgb(40,179,0)'],
                hoverOffset: 4
            },
            ]
        },
        options: {
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Transacciones con CP'
                }
            },
            responsive: true,
        }
    });
}
