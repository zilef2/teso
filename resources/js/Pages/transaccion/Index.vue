<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, router, useForm, usePage} from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch} from 'vue';

import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';

import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon, PencilIcon, TrashIcon} from '@heroicons/vue/24/solid';
import Create from '@/Pages/transaccion/Create.vue';
import Edit from '@/Pages/transaccion/Edit.vue';
import Delete from '@/Pages/transaccion/Delete.vue';

import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import TextInput from "@/Components/TextInput.vue";
import Indicadores from "@/Pages/transaccion/Indicadores.vue";
// import { CursorArrowRippleIcon, ChevronUpDownIcon,QuestionMarkCircleIcon, EyeIcon, PencilIcon, TrashIcon, UserGroupIcon } from '@heroicons/vue/24/solid';

const {_, debounce, pickBy} = pkg
const props = defineProps({
    fromController: Object,
    total: Number,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    title: String,

    numberPermissions: Number,
    losSelect: Object,//normally used by headlessui
    thisAtributos: Object,
    Indicadores: Object,
})

const data = reactive({
    params: {
        search: props.filters.search,
        searchContrapartida: props.filters.searchContrapartida,
        searchDocumento: props.filters.searchDocumento,
        searchCodigo: props.filters.searchCodigo,
        searchConcepto: props.filters.searchConcepto,
        searchDocRef: props.filters.searchDocRef,
        OnlyCP: props.filters.OnlyCP,
        OnlyEmptyCP: props.filters.OnlyEmptyCP,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    transacciono: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    subirOpen: false,
    editOpen: false,
    deleteOpen: false,
    // deleteBulkOpen: false,
    dataSet: usePage().props.app.perpage,
    //ci
    procensandoCPCI: false,
    procensandAJCI: false,
    procensandANCI: false,
    //ce
    procensandCE: false,
})

// <!--<editor-fold desc="order, watchclone, select">-->
const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("transaccion.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((transaccion) => {
            data.selectedId.push(transaccion.id)
        })
    }
}
const select = () => {
    data.multipleSelect = props.fromController?.data.length === data.selectedId.length;
}
// <!--</editor-fold>-->


const form = useForm({
    id: 0,
})
// watchEffect(() => { })


// text // number // dinero // date // datetime // foreign
const titulos = [
    // { order: 'codigo', label: 'codigo', type: 'text' },
    {order: 'codigo_cuenta_contable', label: 'codigo_cuenta_contable', type: 'text'},
    {order: 'numero_cuenta_bancaria', label: 'numero_cuenta_bancaria', type: 'text'},
    {order: 'banco', label: 'banco', type: 'text'},
    {order: 'tipo_de_recurso', label: 'tipo_de_recurso', type: 'text'},
    // { order: 'inventario', label: 'inventario', type: 'foreign',nameid:'nombre'},
];
const Buscar_CP_CI = () => {
    data.procensandoCPCI = true
    form.post(route('Buscar_CP_CI'), {
        onFinish: () => data.procensandoCPCI = false,
    });
}
const Buscar_AJ_CI = () => {
    data.procensandAJCI = true
    form.post(route('Buscar_AJ_CI'), {
        onFinish: () => data.procensandAJCI = false,
    });
}
const Buscar_AN_CI = () => {
    data.procensandANCI = true
    form.post(route('Buscar_AN_CI'), {
        onFinish: () => data.procensandANCI = false,
    });
}
const Buscar_CP_CE = () => {
    data.procensandCE = true
    form.post(route('Buscar_CP_CE'), {
        onFinish: () => data.procensandCE = false,
    });
}
</script>

<template>
    <Head :title="props.title"/>

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold"/>
        <div class="space-y-4">
            <!-- {{ props.fromController.data[2] }} -->
            <div class="flex justify-between px-4 sm:px-0">
                <div class="inline-flex rounded-xl overflow-hidden w-fit">
                        <Link :href="route('jobs')">
                            <PrimaryButton class="rounded-lg mx-2">
                                Ver Cruces
                            </PrimaryButton>
                        </Link>
                    <div class="mx-2">
                        <PrimaryButton v-if="!form.processing || !data.procensandoCPCI" class="rounded-lg"
                                       @click="Buscar_CP_CI">
                            Contrapartidas CI
                            <!--                            de {{OnlyMonthAndYear(Date.now())}}-->
                        </PrimaryButton>
                        <div v-else class="text-sky-600">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Procesando la asignacion de concepto (CI)...
                        </div>
                    </div>
                    <div class="mx-2">
                        <PrimaryButton v-if="!form.processing || !data.procensandAJCI" class="rounded-lg"
                                       @click="Buscar_AJ_CI">
                            Ajustes de CI
                        </PrimaryButton>

                        <div v-else class="text-sky-600">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Procesando la asignacion de concepto (AJ)...
                        </div>
                    </div>
                    <div class="mx-2">
                        <PrimaryButton v-if="!form.processing || !data.procensandANCI" class="rounded-lg"
                                       @click="Buscar_AN_CI">
                            Anulaciones de CI
                            <!--                            {{OnlyMonthAndYear(Date.now())}}-->
                        </PrimaryButton>
                        <div v-else class="text-sky-600">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Procesando la asignacion de concepto (AN)...
                        </div>
                    </div>

                    <div class="mx-4 mb-1"> | </div>
                    <div class="mx-2 mb-1">
                        <PrimaryButton v-if="!form.processing || !data.procensandCE" class="rounded-lg"
                                       @click="Buscar_CP_CE">
                            Ajustes de CE
                        </PrimaryButton>
                        <div v-else class="text-sky-600">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Procesando CE...
                        </div>
                    </div>


                    <Create v-if="can(['create transaccion'])" :numberPermissions="props.numberPermissions"
                            :titulos="titulos" :show="data.createOpen" @close="data.createOpen = false"
                            :title="props.title"
                            :losSelect="props.losSelect"/>

                    <Edit v-if="can(['update transaccion'])" :titulos="titulos"
                          :numberPermissions="props.numberPermissions" :show="data.editOpen"
                          @close="data.editOpen = false"
                          :transacciona="data.transacciono" :title="props.title" :losSelect="props.losSelect"/>

                    <Delete v-if="can(['delete transaccion'])" :numberPermissions="props.numberPermissions"
                            :show="data.deleteOpen" @close="data.deleteOpen = false" :transacciona="data.transacciono"
                            :title="props.title"/>
                </div>
                <div class="my-1">Reg/pág
                    <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet"/>
                    - {{ props.fromController.total }}
                </div>

            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-xl">
                <div class="flex justify-items-end p-3 gap-6">
                    <div class="flex space-x-2">
                        <!-- <DangerButton @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length != 0 && can(['delete transaccion'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton> -->
                    </div>
                    <TextInput v-model="data.params.search" type="text"
                               class="block w-1/6 xl:w-1/12 rounded-lg" placeholder="Código cuenta contable"/>
                    <TextInput v-model="data.params.searchCodigo" type="text"
                               class="block w-1/6 xl:w-1/12 rounded-lg" placeholder="Código"/>

                    <TextInput v-model="data.params.searchDocumento" type="text"
                               class="block w-1/6 xl:w-1/12 rounded-lg" placeholder="Documento"/>
                    <TextInput v-model="data.params.searchContrapartida" type="text"
                               class="block w-1/6 xl:w-1/12 rounded-lg" placeholder="CP"/>

                    <TextInput v-model="data.params.searchConcepto" type="text"
                               class="block w-1/6 xl:w-1/12 rounded-lg" placeholder="Concepto de flujo"/>
                    <TextInput v-model="data.params.searchDocRef" type="number"
                               class="block w-4/6 md:w-3/6 xl:w-1/12 rounded-lg" placeholder="Doc Ref"/>
                    <div class="my-2 flex gap-2">
                        <input v-model="data.params.OnlyCP" value="onlycp" type="radio" id="rbutton1" class="my-2"/>
                        <label class="mt-1">Con CP</label>
                        <input v-model="data.params.OnlyCP" value="onlyemptycp" type="radio" id="rbutton2"
                               class="my-2"/>
                        <label class="mt-1">Sin CP</label>
                        <input v-model="data.params.OnlyCP" value="noSeEncontro" type="radio" id="rbutton2"
                               class="my-2"/>
                        <label class="mt-1">No se encontro</label>
                        <input v-model="data.params.OnlyCP" value="allcp" type="radio" id="rbutton3" class="my-2"/>
                        <label class="mt-1">Todas</label>
                    </div>
                </div>

                <div class="overflow-x-auto scrollbar-table">
                    <table v-if="props.total > 0" class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                        <tr class="dark:bg-gray-900/50 text-left">
                            <th class="px-2 py-4 text-center">
                                <Checkbox v-model:checked="data.multipleSelect" @change="selectAll"/>
                            </th>
                            <th v-if="numberPermissions > 1" class="px-2 py-4">Accion</th>

                            <th class="px-2 py-4 text-center">#</th>
                            <th v-for="titulo in props.thisAtributos" class="px-2 py-4 cursor-pointer"
                                v-on:click="order(titulo)">
                                <div class="flex justify-between items-center">
                                    <span>{{ lang().label[titulo] }}</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
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
                            class="border-t border-gray-200
                             odd:bg-white even:bg-slate-100
                             hover:bg-gray-200/30
                             dark:border-gray-700 hover:dark:bg-gray-900/20">

                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                <input
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"
                                    type="checkbox" @change="select" :value="claseFromController.id"
                                    v-model="data.selectedId"/>
                            </td>
                            <td v-if="numberPermissions > 1" class="whitespace-nowrap py-4 w-12 px-2 sm:py-3">
                                <div class="flex justify-center items-center">
                                    <div class="rounded-md overflow-hidden">
                                        <!--                                            v-show="can(['update transaccion'])"-->
                                        <InfoButton type="button"
                                                    @click="(data.editOpen = true), (data.transacciono = claseFromController)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                            <PencilIcon class="w-4 h-4"/>
                                        </InfoButton>
                                        <!--                                            v-show="can(['delete transaccion'])"-->
<!--                                        <DangerButton type="button"-->
<!--                                                      @click="(data.deleteOpen = true), (data.transacciono = claseFromController)"-->
<!--                                                      class="px-2 py-1.5 rounded-none"-->
<!--                                                      v-tooltip="lang().tooltip.delete">-->
<!--                                            <TrashIcon class="w-4 h-4"/>-->
<!--                                        </DangerButton>-->
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">{{ ++indexu }}</td>
                            <!--                                <td v-for="titulo in titulos" class="whitespace-nowrap py-4 px-2 sm:py-3">-->
                            <td v-for="titulo in props.thisAtributos" class="whitespace-wrap py-4 px-2 sm:py-3">
                                <span v-if="titulo === 'documento'"
                                    @click="data.params.searchDocumento = claseFromController[titulo]"
                                    class="underline text-blue-500 cursor-pointer">
                                    {{ claseFromController[titulo] }}
                                </span>
                                <span v-else> {{ claseFromController[titulo] }} </span>
                            </td>

                        </tr>
                        <!--                            <tr class="border-t border-gray-600">-->
                        <!--                                <td v-if="numberPermissions > 1"-->
                        <!--                                    class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"> - -->
                        <!--                                </td>-->
                        <!--                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"> Total: </td>-->
                        <!--                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">-->
                        <!--                                    {{ props.total }}-->
                        <!--                                </td>-->
                        <!--                            </tr>-->
                        </tbody>
                    </table>
                    <h2 v-else class="text-center text-xl my-8">Sin Registros</h2>
                </div>
                <div v-if="props.total > 0"
                     class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <Pagination :links="props.fromController" :filters="data.params"/>
                </div>

                <div class="grid grid-cols-1 mx-auto">
                    <div class="mx-auto items-center content-center place-self-center inline-flex">

                        <Indicadores
                            :nombre="'Transacciones no encontradas'"
                            :Indicador="props.Indicadores.NoSeEncontro"
                            :Total="props.Indicadores.Transacciones"
                            class="text-center"
                        />
                        <Indicadores
                            :nombre="'Ajustes'"
                            :Indicador="props.Indicadores.AJCount"
                            :Total="props.Indicadores.Transacciones"
                            class="text-center"
                        />
                        <Indicadores
                            :nombre="'Anulaciones'"
                            :Indicador="props.Indicadores.ANCount"
                            :Total="props.Indicadores.Transacciones"
                            class="text-center"
                        />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
