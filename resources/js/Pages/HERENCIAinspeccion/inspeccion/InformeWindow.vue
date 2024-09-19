<script setup>
import { useForm } from "@inertiajs/vue3";
import "vue-select/dist/vue-select.css";
import { computed, onMounted, reactive, ref, watch, watchEffect } from "vue";
import "@vuepic/vue-datepicker/dist/main.css";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import Toast from "@/Components/Toast.vue";
import { formatDate, formatDateSimply } from "@/global";
import BackButton from "@/Components/BackButton.vue";
import FirmarButton from "@/Components/FirmarButton.vue";

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
  TieneResultadoVacio: Boolean,
  TieneAlmenosFirma: Number,
  TieneFirmaGuardada: Boolean,
  nombresFirmas: Array,
});
// const emit = defineEmits(["close"]);

const data = reactive({
  params: {
    pregunta: "",
  },
  categoriaAgrup: {},
});

//very usefull
const justNames = props.titulos.map((names) => names);
const form = useForm({
  ...Object.fromEntries(justNames.map((field) => [field, ""])),
  areainspeccion: [],
  userRecibir: "",
  responsable_sst: "",
  responsable_a: "",
});
onMounted(() => {});

const create = () => {
  form.post(route("inspeccion.store"), {
    onSuccess: () => form.reset(),
    onError: () => null,
    onFinish: () => null,
  });
};

watchEffect(() => {
  if (props.show) {
    form.errors = {};
  }
});

props.Inspeccion.aspectoos.forEach((equix) => {
  if (!data.categoriaAgrup.hasOwnProperty(equix.categoria)) {
    data.categoriaAgrup[equix.categoria] = {
      aspectos: [],
    };
  }
  //Agregamos los datos de aspectos.
  if (equix.pivot.calificacion !== "NO APLICA") {
    const seguimientosCorrespondientes = props.Inspeccion.seguimientos.filter(
      (seguimiento) => {
        return seguimiento.aspecto_inspeccion_id === equix.pivot.id;
      }
    );

    data.categoriaAgrup[equix.categoria].aspectos.push({
      id: equix.id,
      aspectoNombre: equix.nombre,
      calificacion: equix.pivot.calificacion,
      registrofotografico: equix.pivot.registrofotografico,
      observaciones: equix.pivot.observaciones,
      Resultado: equix.pivot.Resultado,
      MejoraSugerida: equix.pivot.MejoraSugerida,
      ResponsableGestionarMejora: equix.pivot.ResponsableGestionarMejora,
      FechaCompromiso: equix.pivot.FechaCompromiso,
      abierto: equix.pivot.abierto,
      aspecto_inspeccion_id: equix.pivot.id,
      seguimientos: seguimientosCorrespondientes,
    });
  }
});
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
};
// }
// });
</script>

<template>
  <Toast :flash="$page.props.flash" class="" />

  <section class="space-y-1">
    <section class="bg-white dark:bg-gray-900">
      <div class="lg:grid lg:min-h-screen lg:grid-cols-1">
        <!--                <section class="relative flex h-32 items-end bg-gray-900 lg:col-span-3 lg:h-full xl:col-span-3">-->
        <!--                    <img alt="Algo salió mal"-->
        <!--                         src="https://cdnwordpresstest-f0ekdgevcngegudb.z01.azurefd.net/es/wp-content/uploads/2022/03/20200813-Colmayor.jpg"-->
        <!--                         class=" no-print absolute inset-0 h-full w-full object-cover backdrop-opacity-50 opacity-60 "/>-->
        <!--                    <div class="hidden lg:relative lg:block lg:p-12">-->
        <!--                        <a class="block text-white" href="#">-->
        <!--                            <span class="sr-only">Informe</span>-->
        <!--                        </a>-->

        <!--                        <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">-->
        <!--                            INSPECCIÓN AMBIENTAL Y DE SEGURIDAD Y SALUD EN EL TRABAJO-->
        <!--                        </h2>-->
        <!--                        <p class="mt-4 leading-relaxed text-white/90">-->
        <!--                            SS-FR-105-->
        <!--                        </p>-->
        <!--                    </div>-->
        <!--                </section>-->
        <div class="flex items-center justify-center px-0 py-8">
          <div class="flex-none w-14"></div>
          <div class="grow">
            <main
              class="flex-grow items-center justify-center px-0 py-8 lg:col-span-9 lg:py-4 xl:col-span-9"
            >
              <div class="w-full lg:min-w-xl">
                <div class="relative -mt-16 block lg:hidden">
                  <a
                    class="inline-flex size-16 items-center justify-center rounded-full bg-white text-blue-600 sm:size-20 dark:bg-gray-900"
                    href="#"
                  >
                    <span class="sr-only">Informe</span>
                  </a>
                  <div class="col-span-full text-sm w-full">
                    <p>
                      Realización
                      {{ formatDateSimply(props.Inspeccion.fecha_realizacion) }}
                    </p>
                  </div>

                  <h1
                    class="mt-2 text-2xl font-bold text-gray-900 md:text-3xl dark:text-white"
                  >
                    INSPECCIÓN AMBIENTAL Y DE SEGURIDAD Y SALUD EN EL TRABAJO
                  </h1>
                  <!--                                    <p class="mt-4 leading-relaxed text-gray-500 dark:text-gray-400">-->
                  <!--                                        SS-FR-105-->
                  <!--                                    </p>-->
                </div>
                <div class="text-center inline-flex gap-8">
                  <div class="flex text-center mx-auto my-6 no-print">
                    <button
                      onclick="window.print()"
                      class="no-print px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-200"
                    >
                      Imprimir
                    </button>
                  </div>

                  <div class="flex text-center mx-auto my-6 no-print">
                    <BackButton />
                  </div>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-8">
                  <!-- TieneResultadoVacio: hace referencia a que el informe necesita resultados -->
                  <div
                    v-if="props.TieneResultadoVacio"
                    class="col-span-full w-full text-red-600 text-center text-xl"
                  >
                    <p>El informe actual, aun tiene resultados sin completar</p>
                  </div>
                  <div class="col-span-full w-full no-print">
                    <p>
                      Fecha de realización
                      {{ formatDateSimply(props.Inspeccion.fecha_realizacion) }}
                    </p>
                  </div>
                  <div class="w-full col-span-full grid grid-cols-2">
                    <label
                      for="FirstName"
                      class="block text-xl my-2 font-bold text-gray-700 dark:text-gray-200 col-span-full"
                    >
                      Áreas de inspección
                    </label>
                    <div class="mt-1">
                      <p
                        v-for="(aria, inde) in props.Inspeccion.areas"
                        :index="inde"
                        class="text-sm"
                      >
                        {{ aria.nombre }}
                      </p>
                    </div>
                  </div>
                  <div class="w-full border-r-2 border-gray-500">
                    <label
                      for="FirstName"
                      class="block text-sm sm:text-xl my-2 font-bold text-gray-700 dark:text-gray-200"
                    >
                      Responsables
                    </label>
                    <div class="mt-1">
                      <p v-for="(aria, inde) in props.Inspeccion.users" :index="inde">
                        {{ aria.tipo_user }}: {{ aria.name }} -
                        {{ aria.cargo ? aria.cargo : "Sin cargo!" }}
                      </p>
                    </div>
                  </div>
                  <div class="w-full mb-6">
                    <label
                      for="FirstName"
                      class="block text-sm sm:text-xl my-2 font-bold text-gray-700 dark:text-gray-200"
                    >
                      Responsable de recibir
                    </label>
                    <div class="mt-1">
                      <p>{{ props.Inspeccion.userRecibir }}</p>
                    </div>
                  </div>
                </div>
                  
                  
                <div
                  v-if="props.TieneFirmaGuardada"
                  class="flex text-center mx-auto my-6 no-print"
                >
                  <FirmarButton
                    v-if="!props.TieneAlmenosFirma"
                    :inspeccionId="Inspeccion.id"
                  />
                </div>
                <div v-else class="flex text-center mx-auto my-6 no-print text-red-500">
                  <p class="text-center mx-auto">
                    Usted aun no tiene una firma guardada!
                  </p>
                </div>
                  
                  
                <div v-if="props.nombresFirmas.length" class="w-full mx-auto text-center my-8 no-print">
                  <label
                    class="block text-sm sm:text-xl my-2 font-bold text-gray-700 dark:text-gray-200"
                  >
                    Firmas
                  </label>
                </div>
                <div v-if="props.nombresFirmas.length" class="flex flex-wrap -mx-4 -my-8 no-print">
                  <div v-for="(firmado, indice) in props.nombresFirmas" class="py-8 px-4">
                    <div class="flex">
                      <div class="">
                        <div class="w-32 flex-shrink-0 flex flex-col text-center leading-none" >
                          <span class="text-gray-500 pb-2 mb-2" >{{ formatDateSimply(firmado.created, true) }}</span>
                        </div>
                        <div class="flex-grow pl-6">
                          <h1 class="title-font text-sm font-medium text-gray-900 mb-3">
                            {{ firmado.nombre }}
                          </h1>
                          <!-- <h2 class="tracking-widest text-xs title-font font-medium text-indigo-500 mb-1" > Ha firmado </h2> -->
                          <div>
                            <img
                              :src="firmado.firma"
                              alt="Captured Photo"
                              class="w-full lg:max-w-[300px] h-auto border border-b-black mb-2 rounded-xl mx-auto text-center border-b-2 border-gray-300"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <AspectosSegui
                  :categoriaAgrup="data.categoriaAgrup"
                  :BoolRealizarSegui="0"
                />

                <div class="flex flex-wrap -mx-4 -my-8">
                  <div v-for="(firmado, indice) in props.nombresFirmas" class="py-8 px-4">
                    <div class="flex">
                      <div class="">
                        <div class="w-32 flex-shrink-0 flex flex-col text-center leading-none" >
                          <span class="text-gray-500 pb-2 mb-2" >{{ formatDateSimply(firmado.created, true) }}</span>
                        </div>
                        <div class="flex-grow pl-6">
                          <h1 class="title-font text-sm font-medium text-gray-900 mb-3">
                            {{ firmado.nombre }}
                          </h1>
                          <!-- <h2 class="tracking-widest text-xs title-font font-medium text-indigo-500 mb-1" > Ha firmado </h2> -->
                          <div>
                            <img
                              :src="firmado.firma"
                              alt="Captured Photo"
                              class="w-1/3 lg:max-w-[220px] h-auto border border-b-black mb-2 rounded-xl mx-auto text-center border-b-2 border-gray-300"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex text-center mx-auto mt-16 mb-6 no-print">
                  <BackButton />
                </div>
              </div>
            </main>
          </div>
          <div class="flex-none w-14"></div>
        </div>
        <div
          @click="scrollToTop()"
          class="fixed bottom-10 right-10 bg-blue-500 py-2 px-4 rounded text-white font-bold cursor-pointer hover:bg-blue-700 no-print"
        >
          Volver arriba
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
