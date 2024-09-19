<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch, ref, watchEffect, onMounted } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { router, usePage, Link, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import {ChevronUpDownIcon, CursorArrowRippleIcon,QuestionMarkCircleIcon, EyeIcon, PencilIcon, TrashIcon, ArrowUpCircleIcon } from '@heroicons/vue/24/solid';
import Create from '@/Pages/registrosmedio/Create.vue';
import Edit from '@/Pages/registrosmedio/Edit.vue';
import Delete from '@/Pages/registrosmedio/Delete.vue';
import DeleteBulk from '@/Pages/registrosmedio/DeleteBulk.vue';
import Abiertoycerrado from '@/Pages/registrosmedio/Abiertoycerrado.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import { formatDate, number_format } from '@/global.ts';
import RToast from '@/Components/RToast.vue';
import GreenButton from "@/Components/GreenButton.vue";

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
})

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    registrosmedioo: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    abiertoycerradoOpen: false,
    Abiertoycerrado:{},
    deleteBulkOpen: false,
    dataSet: usePage().props.app.perpage,
})

// <!--<editor-fold desc="order, watchclone, select">-->
const order = (field) => {
    data.params.field = field
    data.params.order = data.params.order === "asc" ? "desc" : "asc"
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("registrosmedio.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 150))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController.data.forEach((registrosmedio) => {
            data.selectedId.push(registrosmedio.id)
        })
    }
}
const select = () => {
    if (props.fromController.data.length == data.selectedId.length) {
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
    { order: 'fecha_realizacion', label: 'fecha_realizacion', type: 'datetime' },
    { order: 'userRecibir', label: 'userRecibir', type: 'text' },
    { order: 'userEntregar', label: 'userEntregar', type: 'text' },
    { order: 'Abiertoycerrado', label: 'Abiertos', type: 'details' },
    { order: 'adjetivo', label: 'adjetivo', type: 'text' },
    { order: 'placa', label: 'placa', type: 'text' },
    { order: 'categoria', label: 'categoria Aspectos', type: 'text' },
];

</script>

<template>
    <Head :title="props.title" />

    <AuthenticatedLayout>
        <RToast :flash="$page.props.flash"/>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" class="capitalize text-xl font-bold"/>
        <div class="space-y-4">
            <!-- {{ props.fromController.data[2] }} -->
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <Link :href="route('registrosmedio.create')" v-show="breadcrumbs.length !== 0">
                        <PrimaryButton class="rounded-none">
                            Nueva registrosmedio
                        </PrimaryButton>
                    </Link>

<!--                    <Edit :titulos="titulos"-->
<!--                        :numberPermissions="props.numberPermissions" :show="data.editOpen" @close="data.editOpen = false"-->
<!--                        :registrosmedioa="data.registrosmedioo" :title="props.title" :losSelect=props.losSelect />-->

                    <Delete  :numberPermissions="props.numberPermissions"
                        :show="data.deleteOpen" @close="data.deleteOpen = false" :registrosmedio="data.registrosmedioo"
                        :title="props.title" />
                    <DeleteBulk :show="data.deleteBulkOpen"
                        @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                        :selectedId="data.selectedId" :title="props.title" />
                    <Abiertoycerrado :show="data.abiertoycerradoOpen" @close="data.abiertoycerradoOpen = false" :Abiertoycerrado="data.Abiertoycerrado" />
                </div>
                <p class="flex my-2 text-sm">
                    Esta lista se actualiza cada minuto
                </p>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <span class="my-auto hidden xl:block">Registros por página</span>
                          <span class="my-auto md:hidden">R</span>
                        <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" />
                        <DangerButton @click="data.deleteBulkOpen = true"
                                      class="px-3 py-1.5"
                                      v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>
                    <TextInput v-if="props.numberPermissions > 1" v-model="data.params.search" type="text"
                        class="block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg" placeholder="Buscar por Area" />
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table v-if="props.total > 0" class="w-full border-collapse">
                        <thead class="uppercase text-sm border-t border-gray-200 dark:border-gray-700">
                            <tr class="dark:bg-gray-900/50 text-left">
                                <th class="px-2 py-4 text-cente border-b-[4px] border-sky-200 text-leftr">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll" />
                                </th>
                                <th class="px-2 py-4 border-b-[4px] border-sky-200 text-left">Accion</th>
<!--                                <th class="px-2 py-4 text-center">#</th>-->

<!--                                    v-on:click="order(titulo['order'])"-->
                                <th v-for="titulo in titulos" class="px-2 py-4 cursor-pointer border-b-[4px] border-sky-200 text-left">
                                    <div class="flex justify-between items-center">
                                        <span>{{ titulo['label'] }}</span>
<!--                                        <ChevronUpDownIcon class="w-4 h-4" />-->
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(claseFromController, indexu) in props.fromController.data" :key="indexu"
                                class="hover:bg-sky-100 transition-colors duration-[1s]
                                border-t border-gray-200 dark:border-gray-700
                                 hover:dark:bg-gray-900/20">

                                <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                    <input
                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"
                                        type="checkbox" @change="select" :value="claseFromController.id"
                                        v-model="data.selectedId" />
                                </td>
                                <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3">
                                    <div class="flex justify-center items-center">
                                        <div class="rounded-md overflow-hidden">
<!--                                            <Link :href="route('registrosmedio.edit',claseFromController.id)">-->
<!--                                                <InfoButton class="rounded-none"-->
<!--                                                            v-tooltip="'Editar'">-->
<!--                                                    <PencilIcon class="w-4 h-4" />-->
<!--                                                </InfoButton>-->
<!--                                            </Link>-->
<!--                                            <Link :href="route('Seguimiento',claseFromController.id)">-->
<!--                                                <InfoButton class="rounded-none"-->
<!--                                                            v-tooltip="'Seguimiento'">-->
<!--                                                    <ArrowUpCircleIcon class="w-4 h-4" />-->
<!--                                                </InfoButton>-->
<!--                                            </Link>-->
<!--                                            <Link :href="route('Informe',claseFromController.id)">-->
<!--                                                <GreenButton class="rounded-none"-->
<!--                                                            v-tooltip="'Informe'">-->
<!--                                                    <EyeIcon class="w-4 h-4" />-->
<!--                                                </GreenButton>-->
<!--                                            </Link>-->

                                            <DangerButton type="button"
                                                @click="(data.deleteOpen = true), (data.registrosmedioo = claseFromController)"
                                                class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.delete">
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                                <td v-for="titulo in titulos" class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <span v-if="titulo['type'] === 'text'"> {{ claseFromController[titulo['order']] }} </span>
                                    <span v-if="titulo['type'] === 'number'"> {{ number_format(claseFromController[titulo['order']], 0, false) }} </span>
                                    <span v-if="titulo['type'] === 'dinero'"> {{ number_format(claseFromController[titulo['order']], 0, true) }} </span>
                                    <span v-if="titulo['type'] === 'date'"> {{ formatDate(claseFromController[titulo['order']], false) }} </span>
                                    <span v-if="titulo['type'] === 'datetime'"> {{ formatDate(claseFromController[titulo['order']], true) }} </span>
                                    <span v-if="titulo['type'] === 'foreign'"> {{ claseFromController[titulo['order']][titulo['nameid']] }} </span>
                                    <div v-if="titulo['type'] === 'html'"> <p v-html="claseFromController[titulo['order']]"></p></div>
                                    <div v-if="titulo['type'] === 'details'">
                                         <div v-tooltip="lang().tooltip.detail"
                                            @click="(data.abiertoycerradoOpen = true), (data.Abiertoycerrado = claseFromController.registrofotografico)"
                                            class="whitespace-nowrap py-4 px-2 sm:py-3 cursor-pointer text-blue-600 dark:text-blue-400 font-bold underline">
                                            Visualizacion rápida
                                         </div>
                                    </div>
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
