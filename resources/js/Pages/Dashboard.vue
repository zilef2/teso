<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {ChevronRightIcon, KeyIcon, ShieldCheckIcon, UserIcon} from '@heroicons/vue/24/solid';
import {Head, Link} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Chart from 'chart.js/auto';
import {ref, onMounted, watchEffect} from 'vue';

// <!--<editor-fold desc=" lo demas">-->
// roles: Number,
const props = defineProps({
    users: Number,
    roles: Number,
    rolesNameds: Object,
    numberPermissions: Number,
})

const dashLinks = [
    // 'Informes',
    // 'roles',
];
const colores = [
    'bg-gray-400',
    // 'bg-gray-500',
    // 'bg-gray-600',
];
const descripcion = [
    'Descripcion',
    // 'Descripcion',
    // 'descripcion',
];
const laImg = [
    'KeyIcon',
    // 'KeyIcon',
    // 'KeyIcon',
    // 'KeyIcon',
];
const downloadAnexos = () => {
    window.open('downloadAnexos', '_blank')
}
// <!--</editor-fold>-->


const chartCanvas = ref(null);
const chartCanvas2 = ref(null);

onMounted(() => {
    const ctx = chartCanvas.value.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });


    const ctx2 = chartCanvas2.value.getContext('2d');
    const mixedChart = new Chart(ctx2, {
        data: {
            datasets: [{
                type: 'bar',
                label: 'Bar Dataset',
                data: [10, 20, 30, 40]
            }, {
                type: 'line',
                label: 'Line Dataset',
                data: [150, 50, 250, 5],
            }],
            labels: ['January', 'February', 'March', 'April']
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            hide: {
                animations: {
                    x: {
                        to: 0
                    },
                    y: {
                        to: 0
                    }
                }
            }
        }
    });


});
</script>

<template>

    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <Breadcrumb :title="'Resumen'" :breadcrumbs="[]"/>
        <div class="my-20">
            <canvas ref="chartCanvas"></canvas>
        </div>
        <div class="my-20">
            <canvas ref="chartCanvas2"></canvas>
        </div>

    </AuthenticatedLayout>
</template>
