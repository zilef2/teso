<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch} from 'vue';

import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';

import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon, PencilIcon, TrashIcon} from '@heroicons/vue/24/solid';
// import { CursorArrowRippleIcon, ChevronUpDownIcon,QuestionMarkCircleIcon, EyeIcon, PencilIcon, TrashIcon, UserGroupIcon } from '@heroicons/vue/24/solid';
import Create from '@/Pages/Comprobante/Create.vue';
import Edit from '@/Pages/Comprobante/Edit.vue';
import Delete from '@/Pages/Comprobante/Delete.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';

import {formatDate, number_format} from '@/global.ts';

const { _, debounce, pickBy } = pkg
const props = defineProps({
    fromController: Object,
    total: Number,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    title: String,

    numberPermissions: Number,
    losSelect:Object,//normally used by headlessui
    pluckResultados:Object,
})

const data = reactive({
    params: {
        codigo: props.filters.codigo,
        numero_documento: props.filters.numero_documento,
        valor_debito: props.filters.valor_debito,
        valor_credito: props.filters.valor_credito,
        resultado_asientos: props.filters.resultado_asientos,
        sin_afectacion: props.filters.sin_afectacion,

        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    Comprobanteo: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    // deleteBulkOpen: false,
    dataSet: usePage().props.app.perpage,
})

// <!--<editor-fold desc="order, watchclone, select">-->
const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("IndexCE"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((Comprobante) => {
            data.selectedId.push(Comprobante.id)
        })
    }
}

const select = () => data.multipleSelect = props.fromController?.data.length === data.selectedId.length;
// <!--</editor-fold>-->


// const form = useForm({ })
// watchEffect(() => { })


// text // number // dinero // date // datetime // foreign
const titulos = [
    { order: 'codigo', label: 'codigo', type: 'text' },
    { order: 'resultado_asientos', label: 'resultado_asientos', type: 'text' },
    { order: 'sin_afectacion', label: 'sin_afectacion', type: 'bool_afectacion' },
    { order: 'cuenta_contrapartida', label: 'cuenta_contrapartida', type: 'text' },
    { order: 'notas', label: 'notas', type: 'longtext' },
    { order: 'numero_documento', label: 'numero_documento', type: 'text' },
    // { order: 'numero_cheque', label: 'numero_cheque', type: 'text' },
    { order: 'fecha_elaboracion', label: 'fecha_elaboracion', type: 'text' },
    { order: 'consecutivo', label: 'consecutivo', type: 'text' },
    { order: 'codigo_cuenta', label: 'codigo_cuenta', type: 'text' },
    { order: 'valor_debito', label: 'valor_debito', type: 'text' },
    { order: 'valor_credito', label: 'valor_credito', type: 'text' },
    { order: 'nombre_cuenta', label: 'nombre_cuenta', type: 'text' },
    { order: 'ccostos', label: 'ccostos', type: 'text' },
    { order: 'nit', label: 'nit', type: 'text' },
    { order: 'nombre', label: 'nombre', type: 'text' },
    { order: 'codigo_asiento', label: 'codigo_asiento', type: 'text' },
    { order: 'documento_ref', label: 'documento_ref', type: 'text' },
    { order: 'plan_cuentas', label: 'plan_cuentas', type: 'text' },
  // { order: 'inventario', label: 'inventario', type: 'foreign',nameid:'nombre'},
];
</script>
<template>
    <Head :title="'CE'" />

    <AuthenticatedLayout>
        <Breadcrumb :title="'CE'" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold"/>
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true"
                        v-if="can(['create Comprobante'])">
                        {{ lang().button.new }}
                    </PrimaryButton>

                    <Create v-if="can(['create Comprobante'])" :numberPermissions="props.numberPermissions"
                        :titulos="titulos" :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        :losSelect=props.losSelect />

                    <Edit v-if="can(['update Comprobante'])" :titulos="titulos"
                        :numberPermissions="props.numberPermissions" :show="data.editOpen" @close="data.editOpen = false"
                        :Comprobantea="data.Comprobanteo" :title="props.title" :losSelect=props.losSelect />

                    <Delete v-if="can(['delete Comprobante'])" :numberPermissions="props.numberPermissions"
                        :show="data.deleteOpen" @close="data.deleteOpen = false" :Comprobantea="data.Comprobanteo"
                        :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2 gap-4">
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <!-- <DangerButton @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length != 0 && can(['delete Comprobante'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton> -->
                    </div>
                    <TextInput  v-model="data.params.resultado_asientos" type="text"
                        class="block w-4/6 md:w-3/6 lg:w-2/6 mx-4 rounded-lg" placeholder="Resultados" />
                    <TextInput  v-model="data.params.sin_afectacion" type="text"
                        class="block w-4/6 md:w-3/6 lg:w-2/6 mx-4 rounded-lg" placeholder="Afectacion" />
                    <TextInput  v-model="data.params.numero_documento" type="text"
                        class="block w-4/6 md:w-3/6 lg:w-2/6 mx-4 rounded-lg" placeholder="Numero documento" />
                    <TextInput  v-model="data.params.valor_debito" type="text"
                        class="hidden md:block md:w-3/6 lg:w-2/6 mx-4 rounded-lg" placeholder="Valor debito" />
                    <TextInput  v-model="data.params.valor_credito" type="text"
                        class="hidden md:block md:w-3/6 lg:w-2/6 mx-4 rounded-lg" placeholder="Valor credito" />
                </div>
                <div class="flex justify-between p-2">
                    <div v-if="props.pluckResultados.length === 0" class="flex space-x-2 gap-4 text-red-600">
                        No se ha cruzado la información
                    </div>
                    <div v-else class="flex space-x-2 gap-4">
                        <ul v-for="result in props.pluckResultados">
                            <li class="text-blue-600">{{result}}</li>
                        </ul>
                        {{}}
                    </div>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table v-if="props.total > 0" class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900/50 text-left">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th v-if="numberPermissions > 1" class="px-2 py-4">Accion</th>

<!--                                <th class="px-2 py-4 text-center">#</th>-->
                                <th v-for="titulo in titulos" class="px-2 py-4 cursor-pointer"
                                    v-on:click="order(titulo['order'])">
                                    <div class="flex justify-between items-center w-fit">
                                        <span>{{ lang().label[titulo['label']] }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <!-- <th class="px-2 py-4 cursor-pointer" v-on:click="order('fecha_nacimiento')">
                                    <div class="flex justify-between items-center"> <span>{{ lang().label.edad }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(claseFromController, indexu) in props.fromController.data" :key="indexu"
                                class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20">

                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    <input
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"
                                        type="checkbox" @change="select" :value="claseFromController.id"
                                        v-model="data.selectedId" />
                                </td>
                                <td v-if="numberPermissions > 1" class="whitespace-nowrap py-4 w-12 px-2 sm:py-3">
                                    <div class="flex justify-center items-center">
                                        <div class="rounded-md overflow-hidden">
                                            <InfoButton v-show="can(['update Comprobante'])" type="button"
                                                @click="(data.editOpen = true), (data.Comprobanteo = claseFromController)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <DangerButton v-show="can(['delete Comprobante'])" type="button"
                                                @click="(data.deleteOpen = true), (data.Comprobanteo = claseFromController)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
<!--                                <td class="py-4 px-2 sm:py-3 text-center">{{ ++indexu }}</td>-->
                                <td v-for="titulo in titulos" class="py-4 px-2 sm:py-3">
                                    <span v-if="titulo['type'] === 'text'" class="whitespace-nowrap"> {{ claseFromController[titulo['order']] }} </span>
                                    <p v-if="titulo['type'] === 'longtext'" class="whitespace-wrap md:w-96"> {{ claseFromController[titulo['order']] }} </p>
                                    <span v-if="titulo['type'] === 'number'" class="whitespace-nowrap"> {{ number_format(claseFromController[titulo['order']], 0, false) }} </span>
                                    <span v-if="titulo['type'] === 'bool_afectacion'" class="whitespace-nowrap"> {{
                                            claseFromController[titulo['order']] === 1 ? 'Sin afectación' :
                                            claseFromController[titulo['order']] === 0 ? 'Con afectación' : 'Sin revisar'
                                        }} </span>
                                    <span v-if="titulo['type'] === 'dinero'" class="whitespace-nowrap"> {{ number_format(claseFromController[titulo['order']], 0, true) }} </span>
                                    <span v-if="titulo['type'] === 'date'" class="whitespace-nowrap"> {{ formatDate(claseFromController[titulo['order']], false) }} </span>
                                    <span v-if="titulo['type'] === 'datetime'" class="whitespace-nowrap"> {{ formatDate(claseFromController[titulo['order']], true) }} </span>
                                    <span v-if="titulo['type'] === 'foreign'" class="whitespace-nowrap"> {{ claseComprobantea[titulo['order']][titulo['nameid']] }} </span>
                                </td>

                            </tr>
                            <tr class="border-t border-gray-600">
                                <td v-if="numberPermissions > 1"
                                    class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> -
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"> Total: </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    {{ props.total }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h2 v-else class="text-center text-xl my-8">Sin Registros</h2>
                </div>
                <div v-if="props.total > 0"
                    class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
