<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head,Link, router, usePage} from '@inertiajs/vue3';
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
import Create from '@/Pages/cuenta/Create.vue';
import Edit from '@/Pages/cuenta/Edit.vue';
import Delete from '@/Pages/cuenta/Delete.vue';

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
    thisAtributos:Object,
})

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    cuentao: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    subirOpen: false,
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
    router.get(route("cuenta.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((cuenta) => {
            data.selectedId.push(cuenta.id)
        })
    }
}
const select = () => {
    if (props.fromController?.data.length == data.selectedId.length) {
        data.multipleSelect = true
    } else {
        data.multipleSelect = false
    }
}
// <!--</editor-fold>-->


// const form = useForm({ })
// watchEffect(() => { })


// text // number // dinero // date // datetime // foreign
const titulos = [
    // { order: 'codigo', label: 'codigo', type: 'text' },
    { order: 'codigo_cuenta_contable', label: 'codigo_cuenta_contable', type: 'text' },
    { order: 'numero_cuenta_bancaria', label: 'numero_cuenta_bancaria', type: 'text' },
    { order: 'banco', label: 'banco', type: 'text' },
    { order: 'tipo_de_recurso', label: 'tipo_de_recurso', type: 'text' },
    { order: 'estado', label: 'estado', type: 'text' },
  // { order: 'inventario', label: 'inventario', type: 'foreign',nameid:'nombre'},
];
</script>

<template>
    <Head :title="props.title" />

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold"/>
        <div class="space-y-4">
            <!-- {{ props.fromController.data[2] }} -->
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton class="rounded-none" @click="data.createOpen = true"
                        v-if="can(['istesorera'])">
                        {{ lang().button.new }}
                    </PrimaryButton>

<!--                    <PrimaryButton class="rounded-none" @click="data.subirOpen = true"-->
<!--                        v-if="can(['isSuper'])">-->
<!--                        Subir-->
<!--                    </PrimaryButton>-->

                    <Create v-if="can(['create cuenta'])" :numberPermissions="props.numberPermissions"
                        :titulos="titulos" :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                        :losSelect=props.losSelect />
                    <Link v-if="can(['istesorera'])" :href="route('subirexceles')">
                        <PrimaryButton class="rounded-none">
                            Subir Cuentas
                        </PrimaryButton>
                    </Link>
<!--                    <SubirCuenta v-if="can(['isSuper'])" :numberPermissions="props.numberPermissions"-->
<!--                        :titulos="titulos" :show="data.subirOpen" @close="data.subirOpen = false" :title="props.title"-->
<!--                        :losSelect=props.losSelect />-->

                    <Edit v-if="can(['update cuenta'])" :titulos="titulos"
                        :numberPermissions="props.numberPermissions" :show="data.editOpen" @close="data.editOpen = false"
                        :cuentaa="data.cuentao" :title="props.title" :losSelect=props.losSelect />

                    <Delete v-if="can(['delete cuenta'])" :numberPermissions="props.numberPermissions"
                        :show="data.deleteOpen" @close="data.deleteOpen = false" :cuentaa="data.cuentao"
                        :title="props.title" />
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2 gap-6">
                    <div class="flex space-x-2">
                        <!-- <DangerButton @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length != 0 && can(['delete cuenta'])" class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton> -->
                    </div>
                    <TextInput v-model="data.params.search" type="text"
                        class="block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg" placeholder="Codigo" />
                    <TextInput v-model="data.params.searchNumCuenta" type="number"
                        class="block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg" placeholder="Numero de cuenta" />
                    <TextInput v-model="data.params.searchBanco" type="text"
                        class="block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg" placeholder="Banco" />
                    <TextInput v-model="data.params.searchtipo" type="text"
                        class="block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg" placeholder="Tipo recurso" />
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table v-if="props.total > 0" class="w-full">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900/50 text-left">
                                <th class="px-2 py-4 text-center">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th v-if="numberPermissions > 1" class="px-2 py-4">Accion</th>

                                <th class="px-2 py-4 text-center">#</th>
                                <th v-for="titulo in props.thisAtributos" class="px-2 py-4 cursor-pointer"
                                    v-on:click="order(titulo)">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label[titulo] }}</span>
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
<!--                                            v-show="can(['update cuenta'])"-->
                                            <InfoButton  type="button"
                                                @click="(data.editOpen = true), (data.cuentao = claseFromController)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
<!--                                            v-show="can(['delete cuenta'])"-->
                                            <DangerButton  type="button"
                                                @click="(data.deleteOpen = true), (data.cuentao = claseFromController)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">{{ ++indexu }}</td>
<!--                                <td v-for="titulo in titulos" class="whitespace-nowrap py-4 px-2 sm:py-3">-->
                                <td v-for="titulo in props.thisAtributos" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <span> {{ claseFromController[titulo] }} </span>
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
                    <Pagination :links="props.fromController" :filters="data.params" />
                    <div class="ml-8">
                        Reg/pág <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
