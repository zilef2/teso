<template>
    <div  v-for="(categoria,index) in categoriaAgrup" :key="index" class="flex flex-wrap my-4 xl:h-1/2">
        <div v-if="categoriaAgrup[index].aspectos.length" class="w-full text-center my-1">
            <h2 class="text-lg font-bold">{{ index }}</h2>
        </div>
        <div v-for="(aspec,inde) in categoria.aspectos" :key="inde" class="p-4 w-full 2xl:w-1/2 4xl:w-1/4">
            <div class="bg-gray-100 hover:border-4 border-2 border-gray-300 hover:border-gray-600 border-opacity-60 rounded-lg overflow-hidden">
<!--                <img v-if="aspec.registrofotografico" :src="aspec.registrofotografico" alt="Foto Dañada"-->
<!--                     class="max-h-80 xl:max-h-full xl:h-full w-full object-cover object-center hover:border-4 hover:border-amber-300">-->
                <img v-if="aspec.registrofotografico"
                     :src="getImageUrl(aspec)" alt="Foto Dañada"
                     class="mx-auto max-h-60 lg:max-h-72 xl:h-[280px] xl:max-h-[280px] w-auto object-cover object-center rounded-2xl mt-2">

                <div class="p-6">
                    <h2 class="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">Aspecto</h2>
                    <h1 class="title-font text-xs md:text-lg font-medium text-gray-900 mb-4 h-[70px] 4xl:h-[90px]">{{ aspec.aspectoNombre }}</h1>
                    <div class="flex-nowrap items-center my-4 text-center mx-auto gap-8">
                        <p class="text-indigo-500 inline-flex items-center lg:mb-2 text-sm md:text-lg text-center">{{ aspec.calificacion }}</p>
                    </div>
                    <div class="flex-wrap items-center mt-2 -mb-2 text-center mx-auto gap-8">
                        <p class="text-black inline-flex items-center lg:mb-2 text-sm md:text-lg text-center">Observaciones: {{ aspec.observaciones }}</p>
                    </div>
                    <div v-if="!props.BoolRealizarSegui" class="flex-wrap items-center mt-2 -mb-2 text-center mx-auto gap-8">
                        <p class="text-black inline-flex items-center lg:mb-2 text-sm md:text-lg text-center">Mejora Sugerida: {{ aspec.MejoraSugerida }}</p>
                    </div>
                    <div v-if="!props.BoolRealizarSegui" class="flex-wrap items-center my-3 md:mt-2 md:-mb-2 text-center mx-auto gap-8">
                        <p class="text-black inline-flex items-center lg:mb-2 text-sm md:text-lg text-center">Responsable Gestionar la Mejora: {{ aspec.ResponsableGestionarMejora }}</p>
                    </div>
                    <div v-if="!props.BoolRealizarSegui" class="flex-wrap items-center mt-2 -mb-2 text-center mx-auto gap-8">
                        <p class="text-black inline-flex items-center lg:mb-2 text-sm md:text-lg text-center">Fecha de Compromiso: {{ aspec.FechaCompromiso ? formatDateSimply(aspec.FechaCompromiso) : ''}}</p>
                    </div>
                    <HistoricoSeguimiento
                        :historico_seguimientos="aspec.seguimientos"
                    />

                    <RealizarSeguimiento v-if="props.BoolRealizarSegui"
                        :aspec="aspec"
                        :historico_seguimientos="aspec.seguimientos"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
//todo: si solo hay un aspecto a evaluar, darle el espacio completo (grid col-full)
import RealizarSeguimiento from "@/Pages/inspeccion/Seguimiento/RealizarSeguimiento.vue";
import HistoricoSeguimiento from "@/Pages/inspeccion/Seguimiento/HistoricoSeguimiento.vue";
import {formatDateSimply} from "@/global";

const props = defineProps({
    categoriaAgrup: Object,
    BoolRealizarSegui: Boolean,
})

const getImageUrl = (aspec) => {
    return aspec.registrofotografico
        ? `/storage/${aspec.registrofotografico}`
        : '';
}
// let historico_seguimientos =
// GuardarSeguimiento
</script>
