<template>
    <div v-if="props.historico_seguimientos.length" id="historico_seguimientos" class="mt-2 text-xl mx-auto text-center border border-t-2 border-b-cyan-600">
        Seguimiento
    </div>
    <section class="text-gray-600 body-font overflow-hidden">
        <div class="container px-5 py-0 mx-auto">
            <div class="my-2 divide-y-2 divide-gray-100">
                <div v-for="segui in props.historico_seguimientos" class="py-1 flex flex-wrap md:flex-nowrap">
                    <div class="flex-grow text-center">
                        <p class="leading-relaxed">
                             Mejora sugerida: {{ formatDateSimply(segui.created_at) }}
                        </p>
                        <h2 class="text-lg font-medium text-indigo-700 mb-2 mx-auto">
                            {{ segui.calificacion }}
                        </h2>
                        <p class="leading-relaxed">
                            {{ segui.observaciones }}
                        </p>
                        <a v-if="segui.registrofotografico" class="w-5/6 h-auto text-indigo-500 inline-flex items-center mt-4" :href="segui.registrofotografico" target="_blank">
                            <img :src="getImageUrl(segui)" alt="Foto Dañada" class="h-[280px] w-auto"/>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>


import {formatDate, formatDateSimply} from "@/global";

const props = defineProps({
    categoriaAgrup: Object,
    historico_seguimientos: Object,
})

const getImageUrl = (aspec) => {
    console.log("=>(AspectosSegui.vue:46) aspec", aspec);
    return aspec.registrofotografico
        ? `/storage/${aspec.registrofotografico}`
        : '';
}
</script>
