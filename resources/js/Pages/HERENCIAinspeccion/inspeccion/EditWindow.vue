<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import "vue-select/dist/vue-select.css";
import {onMounted, reactive, watchEffect} from 'vue';
import '@vuepic/vue-datepicker/dist/main.css'
import Toast from "@/Components/Toast.vue";
import {RecuperarASpectos, RecuperarSimpleProps, RecuperarVueMultiSelect, RecuperarVueSelectSST, TransformTdate} from "@/global";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import BackButton from "@/Components/BackButton.vue";

import ShowAspectos from "@/Pages/inspeccion/ShowAspectos.vue";
import SeccionTitular from "@/Pages/inspeccion/SeccionTitular.vue";
import SeccionTitularGrande from "@/Pages/inspeccion/SeccionTitularGrande.vue";

// --------------------------- ** -------------------------

const props = defineProps({
    numberPermissions: Number,
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,
    inspeccions: Object,
    responsable: Array,
    areainspeccion: Object,
    aspectos: Object,
    //only in edit
})
// const emit = defineEmits(["close"]);

const data = reactive({
    params: {
        pregunta: ''
    },
})

//very usefull
const justNames = props.titulos.map(names => names)
const form = useForm({
    ...Object.fromEntries(justNames.map(field => [field, ''])),
    areainspeccion: [],
    userRecibir: '',
    responsableSST: '',
    responsableSGA: '',
    responsables: '',
    AspectosID: [],
});

onMounted(() => {
    if (props.numberPermissions > 900) {
    }
    form.fecha_realizacion = TransformTdate(props.inspeccions.fecha_realizacion, true)
    RecuperarSimpleProps(form, props, [
        'userRecibir',
    ])
    RecuperarVueSelectSST(form, props, [
        'responsable',
    ])
    RecuperarVueMultiSelect(form, props, [
        'areainspeccion',
    ])
    RecuperarASpectos(form, props)
});


const update = () => {
    // console.log("🧈 debu pieza_id:", form.pieza_id);
    form.responsables = [form.responsableSST, form.responsableSGA]
    form.put(route('inspeccion.update', props.inspeccions.id), {
        // preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            form.reset()
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null,
    })
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
})


let categoriaAgrup = {}
//Recorremos el arreglo

// console.log("=>(EditWindow.vue:90) props.aspectos", props.aspectos);
props.aspectos.forEach(x => {
    if (!categoriaAgrup.hasOwnProperty(x.categoria)) {
        categoriaAgrup[x.categoria] = {
            aspectos: []
        }
    }
    //Agregamos los datos de aspectos.
    categoriaAgrup[x.categoria].aspectos.push({
        aspectoNombre: x.aspectoNombre,
        calificacion: x.calificacion,
        registrofotografico: x.registrofotografico,
        observaciones: x.observaciones,
    })
})

</script>

<template>
    <Toast :flash="$page.props.flash" class=""/>
    <section class="space-y-1">
        <section class="bg-white dark:bg-gray-900">

            <div class="lg:grid lg:min-h-screen lg:grid-cols-12">
                <SeccionTitular/>
                <div class="flex items-center justify-center px-0 py-8 lg:col-span-9 lg:py-4 xl:col-span-9">
                    <div class="flex-none w-14"></div>
                    <div class="grow">
                        <main class="flex-grow items-center justify-center px-0 py-8 lg:col-span-10 lg:py-">
                            <div class="w-full lg:min-w-xl">
                                <SeccionTitularGrande/>
                                <form @submit.prevent="update" class="mt-8">
                                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                                        <div class="xl:col-span-3 w-full">
                                            <label for="FirstName" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Área de
                                                inspección</label>
                                            <div id="areainspeccion" class="mt-1 ">
                                                <vSelect multiple v-model="form.areainspeccion"
                                                         :options="props.losSelect['areas']"
                                                >
                                                </vSelect>
                                                <!--                                                hola : {{props.losSelect['areas']}}-->

                                                <InputError class="mt-2" :message="form.errors.areainspeccion"/>
                                            </div>
                                        </div>
                                        <!--                                        qwe: {{ form.areainspeccion }}-->
                                        <div class="xl:col-span-1">
                                            <label for="LastName" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                                Fecha realizacion
                                            </label>
                                            <input
                                                type="date"
                                                v-model="form.fecha_realizacion"
                                                class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                                            />
                                        </div>
                                        <div class="xl:col-span-1">
                                            <label for="Email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                                RESPONSABLE INSPECCIÓN SG-SST
                                            </label>
                                            <div id="areainspeccion" class="mt-1">
                                                <v-select v-model="form.responsableSST" :options="props.losSelect['responsablesSST']">
                                                </v-select>
                                                <InputError class="mt-2" :message="form.errors.responsableSST"/>
                                            </div>
                                        </div>
                                        <div class="xl:col-span-1">
                                            <label for="Email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                                <p class="lowercase">RESPONSABLE INSPECCIÓN SG-A</p>
                                            </label>

                                            <div id="areainspeccion" class="mt-1">
                                                <v-select v-model="form.responsableSGA" :options="props.losSelect['responsablesSGA']"></v-select>
                                                <InputError class="mt-2" :message="form.errors.responsableSGA"/>
                                            </div>
                                        </div>
                                        <div class="xl:col-span-2 ">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Responsable de recibir la
                                                inspección</label>
                                            <TextInput id="userRecibir" type="text" class="mt-1 block w-full" v-model="form.userRecibir"
                                                       required :error="form.errors.userRecibir"/>
                                        </div>
                                        <div class="xl:col-span-1"></div>
                                    </div>

                                    <ShowAspectos :categoriaAgrup="categoriaAgrup"/>
                                    <div class="flex text-center mx-auto">
                                        <div class="xl:col-span-3 flex sm:items-center sm:gap-4 mt-12 mb-4 mx-auto">
                                            <button type="submit" class="inline-block shrink-0 rounded-md
                                            px-12 py-3 text-sm font-medium text-white text-center
                                            border border-blue-600 bg-blue-600
                                            hover:text-blue-600 focus:outline-none
                                             active:text-red-600 dark:hover:bg-blue-700 dark:hover:text-white
                                            transition hover:bg-transparent
                                            ">
                                                Guardar cambios
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex text-center mx-auto">
                                        <BackButton />
                                    </div>
                                </form>


                            </div>
                        </main>
                    </div>
                    <div class="flex-none w-14"></div>
                </div>
            </div>
        </section>
    </section>
</template>

<style>
@media (prefers-color-scheme: dark) {
    ::v-deep .vs-dropdown-option--active-color {
        color: #0c905f;
    }
}
</style>
