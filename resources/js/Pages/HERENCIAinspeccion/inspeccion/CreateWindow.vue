<script setup>
import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import {useForm} from '@inertiajs/vue3';
    import "vue-select/dist/vue-select.css";
    import {onMounted, reactive, ref,watch, watchEffect} from 'vue';
    import '@vuepic/vue-datepicker/dist/main.css'
    import vSelect from "vue-select";
    import "vue-select/dist/vue-select.css";
    import Toast from "@/Components/Toast.vue";
    import {TransformTdate} from "@/global";

// --------------------------- ** -------------------------

const props = defineProps({
    numberPermissions: Number,
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,
    UsersCopasst: Object,
})
// const emit = defineEmits(["close"]);

const data = reactive({
    params: {
        pregunta: ''
    },
    isCooldown: false,
    EsCopasst: false,
})

//very usefull
const justNames = props.titulos.map(names => names)
const form = useForm({
    ...Object.fromEntries(justNames.map(field => [field, ''])),
    areainspeccion: [],
    userRecibir: '',
    responsable_sst: '',
    responsable_a: '',

});
onMounted(() => {
    if (props.numberPermissions > 900) {
        // const valueRAn = Math.floor(Math.random() * (9) + 1)
        // form.nombre = 'nombre de prueba inspeccion ' + (valueRAn);
        // form.hora_inicial = '0'+valueRAn+':00'//temp

        let hoy = TransformTdate(new Date(), true)
        form.fecha_realizacion = hoy
        // form.fecha_realizacion = '2024-07-24'
        form.areainspeccion = ['Fablab']
        form.responsable_sst = 'Daniel Cuartas Arboleda'
        form.responsable_a = 'Diana Marcela Cardona Gomez'
        form.userRecibir = 'ejemplo Recibe: '+hoy
    }
});


const OnErroru = () => {
    const errors = form.errors;
    let errorMessage = '';

    for (let [field, message] of Object.entries(errors)) {
        if (field == 'userRecibir') field = 'Responsable de recibir'
        errorMessage += `${field}: ${message}\n`;
    }
    alert(errorMessage);
}
const cooldownDuration = 4000; // 23 segundos en milisegundos
const create = () => { //this is the most recent version of the function
    if (data.isCooldown) {
        console.log("Espera " + cooldownDuration / 1000 + " segundos antes de volver a llamar.");
        setTimeout(() => {
            data.isCooldown = false;
            console.log("Ahora puedes llamar a la función create de nuevo.");
        }, cooldownDuration);
    }
    data.isCooldown = true;

    form.post(route('inspeccion.store', [props.idInspeccion, form.contador]), {
        onSuccess: () => form.reset(),
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => {}
    })
}

watchEffect(() => {})

watch(() => form.responsable_sst, (responsableSst) => {
  let isCopasst = false
  props.UsersCopasst.forEach(element => {
      if(responsableSst === element){
        isCopasst = true
        form.responsable_a = ''
      }
    });
    data.EsCopasst = isCopasst
})


//very usefull
const sexos = [{label: 'Masculino', value: 0}, {label: 'Femenino', value: 1}];
</script>

<template>
  <Toast :flash="$page.props.flash" class="" />

  <section class="space-y-1">
    <section class="bg-white dark:bg-gray-900">
      <div class="lg:grid lg:min-h-screen lg:grid-cols-12">
        <section
          class="relative flex h-32 items-end bg-gray-900 lg:col-span-3 lg:h-full xl:col-span-3"
        >
          <img
            alt="Algo salió mal"
            src="https://cdnwordpresstest-f0ekdgevcngegudb.z01.azurefd.net/es/wp-content/uploads/2022/03/20200813-Colmayor.jpg"
            class="absolute inset-0 h-full w-full object-cover backdrop-opacity-50 opacity-60"
          />
          <div class="hidden lg:relative lg:block lg:p-12">
            <a class="block text-white" href="#">
              <span class="sr-only">Inicio</span>
              <svg
                class="h-8 sm:h-10"
                viewBox="0 0 28 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M0.41 10.3847C1.14777 7.4194 2.85643 4.7861 5.2639 2.90424C7.6714 1.02234 10.6393 0 13.695 0C16.7507 0 19.7186 1.02234 22.1261 2.90424C24.5336 4.7861 26.2422 7.4194 26.98 10.3847H25.78C23.7557 10.3549 21.7729 10.9599 20.11 12.1147C20.014 12.1842 19.9138 12.2477 19.81 12.3047H19.67C19.5662 12.2477 19.466 12.1842 19.37 12.1147C17.6924 10.9866 15.7166 10.3841 13.695 10.3841C11.6734 10.3841 9.6976 10.9866 8.02 12.1147C7.924 12.1842 7.8238 12.2477 7.72 12.3047H7.58C7.4762 12.2477 7.376 12.1842 7.28 12.1147C5.6171 10.9599 3.6343 10.3549 1.61 10.3847H0.41ZM23.62 16.6547C24.236 16.175 24.9995 15.924 25.78 15.9447H27.39V12.7347H25.78C24.4052 12.7181 23.0619 13.146 21.95 13.9547C21.3243 14.416 20.5674 14.6649 19.79 14.6649C19.0126 14.6649 18.2557 14.416 17.63 13.9547C16.4899 13.1611 15.1341 12.7356 13.745 12.7356C12.3559 12.7356 11.0001 13.1611 9.86 13.9547C9.2343 14.416 8.4774 14.6649 7.7 14.6649C6.9226 14.6649 6.1657 14.416 5.54 13.9547C4.4144 13.1356 3.0518 12.7072 1.66 12.7347H0V15.9447H1.61C2.39051 15.924 3.154 16.175 3.77 16.6547C4.908 17.4489 6.2623 17.8747 7.65 17.8747C9.0377 17.8747 10.392 17.4489 11.53 16.6547C12.1468 16.1765 12.9097 15.9257 13.69 15.9447C14.4708 15.9223 15.2348 16.1735 15.85 16.6547C16.9901 17.4484 18.3459 17.8738 19.735 17.8738C21.1241 17.8738 22.4799 17.4484 23.62 16.6547ZM23.62 22.3947C24.236 21.915 24.9995 21.664 25.78 21.6847H27.39V18.4747H25.78C24.4052 18.4581 23.0619 18.886 21.95 19.6947C21.3243 20.156 20.5674 20.4049 19.79 20.4049C19.0126 20.4049 18.2557 20.156 17.63 19.6947C16.4899 18.9011 15.1341 18.4757 13.745 18.4757C12.3559 18.4757 11.0001 18.9011 9.86 19.6947C9.2343 20.156 8.4774 20.4049 7.7 20.4049C6.9226 20.4049 6.1657 20.156 5.54 19.6947C4.4144 18.8757 3.0518 18.4472 1.66 18.4747H0V21.6847H1.61C2.39051 21.664 3.154 21.915 3.77 22.3947C4.908 23.1889 6.2623 23.6147 7.65 23.6147C9.0377 23.6147 10.392 23.1889 11.53 22.3947C12.1468 21.9165 12.9097 21.6657 13.69 21.6847C14.4708 21.6623 15.2348 21.9135 15.85 22.3947C16.9901 23.1884 18.3459 23.6138 19.735 23.6138C21.1241 23.6138 22.4799 23.1884 23.62 22.3947Z"
                  fill="currentColor"
                />
              </svg>
            </a>

            <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
              INSPECCIÓN AMBIENTAL Y DE SEGURIDAD Y SALUD EN EL TRABAJO
            </h2>
            <p class="mt-4 leading-relaxed text-white/90">SS-FR-105</p>
          </div>
        </section>
        <div
          class="flex items-center justify-center px-0 py-8 lg:col-span-9 lg:py-4 xl:col-span-9"
        >
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
                    <span class="sr-only">Inicio</span>
                    <svg
                      class="h-8 sm:h-10"
                      viewBox="0 0 28 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M0.41 10.3847C1.14777 7.4194 2.85643 4.7861 5.2639 2.90424C7.6714 1.02234 10.6393 0 13.695 0C16.7507 0 19.7186 1.02234 22.1261 2.90424C24.5336 4.7861 26.2422 7.4194 26.98 10.3847H25.78C23.7557 10.3549 21.7729 10.9599 20.11 12.1147C20.014 12.1842 19.9138 12.2477 19.81 12.3047H19.67C19.5662 12.2477 19.466 12.1842 19.37 12.1147C17.6924 10.9866 15.7166 10.3841 13.695 10.3841C11.6734 10.3841 9.6976 10.9866 8.02 12.1147C7.924 12.1842 7.8238 12.2477 7.72 12.3047H7.58C7.4762 12.2477 7.376 12.1842 7.28 12.1147C5.6171 10.9599 3.6343 10.3549 1.61 10.3847H0.41ZM23.62 16.6547C24.236 16.175 24.9995 15.924 25.78 15.9447H27.39V12.7347H25.78C24.4052 12.7181 23.0619 13.146 21.95 13.9547C21.3243 14.416 20.5674 14.6649 19.79 14.6649C19.0126 14.6649 18.2557 14.416 17.63 13.9547C16.4899 13.1611 15.1341 12.7356 13.745 12.7356C12.3559 12.7356 11.0001 13.1611 9.86 13.9547C9.2343 14.416 8.4774 14.6649 7.7 14.6649C6.9226 14.6649 6.1657 14.416 5.54 13.9547C4.4144 13.1356 3.0518 12.7072 1.66 12.7347H0V15.9447H1.61C2.39051 15.924 3.154 16.175 3.77 16.6547C4.908 17.4489 6.2623 17.8747 7.65 17.8747C9.0377 17.8747 10.392 17.4489 11.53 16.6547C12.1468 16.1765 12.9097 15.9257 13.69 15.9447C14.4708 15.9223 15.2348 16.1735 15.85 16.6547C16.9901 17.4484 18.3459 17.8738 19.735 17.8738C21.1241 17.8738 22.4799 17.4484 23.62 16.6547ZM23.62 22.3947C24.236 21.915 24.9995 21.664 25.78 21.6847H27.39V18.4747H25.78C24.4052 18.4581 23.0619 18.886 21.95 19.6947C21.3243 20.156 20.5674 20.4049 19.79 20.4049C19.0126 20.4049 18.2557 20.156 17.63 19.6947C16.4899 18.9011 15.1341 18.4757 13.745 18.4757C12.3559 18.4757 11.0001 18.9011 9.86 19.6947C9.2343 20.156 8.4774 20.4049 7.7 20.4049C6.9226 20.4049 6.1657 20.156 5.54 19.6947C4.4144 18.8757 3.0518 18.4472 1.66 18.4747H0V21.6847H1.61C2.39051 21.664 3.154 21.915 3.77 22.3947C4.908 23.1889 6.2623 23.6147 7.65 23.6147C9.0377 23.6147 10.392 23.1889 11.53 22.3947C12.1468 21.9165 12.9097 21.6657 13.69 21.6847C14.4708 21.6623 15.2348 21.9135 15.85 22.3947C16.9901 23.1884 18.3459 23.6138 19.735 23.6138C21.1241 23.6138 22.4799 23.1884 23.62 22.3947Z"
                        fill="currentColor"
                      />
                    </svg>
                  </a>

                  <h1
                    class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl md:text-4xl dark:text-white"
                  >
                    INSPECCIÓN AMBIENTAL Y DE SEGURIDAD Y SALUD EN EL TRABAJO
                  </h1>
                  <p class="mt-4 leading-relaxed text-gray-500 dark:text-gray-400">
                    SS-FR-105
                  </p>
                </div>

                <form
                  @submit.prevent="create"
                  class="mt-8 grid grid-cols-2 xl:grid-cols-3 gap-6"
                >
                  <div class="col-span-2 w-full">
                    <label
                      for="FirstName"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >
                      Área de inspección
                    </label>

                    <div id="areainspeccion" class="mt-1">
                      <v-select
                        multiple
                        v-model="form.areainspeccion"
                        :options="props.losSelect['areas']"
                      ></v-select>
                      <InputError class="mt-2" :message="form.errors.areainspeccion" />
                    </div>
                  </div>

                  <div class="col-span-2 lg:col-span-1">
                    <label
                      for="LastName"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >
                      Fecha realizacion
                    </label>
                    <input
                      type="date"
                      v-model="form.fecha_realizacion"
                      class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                    />
                  </div>

                  <div class="col-span-2 lg:col-span-1">
                    <label v-if="data.EsCopasst" for="respo1" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                      Responsable inspección Copasst
                    </label>
                    <label v-else for="respo1" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                      Responsable inspección SG-SST
                    </label>
                    <div id="areainspeccion" class="mt-1">
                      <v-select v-model="form.responsable_sst"
                        :options="props.losSelect['responsablesSST']"
                      ></v-select>
                      <InputError class="mt-2" :message="form.errors.responsable_sst" />
                    </div>
                  </div>
                  <div v-if="!data.EsCopasst" class="col-span-2 lg:col-span-1">
                    <label
                      for="respo2"
                      class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >
                      <p class="">Responsable inspección SG-A</p>
                    </label>
                    <div id="areainspeccion" class="mt-1">
                      <v-select
                        v-model="form.responsable_a"
                        :options="props.losSelect['responsablesSGA']"
                      ></v-select>
                      <InputError class="mt-2" :message="form.errors.responsable_a" />
                    </div>
                  </div>
                  <div class="">
                    <label
                      class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                      >Responsable de recibir la inspección</label
                    >
                    <TextInput
                      id="userRecibir"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.userRecibir"
                      required
                      :error="form.errors.userRecibir"
                    />
                  </div>

                  <div
                    class="col-span-full xl:col-span-3 sm:items-center sm:gap-4 mt-2 mb-16 xl:mb-6"
                  >
                    <button
                      type="submit"
                      class="inline-block shrink-0 rounded-md px-12 py-3 text-sm font-medium text-white border border-blue-600 bg-blue-600 hover:text-blue-600 focus:outline-none active:text-red-600 dark:hover:bg-blue-700 dark:hover:text-white transition hover:bg-transparent"
                    >
                      {{ form.processing ? lang().button.wait : lang().button.continue }}
                    </button>
                    <!--                                        <p class="mt-4 text-sm text-gray-500 sm:mt-0 dark:text-gray-400">-->
                    <!--                                            Detalles adicionales...-->
                    <!--                                        </p>-->
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
