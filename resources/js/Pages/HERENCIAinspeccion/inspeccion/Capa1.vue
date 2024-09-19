<template>
    <template v-for="slide in slides" :key="slide">
        <div class="p-24 font-bold text-5xl h-64 flex items-center bg-teal-500 text-white rounded-lg">
            <span class="w-12 text-center">a1</span>
            <span class="text-teal-300">b2</span>
            <span class="w-12 text-center">c3</span>
        </div>
    </template>
</template>

<script>
export default {
    name: 'Capa1',
    slides: [1,2,3]
}
</script>