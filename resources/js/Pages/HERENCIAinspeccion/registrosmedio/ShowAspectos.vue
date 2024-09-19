<template>
    <div v-for="(categoria,index) in categoriaAgrup" :key="index" class="flex flex-wrap -m-4">
        <div class="w-full text-center my-10"><h2 class="text-2xl font-bold">{{ index }}</h2></div>
        <div v-for="(aspec,index) in categoria.aspectos" :key="index" class="p-2 w-1/2 2xl:w-1/3 mx-auto">

            <div class="h-full border-2 border-gray-400 border-opacity-60 rounded-lg overflow-hidden">
                <img v-if="aspec.registrofotografico" :src="aspec.registrofotografico" alt="Foto Dañada" class="h-auto w-full object-cover object-center">
                <div class="p-6">
                    <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">Aspecto</h2>
                    <h1 class="title-font text-lg font-medium text-gray-900 mb-3">{{ aspec.aspectoNombre }}</h1>
                    <div class="flex items-center flex-wrap mt-2 -mb-2 text-center mx-auto">
                        <a class="text-indigo-500 inline-flex items-center lg:mb-2 text-lg text-center">{{ aspec.calificacion }}</a>
                    </div>
                    <textarea v-if="aspec.observaciones" id="observaciones" rows="4" cols="30" v-model="aspec.observaciones" autofocus
                              placeholder="Observaciones" disabled
                              class="mt-4 block w-full overflow-y-scroll"/>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>

const props = defineProps({
    categoriaAgrup: Object
})
</script>
