<template>
    <div class="inline-flex text-center mx-auto gap-8">
        <div>
            <img :src="photoData" alt="Captured Photo"
                 class="w-full lg:max-w-[400px] h-auto border border-amber-600 mb-2 rounded-xl  mx-auto text-center"
            />
        </div>
    </div>

    <div class="inline-flex text-center mx-auto gap-8 my-4">
        <input ref="file" type="file" accept="image/*" @change="handleFileUpload">
    </div>
</template>

<script setup>
import {ref, onMounted, onUnmounted} from 'vue';

const emit = defineEmits(['photoCaptured']);

let photoData = ref(null);
let file = ref(null);

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoData = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
function handleFileUpload(event) {
    previewImage(event)
    file.value = event.target.files[0];
}

const capturePhoto = () => {
    // const canvas = document.createElement('canvas');
    // canvas.width = video.value.videoWidth;
    // canvas.height = video.value.videoHeight;
    // const context = canvas.getContext('2d');
    // context.drawImage(video.value, 0, 0, canvas.width, canvas.height);
    // photoData.value = canvas.toDataURL('image/png');
    // emit('photoCaptured', photoData.value);
    emit('photoCaptured', file);
};



onMounted(() => {});onUnmounted(() => {});
</script>
