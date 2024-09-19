<script setup>
import {useForm} from '@inertiajs/vue3';
import "vue-select/dist/vue-select.css";
import {onMounted, reactive, ref, watch, watchEffect} from 'vue';
import '@vuepic/vue-datepicker/dist/main.css'
import Toast from "@/Components/Toast.vue";

import SeleccionCumple from "@/Pages/inspeccion/SeleccionCumple.vue";
import ObservacionYFoto from "@/Pages/inspeccion/ObservacionYFoto.vue";
// --------------------------- ** -------------------------

// <!--<editor-fold desc="go to create">-->
let props = defineProps({
    numberPermissions: Number,
    idInspeccion: Number,
    contador: Number,
    title: String,
    breadcrumbs: Object, //parametros de la clase principal
    titulos: Object, //parametros de la clase principal
    AspectosEnBD: Object,
    category: String,
})

const data = reactive({
    params: {
        pregunta: ''
    },
    caturedphoto: [],
    photoActivated: [],
    isCooldown: false,
    ofotos: [], //para solo obtener los valores necesarios
    NoArrayNoVacios: [], //para validar los elementos no array
    MostrarSimple: [],
})

//very usefull //
/*
0: "Los_pisos_las_ventanas_muros_y_techos_se_encuentran_en_buen_estado"
1 :"La_escaleras_se_encuentra_en_buen_estadopeldaños_con_cinta_antideslizante_huellas_y_pasamanos"
*/

const justAspectos = props.AspectosEnBD.map(aspecto => {
    let aspec = aspecto.nombre.replace(/,/g, ' ')
    aspec = aspec.replace(/[()]/g, '');
    return aspec.replace(/\s+/g, '_')
})
let AspectosID = props.AspectosEnBD.map(aspecto => {
    return aspecto.id
})


const form = useForm({
    ...Object.fromEntries(AspectosID.map((field) => [field, ''])),
    observaciones: [''],
    image: [],
    contador: props.contador,
});

onMounted(() => {
    for (let i = 2; i < form.length; i++) {
        if (typeof form[i] === 'number') { // Verifica si el valor es numérico
            NoArrayNoVacios.push(form[i]);
        }
    }

    if (props.numberPermissions > 900) {
    }
    justAspectos.forEach((element, inde) => {
        form.observaciones[inde] = ''
        form.image[inde] = ''
        data.photoActivated[inde] = false
        data.caturedphoto[inde] = ''
    });
    AspectosID.forEach(element => {
        return form[element] = 'NO APLICA'
    });
    form.contador = props.contador
});


// , form.observaciones, form.image]
//todo: validar que las observaciones que se necesitan, sean obligatorias

//TODO: validar despues, la idea principal, es que se valide las observaciones en caso se activen. Pero si cumple o no, eso no se valida
let ArrayshasEmptyFields = () => data.NoArrayNoVacios.some((value, index) => {
    let banderaFinal = false
    if (!Array.isArray(value)) {
        //   value.forEach(element => {
        //     console.log("=>(CreateWindow2.vue:91) element", element);
        //     if (element === '') {
        //       banderaFinal = true
        //       return null
        //     }
        //   });
        //   if (banderaFinal) return true
        // } else {
        return (value === '')
    }
});

// <!--</editor-fold>-->
const files = ref([]);
function handleFileUpload(event, index) {
    const file = event.target.files[0];
    if (file) {
        form.image[index] = file;
    }
}

const photoCapturedHandler = ({photoData, aspecid}) => {
    data.caturedphoto = true
    form.image[aspecid] = photoData;
};

watchEffect(() => {})

watch(() => data.ofotos, (newValue) => {
    data.ofotos = newValue.filter(item => item !== null);
}, {deep: true});

let aspectosEnBDObjeto = props.AspectosEnBD.reduce((acc, aspecto) => {
    acc[aspecto.id] = aspecto;
    return acc;
}, {});
Object.keys(aspectosEnBDObjeto).forEach((key, index) => {
    data.MostrarSimple[index] = false;
});

const cooldownDuration = 3000;
const create = () => {
    if (data.isCooldown) console.log("Espera " + cooldownDuration / 1000 + " segundos antes de volver a llamar.");
    data.isCooldown = true;

    //todo: no es solo con form[1]
    data.NoArrayNoVacios = [form[1], form.observaciones, form.image]
    if (ArrayshasEmptyFields()) {
        alert('Faltan datos')
    } else {
        setTimeout(() => {
            data.isCooldown = false;
            console.log("Ahora puedes llamar a la función creat de nuevo.");
        }, cooldownDuration);
        // form.image = files.value.filter(Boolean)

        form.post(route('createPasoContadorP1', [props.idInspeccion, form.contador]), {
            onSuccess: () => {},
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => {}
        })
    }
}
const FinishInspeccion = () => {
    form.post(route('FinishInspeccion', [props.idInspeccion, form.contador]), {
        onSuccess: () => {
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => {
        }
    })
}
</script>
<template>
    <Toast :flash="$page.props.flash" class=""/>
    <section class="space-y-1">
        <section class="bg-white dark:bg-gray-900">
            <div class="lg:grid lg:min-h-screen lg:grid-cols-12">
                <section class="relative flex h-32 items-end bg-gray-900 lg:col-span-2 lg:h-full">
                    <img alt="Algo salió mal"
                         src="https://cdnwordpresstest-f0ekdgevcngegudb.z01.azurefd.net/es/wp-content/uploads/2022/03/20200813-Colmayor.jpg"
                         class="absolute inset-0 h-full w-full object-cover backdrop-opacity-10 opacity-60 max-h-screen"/>
                    <div class="hidden lg:relative lg:block lg:p-6">
                        <a class="block text-white" href="#">
                            <span class="sr-only">Inicio</span>
                            <svg class="h-8 sm:h-10" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0.41 10.3847C1.14777 7.4194 2.85643 4.7861 5.2639 2.90424C7.6714 1.02234 10.6393 0 13.695 0C16.7507 0 19.7186 1.02234 22.1261 2.90424C24.5336 4.7861 26.2422 7.4194 26.98 10.3847H25.78C23.7557 10.3549 21.7729 10.9599 20.11 12.1147C20.014 12.1842 19.9138 12.2477 19.81 12.3047H19.67C19.5662 12.2477 19.466 12.1842 19.37 12.1147C17.6924 10.9866 15.7166 10.3841 13.695 10.3841C11.6734 10.3841 9.6976 10.9866 8.02 12.1147C7.924 12.1842 7.8238 12.2477 7.72 12.3047H7.58C7.4762 12.2477 7.376 12.1842 7.28 12.1147C5.6171 10.9599 3.6343 10.3549 1.61 10.3847H0.41ZM23.62 16.6547C24.236 16.175 24.9995 15.924 25.78 15.9447H27.39V12.7347H25.78C24.4052 12.7181 23.0619 13.146 21.95 13.9547C21.3243 14.416 20.5674 14.6649 19.79 14.6649C19.0126 14.6649 18.2557 14.416 17.63 13.9547C16.4899 13.1611 15.1341 12.7356 13.745 12.7356C12.3559 12.7356 11.0001 13.1611 9.86 13.9547C9.2343 14.416 8.4774 14.6649 7.7 14.6649C6.9226 14.6649 6.1657 14.416 5.54 13.9547C4.4144 13.1356 3.0518 12.7072 1.66 12.7347H0V15.9447H1.61C2.39051 15.924 3.154 16.175 3.77 16.6547C4.908 17.4489 6.2623 17.8747 7.65 17.8747C9.0377 17.8747 10.392 17.4489 11.53 16.6547C12.1468 16.1765 12.9097 15.9257 13.69 15.9447C14.4708 15.9223 15.2348 16.1735 15.85 16.6547C16.9901 17.4484 18.3459 17.8738 19.735 17.8738C21.1241 17.8738 22.4799 17.4484 23.62 16.6547ZM23.62 22.3947C24.236 21.915 24.9995 21.664 25.78 21.6847H27.39V18.4747H25.78C24.4052 18.4581 23.0619 18.886 21.95 19.6947C21.3243 20.156 20.5674 20.4049 19.79 20.4049C19.0126 20.4049 18.2557 20.156 17.63 19.6947C16.4899 18.9011 15.1341 18.4757 13.745 18.4757C12.3559 18.4757 11.0001 18.9011 9.86 19.6947C9.2343 20.156 8.4774 20.4049 7.7 20.4049C6.9226 20.4049 6.1657 20.156 5.54 19.6947C4.4144 18.8757 3.0518 18.4472 1.66 18.4747H0V21.6847H1.61C2.39051 21.664 3.154 21.915 3.77 22.3947C4.908 23.1889 6.2623 23.6147 7.65 23.6147C9.0377 23.6147 10.392 23.1889 11.53 22.3947C12.1468 21.9165 12.9097 21.6657 13.69 21.6847C14.4708 21.6623 15.2348 21.9135 15.85 22.3947C16.9901 23.1884 18.3459 23.6138 19.735 23.6138C21.1241 23.6138 22.4799 23.1884 23.62 22.3947Z"
                                    fill="currentColor"/>
                            </svg>
                        </a>

                        <h2 class="mt-6 text-2xl font-bold text-white sm:text-2xl lg:text-sm">
                            INSPECCIÓN AMBIENTAL Y DE SEGURIDAD Y SALUD EN EL TRABAJO
                        </h2>

                        <p class="mt-4 leading-relaxed text-white/90">
                            SS-FR-105
                        </p>
                    </div>
                </section>
                <div class="flex items-center justify-center px-0 py-8 lg:col-span-10 lg:py-4">
                    <div class="flex-none w-14"></div>
                    <div class="grow">
                        <main class="flex-grow items-center justify-center px-0 py-8 lg:col-span-10 lg:py-6">
                            <div class="w-full lg:min-w-xl">
                                <div class="relative -mt-12 block lg:hidden">
                                    <a class="inline-flex size-16 items-center justify-center rounded-full bg-white text-blue-600 sm:size-20 dark:bg-gray-900"
                                       href="#">
                                        <span class="sr-only"></span>
                                        <svg class="h-8 sm:h-10" viewBox="0 0 28 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M0.41 10.3847C1.14777 7.4194 2.85643 4.7861 5.2639 2.90424C7.6714 1.02234 10.6393 0 13.695 0C16.7507 0 19.7186 1.02234 22.1261 2.90424C24.5336 4.7861 26.2422 7.4194 26.98 10.3847H25.78C23.7557 10.3549 21.7729 10.9599 20.11 12.1147C20.014 12.1842 19.9138 12.2477 19.81 12.3047H19.67C19.5662 12.2477 19.466 12.1842 19.37 12.1147C17.6924 10.9866 15.7166 10.3841 13.695 10.3841C11.6734 10.3841 9.6976 10.9866 8.02 12.1147C7.924 12.1842 7.8238 12.2477 7.72 12.3047H7.58C7.4762 12.2477 7.376 12.1842 7.28 12.1147C5.6171 10.9599 3.6343 10.3549 1.61 10.3847H0.41ZM23.62 16.6547C24.236 16.175 24.9995 15.924 25.78 15.9447H27.39V12.7347H25.78C24.4052 12.7181 23.0619 13.146 21.95 13.9547C21.3243 14.416 20.5674 14.6649 19.79 14.6649C19.0126 14.6649 18.2557 14.416 17.63 13.9547C16.4899 13.1611 15.1341 12.7356 13.745 12.7356C12.3559 12.7356 11.0001 13.1611 9.86 13.9547C9.2343 14.416 8.4774 14.6649 7.7 14.6649C6.9226 14.6649 6.1657 14.416 5.54 13.9547C4.4144 13.1356 3.0518 12.7072 1.66 12.7347H0V15.9447H1.61C2.39051 15.924 3.154 16.175 3.77 16.6547C4.908 17.4489 6.2623 17.8747 7.65 17.8747C9.0377 17.8747 10.392 17.4489 11.53 16.6547C12.1468 16.1765 12.9097 15.9257 13.69 15.9447C14.4708 15.9223 15.2348 16.1735 15.85 16.6547C16.9901 17.4484 18.3459 17.8738 19.735 17.8738C21.1241 17.8738 22.4799 17.4484 23.62 16.6547ZM23.62 22.3947C24.236 21.915 24.9995 21.664 25.78 21.6847H27.39V18.4747H25.78C24.4052 18.4581 23.0619 18.886 21.95 19.6947C21.3243 20.156 20.5674 20.4049 19.79 20.4049C19.0126 20.4049 18.2557 20.156 17.63 19.6947C16.4899 18.9011 15.1341 18.4757 13.745 18.4757C12.3559 18.4757 11.0001 18.9011 9.86 19.6947C9.2343 20.156 8.4774 20.4049 7.7 20.4049C6.9226 20.4049 6.1657 20.156 5.54 19.6947C4.4144 18.8757 3.0518 18.4472 1.66 18.4747H0V21.6847H1.61C2.39051 21.664 3.154 21.915 3.77 22.3947C4.908 23.1889 6.2623 23.6147 7.65 23.6147C9.0377 23.6147 10.392 23.1889 11.53 22.3947C12.1468 21.9165 12.9097 21.6657 13.69 21.6847C14.4708 21.6623 15.2348 21.9135 15.85 22.3947C16.9901 23.1884 18.3459 23.6138 19.735 23.6138C21.1241 23.6138 22.4799 23.1884 23.62 22.3947Z"
                                                fill="currentColor"/>
                                        </svg>
                                    </a>

                                    <h1 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl md:text-sm dark:text-white">
                                        INSPECCIÓN AMBIENTAL Y DE SEGURIDAD Y SALUD EN EL TRABAJO
                                    </h1>
                                    <p class="mt-4 leading-relaxed text-gray-500 dark:text-gray-400">
                                        SS-FR-105
                                    </p>
                                </div>


                                <form @submit.prevent="create" enctype="multipart/form-data"
                                      class="-mt-2 md:mt-10 grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-6">
                                    <div class="md:col-span-2 2xl:col-span-3 w-full">
                                        <h1 class="mt-4 leading-relaxed text-gray-800 dark:text-gray-400 text-center mx-auto text-3xl capitalize font-bold">
                                            {{ props.category.toLowerCase() }}
                                        </h1>
                                    </div>
                                    <div v-for="(aspec,indicep) in aspectosEnBDObjeto" :key="indicep"
                                         class="col-span-1 w-full">

                                        <label for=""
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                            {{ aspec.nombre }}
                                        </label>
                                        <div id="areainspeccion" class="mt-1">
                                            <SeleccionCumple v-model:valor="form[aspec.id]"/>
                                        </div>
                                        <!--                        @click="ActivarDanger(aspec.id)" -->
                                        <div @click="data.MostrarSimple[indicep] = !data.MostrarSimple[indicep]"
                                             class="m-1 cursor-pointer">
                                            <p class="underline text-blue-700">
                                                Tomar Foto {{ data.photoActivated[aspec.id] ? ' ✅ ' : '' }}
                                            </p>
                                        </div>

                                        <ObservacionYFoto v-if="data.MostrarSimple[indicep]"
                                                          @photoCapturedH="photoCapturedHandler"
                                                          :aspec="aspec"
                                                          :form="form"
                                        />

<!--                                        <label :for="'file-' + aspec.id" class="cursor-pointer inline-block bg-blue-500 text-white px-4 py-2 rounded">-->
<!--                                            Seleccionar archivo-->
<!--                                        </label>-->
<!--                                        <input-->
<!--                                            :id="'file-' + aspec.id"-->
<!--                                            ref="file"-->
<!--                                            type="file"-->
<!--                                            accept="image/*"-->
<!--                                            @change="handleFileUpload($event, aspec.id)"-->
<!--                                            class="hidden"-->
<!--                                        />-->
<!--                                        <p v-if="files[aspec.id]">{{ files[aspec.id].name }}</p>-->
                                    </div>
                                    <div
                                        class="mt-4 col-span-1 md:col-span-2 2xl:col-span-3 sm:flex sm:items-center sm:gap-4">
                                        <button type="submit"
                                                class="inline-block shrink-0 rounded-md border border-blue-600 bg-blue-600 px-12 py-3 text-sm font-medium text-white transition hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500 dark:hover:bg-blue-700 dark:hover:text-white">
                                            {{ form.processing ? 'Espere' + '...' : 'Continuar' }}
                                        </button>
                                    </div>
                                    <!--                  <div-->
                                    <!--                      v-for="(aspec,indeFilters) in data.ofotos"-->
                                    <!--                      :key="indeFilters"-->
                                    <!--                      class="col-span-1 md:col-span-2 xl:col-span-2 2xl:col-span-1 w-full">-->
                                    <!--                  </div>-->


                                    <div class="md:col-span-2 2xl:col-span-3 my-6"></div>

                                    <div v-if="data.photoActivated.some(element => element === true)"
                                         class="md:col-span-2 2xl:col-span-3 sm:flex sm:items-center sm:gap-4 mx-auto">
                                        <button type="submit"
                                                class="inline-block shrink-0 rounded-md border border-blue-600 bg-blue-600
                                                 px-12 py-3 text-sm font-medium text-white transition
                                                 hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500 dark:hover:bg-blue-700 dark:hover:text-white">
                                            {{ form.processing ? 'Espere' + '...' : 'Continuar' }}
                                        </button>
                                    </div>
                                </form>
                                <div class="col-span-full flex items-center my-4 text-center mx-auto">
                                    <a @click.once="FinishInspeccion"
                                       class="inline-block shrink-0 rounded-md border border-blue-600 bg-blue-800 px-12 py-3 text-sm font-medium text-white transition
                                             hover:bg-transparent hover:text-red-500 mx-auto
                                             dark:hover:bg-red-700 dark:hover:text-white">
                                        {{ form.processing ? 'Finalizando' + '...' : 'Finalizar inspección' }}
                                    </a>
                                </div>
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
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}
[name="labelSelectVue"] {
    font-weight: 600;
}
</style>
