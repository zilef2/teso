<script setup>
import {useForm} from '@inertiajs/vue3';
import "vue-select/dist/vue-select.css";
import {computed, onMounted, reactive, ref, watch, watchEffect} from 'vue';
import '@vuepic/vue-datepicker/dist/main.css'
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import Toast from "@/Components/Toast.vue";
import BackButton from "@/Components/BackButton.vue";
import {formatDate, formatDateSimply} from "@/global";

import AspectosSegui from "@/Pages/inspeccion/Seguimiento/AspectosSegui.vue";

// --------------------------- ** -------------------------

const props = defineProps({
    numberPermissions: Number,
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,
    Inspeccion: Object,
    // historico_seguimientos: Object,
})
// const emit = defineEmits(["close"]);

const data = reactive({
    params: {
        pregunta: ''
    },
    categoriaAgrup: {},
})

const justNames = props.titulos.map(names => names)
const form = useForm({
    ...Object.fromEntries(justNames.map(field => [field, ''])),
    areainspeccion: [],
    userRecibir: '',
    responsable_sst: '',
    responsable_a: ''
});

onMounted(() => {
    adecuacion()
});


watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
})

const adecuacion = () => {
    props.Inspeccion.aspectos.forEach(equix => {
        if (!data.categoriaAgrup.hasOwnProperty(equix.categoria)) {
            data.categoriaAgrup[equix.categoria] = {
                aspectos: []
            }
        }
        //Agregamos los datos de aspectos.
        if (equix.pivot.calificacion !== 'NO APLICA') {
            const seguimientosCorrespondientes = props.Inspeccion.seguimientos.filter(seguimiento => {
                return seguimiento.aspecto_inspeccion_id === equix.pivot.id;
            });

            data.categoriaAgrup[equix.categoria].aspectos.push({
                id: equix.id,
                aspectoNombre: equix.nombre,
                calificacion: equix.pivot.calificacion,
                registrofotografico: equix.pivot.registrofotografico,
                observaciones: equix.pivot.observaciones,
                abierto: equix.pivot.abierto,
                aspecto_inspeccion_id: equix.pivot.id,
                seguimientos: seguimientosCorrespondientes,
            })
        }
    })
}

</script>

<template>
    <Toast :flash="$page.props.flash" class=""/>

    <section class="space-y-1">
        <section class="bg-gray-100 dark:bg-gray-900">
            <div class="lg:grid lg:min-h-screen lg:grid-cols-12">
                <section class="relative flex h-32 items-center bg-gray-900 lg:col-span-1 lg:h-full 4xl:col-span-3">
                    <img alt="Algo salió mal"
                         src="https://cdnwordpresstest-f0ekdgevcngegudb.z01.azurefd.net/es/wp-content/uploads/2022/03/20200813-Colmayor.jpg"
                         class="absolute inset-0 h-full w-full object-cover backdrop-opacity-50 opacity-60 "/>
                    <div class="hidden lg:relative lg:block lg:p-12">
                        <h2 class="mt-6 text-3xl font-bold text-white md:text-4xl lg:text-xs">
                            Seguimiento
                        </h2>
                    </div>
                </section>
                <div class="flex items-center justify-center px-0 py-8 lg:col-span-11 lg:py-4 4xl:col-span-9">
                    <div class="flex-none w-14"></div>
                    <div class="grow">
                        <main class="flex-grow items-center justify-center px-0 py-8 lg:col-span-9 lg:py-4 xl:col-span-9">
                            <div class="w-full lg:min-w-xl">
                                <div class="relative -mt-16 block lg:hidden">
                                    <a class="inline-flex size-16 items-center justify-center rounded-full bg-white text-blue-600 sm:size-20 dark:bg-gray-900"
                                       href="#">
                                        <span class="sr-only">Informe</span>
                                    </a>

                                    <h1 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl md:text-4xl dark:text-white">
                                        INSPECCIÓN AMBIENTAL Y DE SEGURIDAD Y SALUD EN EL TRABAJO
                                    </h1>
                                    <p class="mt-4 leading-relaxed text-gray-500 dark:text-gray-400">
                                        SS-FR-105
                                    </p>
                                </div>
                                <BackButton/>
                                <div class="mt-8 grid grid-cols-2 xl:grid-cols-3 gap-6">
                                    <div class="col-span-full w-full">
                                        <p>Fecha de realización {{ formatDateSimply(props.Inspeccion?.fecha_realizacion) }}</p>
                                    </div>
                                    <div class="w-full">
                                        <label for="FirstName"
                                               class="block text-xl my-2 font-bold text-gray-700 dark:text-gray-200">Áreas
                                            de
                                            inspección</label>
                                        <div class="mt-1">
                                            <p v-for="(aria,inde) in props.Inspeccion?.areas" :index="inde">
                                                {{ aria.nombre }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="w-full">
                                        <label for="FirstName"
                                               class="block text-xl my-2 font-bold text-gray-700 dark:text-gray-200">Responsables</label>
                                        <div class="mt-1">
                                            <p v-for="(aria,inde) in props.Inspeccion?.users" :index="inde">
                                                {{ aria.tipo_user }}: {{ aria.name }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="w-full mb-6">
                                        <label for="FirstName"
                                               class="block text-xl my-2 font-bold text-gray-700 dark:text-gray-200">
                                            Responsable de recibir
                                        </label>
                                        <div class="mt-1">
                                            <p>
                                                {{ props.Inspeccion?.userRecibir }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <AspectosSegui
                                    :categoriaAgrup="data.categoriaAgrup"
                                    :BoolRealizarSegui="1"
                                />
                                <!--                                    :historico_seguimientos="data.categoriaAgrup"-->
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
@media print {
    .no-print {
        display: none;
    }
}
</style>
