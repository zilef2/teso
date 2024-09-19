<template>
    <div>
        <label>
            <input type="radio" :value="opciones.CUMPLE" v-model="localCalificacion"/>
            Cumple
        </label>
        <label>
            <input type="radio" :value="opciones.CUMPLE_PARCIAL" v-model="localCalificacion"/>
            Cumple parcial
        </label>
        <label>
            <input type="radio" :value="opciones.NO_CUMPLE" v-model="localCalificacion"/>
            No cumple
        </label>
        <label>
            <input type="radio" :value="opciones.NO_APLICA" v-model="localCalificacion"/>
            No aplica
        </label>
<!--        asd {{valor}}-->
    </div>
</template>
<script setup>
import {defineProps, defineEmits, ref, watch, onMounted} from 'vue';

const props = defineProps({
    valor: {
        type: String,
        default: 'NO APLICA',
        required: true
    }
});

const emit = defineEmits(['update:valor']);

let localCalificacion = ref(props.valor);
const opciones = {
    CUMPLE: 'CUMPLE',
    CUMPLE_PARCIAL: 'CUMPLE PARCIAL',
    NO_CUMPLE: 'NO CUMPLE',
    NO_APLICA: 'NO APLICA'
};
onMounted(() => {
});

watch(localCalificacion, (newVal) => {
    emit('update:valor', newVal);
});

</script>