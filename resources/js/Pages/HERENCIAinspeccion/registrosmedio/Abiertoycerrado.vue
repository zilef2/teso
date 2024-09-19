<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    Abiertoycerrado: Object,
})

const emit = defineEmits(["close"]);
const getImageUrl = () => {
    return props.Abiertoycerrado
        ? `/storage/${props.Abiertoycerrado}`
        : '';
}
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" max-width="xl8">
            <div class="p-6 text-gray-900 dark:text-gray-100">
<!--                asd: {{props.Abiertoycerrado}}-->
                <h2 class="text-lg font-medium">{{ lang().label.EvidenciaMedios }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-200">
                    Foto
                </p>
                <div class="my-6 grid grid-cols-1 lg:grid-cols-2">
                    <div>
            <img 
                     :src="getImageUrl(aspec)" alt="Foto Dañada"
                     class="mx-auto max-h-60 lg:max-h-72 xl:max-h-full xl:h-full w-auto object-cover object-center hover:border-4 hover:border-amber-300">

        </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="emit('close')"> {{ lang().button.close }} </SecondaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
