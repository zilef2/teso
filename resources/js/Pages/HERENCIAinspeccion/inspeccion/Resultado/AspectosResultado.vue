<template>
    <div v-for="(categoria,index) in categoriaAgrup" :key="index"
         class="mt-8 grid grid-cols-1 md:grid-cols-2 4xl:grid-cols-3 gap-12 border-t-2 border-t-cyan-600">
        <div v-if="categoriaAgrup[index].aspectos.length" class="w-full col-span-full text-center my-1">
            <h2 class="text-lg font-bold">
                {{ index }}
            </h2>
        </div>
        <div v-for="(aspec,inde) in categoria.aspectos" :key="inde" class="p-4 w-full">
            <div class="border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                <img v-if="aspec.registrofotografico"
                     :src="getImageUrl(aspec)" alt="Foto Dañada"
                     class="mx-auto max-h-60 lg:max-h-72 xl:max-h-full xl:h-full w-auto object-cover object-center hover:border-4 hover:border-amber-300">

                <div class="p-6">
                    <div class="my-2">
                        <label class="font-bold block text-sm text-gray-700 dark:text-gray-200">Aspecto</label>
                        <p>{{ aspec.aspectoNombre }}</p>
                    </div>
                    <div class="my-4">
                        <label class="font-bold block text-sm text-gray-700 dark:text-gray-200">Observaciones</label>
                        <p>{{ aspec.observaciones }}</p>
                    </div>
                    <div class="my-2">
                        <label class="font-bold block text-sm text-gray-700 dark:text-gray-200">Calificación</label>
                        <p>{{ aspec.calificacion }}</p>
                    </div>
                    <div class="my-2">
                        <label v-if="aspec.seguimientos.length" class="font-bold block text-sm text-gray-700 dark:text-gray-200">Se ha realizado seguimiento</label>
                    </div>
                    <div class="">
                        <label for="LastName" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Fecha Compromiso
                        </label>
                        <input
                            type="date"
                            v-model="form.FechaCompromiso[aspec.id]"
                            class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                        />
                        <InputError class="mt-2" :message="form.errors.FechaCompromiso"/>
                    </div>
                    
                    
<!--                    <div class="">-->
<!--                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Resultado</label>-->
<!--                        <textarea id="Resultado" rows="2"-->
<!--                                  cols="30"-->
<!--                                  v-model="form.Resultado[aspec.id]"-->
<!--                                  autofocus-->
<!--                                  placeholder="Resultado"-->
<!--                                  class="mt-1 block w-full overflow-y-scroll"/>-->
<!--                    </div>-->
                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Mejora Sugerida</label>
                        <textarea id="MejoraSugerida" rows="2"
                                  cols="30"
                                  v-model="form.MejoraSugerida[aspec.id]"
                                  autofocus
                                  placeholder="MejoraSugerida"
                                  class="mt-1 block w-full overflow-y-scroll"/>
                        <InputError class="mt-2" :message="form.errors.MejoraSugerida"/>
                    </div>

                    <div class="">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Responsable de Gestionar la Mejora</label>
                        <TextInput id="userRecibir" type="text" class="mt-1 block w-full"
                                   v-model="form.ResponsableGestionarMejora[aspec.id]"
                                   :error="form.errors.ResponsableGestionarMejora"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import {onMounted, watchEffect} from "vue";


const props = defineProps({
    form: Object,
    categoriaAgrup: Object,
    BoolRealizarSegui: Boolean,
})
const emit = defineEmits(['update:form']);

onMounted(() => {
    setTimeout( ()=>{
        Object.entries(props.categoriaAgrup).forEach((categoria) => {
            if(typeof categoria === 'object' && categoria[1]){
                categoria[1].aspectos.forEach(aspec => {
                    props.form.aspecto_inspeccion_id[aspec.id] = aspec.aspecto_inspeccion_id
                });
            }//endif
        });
        // console.log("=>(AspectosResultado.vue:80) form", props.form);
        // emit('formUpdated', props.form); // Emitimos el evento con la imagen capturada
    },1200)
})

watchEffect(() => {
    // emit('formUpdated', props.form);
})

const getImageUrl = (aspec) => {
    return aspec.registrofotografico
        ? `/storage/${aspec.registrofotografico}`
        : '';
}
</script>
