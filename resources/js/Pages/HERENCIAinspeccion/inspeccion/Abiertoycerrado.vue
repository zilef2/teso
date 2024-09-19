<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    Abiertoycerrado: Object,
    losresponsables: Object,
})



const emit = defineEmits(["close"]);
// const groupedData = data.groupBy(item => item.category);

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" max-width="xl8">
            <div class="p-6 text-gray-900 dark:text-gray-100">
<!--                asd: {{props.Abiertoycerrado}}-->
                <h2 class="text-lg font-medium">Aspectos {{ lang().label.Abiertoycerrados }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-200">
                    Total de aspectos sin cerrar: {{props.Abiertoycerrado.length}}
                </p>
                <p v-if="props.losresponsables" class="mt-1 text-sm text-gray-600 dark:text-gray-200">
                    Firmas<br>
                    {{props.losresponsables}}
                </p>
                <div class="my-6 grid grid-cols-1 lg:grid-cols-2">
                    <div v-for="(Abiertoc, index) in props.Abiertoycerrado" :key="index"
                        class="flex justify-between w-full px-4 2xl:px-1 py-2 2xl:text-sm">
                        {{ ++index + ". " + Abiertoc?.aspect }} -
                        {{ Abiertoc?.calificacion }} -
<!--                        <small v-if="Abiertoc?.abierto"> Abierto</small>-->
<!--                        <small v-else> Cerrado </small>-->
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="emit('close')"> {{ lang().button.close }} </SecondaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
