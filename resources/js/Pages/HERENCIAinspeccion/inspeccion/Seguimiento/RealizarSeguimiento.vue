<template>
    <div class="w-full px-1 pt-2">
        <div class="mx-auto w-full rounded-2xl bg-white p-2">
            <Disclosure v-slot="{ open }">
                <DisclosureButton
                    class="flex w-full justify-between rounded-lg bg-sky-100 px-4 py-2 text-left text-sm font-medium text-sky-900 hover:bg-sky-200 focus:outline-none focus-visible:ring focus-visible:ring-sky-500/75">
                    <span>Nueva calificación</span>
                    <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-sky-500"/>
                </DisclosureButton>
                <DisclosurePanel class="px-4 pb-2 pt-4 text-sm text-gray-500  mx-auto text-center">


                    <div id="areainspeccion" class="mt-1">
                        <SeleccionCumple v-model:valor="form.calificacion"/>
                    </div>
                    <textarea id="observaciones" row="4" cols="30" v-model="form.observaciones" autofocus
                              placeholder="Observaciones"
                              class="mt-4 block w-full overflow-y-scroll"/>

                    <div id="foto" class="mt-2  mx-auto text-center">
                        <InstantVideo @photoCaptured="photoCapturedHandler($event)"/>
                    </div>


                    <button @click="GuardarSeguimiento"
                            class=" my-4 mx-auto inline-block shrink-0 rounded-md border border-blue-600 bg-blue-600
                             px-8 py-2 text-sm font-medium text-white transition
                             hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500 dark:hover:bg-blue-700 dark:hover:text-white">
                        {{ form.processing ? 'Espere' + '...' : 'Continuar' }}
                    </button>
                </DisclosurePanel>
            </Disclosure>
        </div>
    </div>
</template>
<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {ChevronUpIcon} from '@heroicons/vue/20/solid'
import {useForm} from "@inertiajs/vue3";

import SeleccionCumple from "@/Pages/inspeccion/SeleccionCumple.vue";
import InstantVideo from "@/Pages/inspeccion/InstantVideo.vue";

let props = defineProps({
    aspec: Object,
    historico_seguimientos: Object,
})
const photoCapturedHandler = (photoData) => {
    // data.caturedphoto = true
    form.image = photoData;
};
const form = useForm({
    calificacion: '',
    observaciones: '',
    image: '',
    abierto: 1,
    aspecto_inspeccion_id: '',
});

/*
todo: hay que validar que calificacion observaciones sean obligatorias
 */

const GuardarSeguimiento = () => {
    // form.calificacion = props.aspec.calificacion
    // form.image = props.aspec.image
    // form.observaciones = props.aspec.observaciones
    form.aspecto_inspeccion_id = props.aspec.aspecto_inspeccion_id

    form.post(route('inspeccion.GuardarSeguimiento', props.aspec.id), {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => form.reset(),
        // onSuccess: () => alert('Aspecto Guardado'),
        onError: () => null,
        onFinish: () =>{
            form.reset()
            window.location.reload()
        }
    })
}
</script>
