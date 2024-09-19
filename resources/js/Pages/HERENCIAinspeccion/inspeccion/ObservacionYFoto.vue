<template>
    <div class="block">
<!--        <label-->
<!--            class="my-4 block text-xl font-medium text-gray-700 dark:text-gray-200 mx-auto text-center">-->
<!--            {{ aspec.value.nombre }}-->
<!--        </label>-->
        <textarea id="observaciones" rows="3"
                  cols="30"
                  v-model="form.observaciones[aspec.id]"
                  autofocus
                  placeholder="Observaciones"
                  class="mt-1 block w-full overflow-y-scroll"/>

        <div id="foto"
             class="mt-2  mx-auto text-center">
            <InstantVideo @photoCaptured="photoCapturedHandler($event,aspec.id)"/>
        </div>
    </div>
<!--    <div v-else class="hidden"></div>-->
</template>
<script setup>
import InstantVideo from "@/Pages/inspeccion/InstantVideo.vue";

const emit = defineEmits(['photoCapturedH']);
let props = defineProps({
    aspec: Object,
    form: Object,
})
const photoCapturedHandler = (photoData,aspecid) => {
  // data.caturedphoto = true
  // form.image[aspecid] = photoData;
    emit('photoCapturedH', {photoData,aspecid})
};
</script>
