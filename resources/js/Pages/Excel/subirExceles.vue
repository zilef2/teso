<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {reactive} from 'vue';
import pkg from 'lodash';

import {ArrowUpCircleIcon, TableCellsIcon} from '@heroicons/vue/24/solid';
import '@vuepic/vue-datepicker/dist/main.css'

const {_, debounce, pickBy} = pkg
const props = defineProps({
    title: String,
    ntransaccion: Number,
})
const data = reactive({
    UniversidadSelect: null,

    mensajeAviso:[
      '',
      'El excel debe contar con el formato aprobado',
      'El excel debe contar con el formato aprobado (CI,AJ, AN)', //comprobantes
      'El excel debe contar con el formato aprobado (Sin afectacion)',//afe sin
      'El excel debe contar con el formato aprobado (Con afectacion)',//afe con
      '', //cuentas
    ],
    rutas:[
        '',
        'upExTransacciones',
        'uploadFileComprobantes',
        'uploadFileAfe',
        'uploadFileConAfe',
        'upExCuentas',
    ],
    NombresEntidades: [
        '',
        'Transacciones',
        'Comprobantes',
        'CE sin Afectacion',
        'CE con Afectacion',
        'cuentas',
    ],

    //tailwind
    ClassCantidadDeBotonesPorPagina: 'p-4 w-full md:w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/6',
})

const form = useForm({
    archivo: [],
    Contador: 0,
    Mes: 0,
    // fecha_fin: '2023-04-03T'+horas[1]+':00', //toerase
});

// <!--<editor-fold desc="upload taks">-->
function uploadFileCuentas() {
    form.post(route('upExCuentas'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // form.reset()
            // data.respuesta = $page.props.flash.success
        },
        onError: () => {
            alert('Hay errores en algunos campos')
        },
        onFinish: () => null,
    });
}

function uploadFileTransacciones() {
    form.post(route('upExTransacciones'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // form.reset()
            // data.respuesta = $page.props.flash.success
        },
        onError: () => {
            alert('Hay errores en algunos campos')
        },
        onFinish: () => null,
    });
}

function uploadFileComprobantes() {
    form.post(route('uploadFileComprobantes'), {
        preserveScroll: true,
        onSuccess: () => {
        },
        onError: () => alert('Hay errores en algunos campos'),
        onFinish: () => null,
    });
}

// <!--</editor-fold>-->

function uploadFileGeneric(contado) {
    form.Contador = contado
    form.post(route(data.rutas[contado]), {
        preserveScroll: true,
        onSuccess: () => {
        },
        onError: () => alert('Hay errores en algunos campos'),
        onFinish: () => null,
    });
}


// <!--<editor-fold desc="formatoNecesit">-->

let formatoNecesita = [];

//transacciones o auxiliar
formatoNecesita[1] = [ //transacciones
    'codigo_cuenta_contable',
    'nombre_cuenta',
    'codigo',
    'documento',//3
    'fecha_elaboracion',
    'descripcion',
    'comprobante',
    'valor_debito',
    'valor_credito',
    'nit',
    'nombre',
    'cod_costos',
    'desc_costos',
    'codigo_interno_cuenta',
    'codigo_tercero',
    'ccostos',
    'saldo_inicial',
    'saldo_final',
    'nombre_empresa',
    'nit_empresa',
    'documento_ref',
    'consecutivo',
    'periodo',
    'plan_cuentas',
]
//comprobantes
formatoNecesita[2] = [
    'codigo',
    'descripcion',
    'comprobante',
    'descripcion2',
    'notas',
    'numero_documento',
    'numero_cheque',
    'fecha_elaboracion',
    'consecutivo',
    'codigo_cuenta',
    'nombre_cuenta',
    'ccostos',
    'nit',
    'nombre',
    'valor_debito',
    'valor_credito',
    'codigo_asiento',
    'documento_ref',
    'plan_cuentas',
]

//cuentas
formatoNecesita[4] = [
    'codigo_cuenta_contable',
    'numero_cuenta_bancaria',
    'banco',
    'tipo_de_cuenta',
    'tipo_de_recurso',
    'convenio',
    'estado'
]
//sin afectacion
formatoNecesita[3] = [
    'valor_debito',
    'valor_credito',
    'codigo_cuenta',
    'codigo_asiento',
    'tipo',
    'codigo',
    'fecha_elaboracion',
    'consecutivo',
    'descripcion',
    'descripcion_concepto',
    'codigo_banco',
    'otros',
    'taquilla',
    'consecutivo',
    'nombre_empresa',
    'nombre_dependencia',
]
//con afectacion
formatoNecesita[5] = [
    'consecutivo',
    'no_op',
    'numero_cheque',
    'valor_egreso',
    'valor_total',
    'nombre',
    'numero_cuenta',
    'codigo_resumido',
    'nombre_proyecto',
    'nit',
    'nombre',
    'saldo_rubro',
    'rubro',
    'nombre_empresa',
    'nombre_dependencia',
    'fecha_elaboracion',
    'estado',
    'descripcion',

]

// data.UniversidadSelect = vectorSelect(data.UniversidadSelect,props.UniversidadSelect,'una')
// const downloadExcel = () => { window.open('users/export/' + form.quincena + '/' + (form.fecha_ini.month) + '/' + form.fecha_ini.year, '_blank') }
// <!--</editor-fold>-->

// <!--<editor-fold desc="fin script">-->
const Abecedario = Array.from({length: 26}, (_, i) => String.fromCharCode(97 + i));
// <!--</editor-fold>-->

const handleFileUpload = (event, numeroArchivo) => {
    const file = event.target.files[0]; // Tomar el primer archivo
    if (file) {
        form.archivo[numeroArchivo] = file;
    } else {
        alert('archivo no detectado')
    }
};
</script>

<template>
    <Head :title="props.title"></Head>
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div v-if="$page.props.flash.warning2" class="px-4 my-2 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <div class="flex max-w-screen-xl shadow-lg rounded-lg">
                        <div class="bg-yellow-600 py-4 px-6 rounded-l-lg flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="fill-current text-white"
                                 width="20" height="20">
                                <path fill-rule="evenodd"
                                      d="M8.22 1.754a.25.25 0 00-.44 0L1.698 13.132a.25.25 0 00.22.368h12.164a.25.25 0 00.22-.368L8.22 1.754zm-1.763-.707c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0114.082 15H1.918a1.75 1.75 0 01-1.543-2.575L6.457 1.047zM9 11a1 1 0 11-2 0 1 1 0 012 0zm-.25-5.25a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5z"></path>
                            </svg>
                        </div>
                        <div
                            class="px-8 py-6 bg-white rounded-r-lg flex justify-between items-center w-full border border-l-transparent border-gray-200">
                            <div v-html="$page.props.flash.warning2"></div>
                            <!-- <button>
                                <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-gray-700" viewBox="0 0 16 16" width="20" height="20"> <path fill-rule="evenodd" d="M3.72 3.72a.75.75 0 011.06 0L8 6.94l3.22-3.22a.75.75 0 111.06 1.06L9.06 8l3.22 3.22a.75.75 0 11-1.06 1.06L8 9.06l-3.22 3.22a.75.75 0 01-1.06-1.06L6.94 8 3.72 4.78a.75.75 0 010-1.06z"> </path> </svg>
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 4xl:px-1 py-6 mx-auto">
                        <!--                        v-if="can(['create user'])"-->
                        <div class="flex flex-wrap -m-4">
                            <div v-for="numeroArchivo in data.NombresEntidades.length"
                                 :class="data.ClassCantidadDeBotonesPorPagina">
                                <div v-if="data.NombresEntidades[numeroArchivo] !== '' && data.NombresEntidades[numeroArchivo]"
                                     class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <TableCellsIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center"/>
                                    <div class="p-6">
                                        <h3 class="title-font text-lg font-medium text-gray-900 mb-3">
                                            Subir {{ data.NombresEntidades[numeroArchivo] }}
                                        </h3>
                                        <p class="leading-relaxed mb-3">
                                            {{data.mensajeAviso[numeroArchivo]}}
                                        </p>
                                        <!--                                        uploadFileGeneric-->
                                        <!--                                        data.NombresEntidades-->
                                        <form v-if="data.mensajeAviso[numeroArchivo] !== ''" @submit.prevent="uploadFileGeneric(numeroArchivo)" id="upload">
                                            <!--                                                   @input="form.archivo[numeroArchivo] = $event.target.files[numeroArchivo]"-->
                                            <input type="file"
                                                   @change="handleFileUpload($event, numeroArchivo)"
                                                   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .csv,application/vnd.ms-excel"
                                            />
                                            <br><br>
                                            <progress v-if="form.progress" :value="form.progress.percentage - 20"
                                                      max="100"
                                                      class="bg-sky-200">
                                                {{ form.progress.percentage }}%
                                            </progress>
                                            <div class="flex">
                                                <!--                                                can(['create user']) && -->
                                                <PrimaryButton
                                                    v-show="form.archivo[numeroArchivo] !== null"
                                                    :disabled="form.archivo[numeroArchivo] == null"
                                                    class=" my-4 rounded-md mx-2"
                                                    :class="{ 'bg-gray-200' : form.archivo[numeroArchivo] == null}">
                                                    {{ lang().button.subir }} excel
                                                </PrimaryButton>
                                            </div>
                                        </form>
                                        <div v-if="data.mensajeAviso[numeroArchivo] !== ''">

                                            <h2 class="text-xl text-gray-900 dark:text-white mt-12">El formato necesita
                                                las siguientes columnas</h2>
                                            <ul class="list-decimal my-6 mx-5">
                                                <li v-for="(campos, indicice) in formatoNecesita[numeroArchivo]"
                                                    class="text-lg">
                                                    {{ Abecedario[indicice] }}. {{ campos }}
                                                </li>
                                            </ul>

                                            <div class="flex items-center flex-wrap my-6">
                                                <span
                                                    class="text-gray-600 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                                <svg class="w-1 h-4 mr-1" stroke="currentColor" stroke-width="2"
                                                     fill="none"
                                                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <p class="text-lg">{{ data.NombresEntidades[numeroArchivo] }}: {{ props.ntransaccion[numeroArchivo] }}</p>
                                            </span>
                                            </div>
                                        </div>
                                        <h2 v-else class="text-xl text-gray-900 dark:text-white mt-12">
                                            El modulo aun no esta disponible
                                        </h2>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
