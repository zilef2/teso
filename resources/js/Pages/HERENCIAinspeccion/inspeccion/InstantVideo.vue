<template>
    <div class="inline-flex text-center mx-auto gap-8">
        <video v-if="!photoData" ref="video" autoplay
               class="w-full lg:max-w-[400px] h-auto
                   border border-amber-600 mb-2 rounded-2xl
                   box-decoration-clone bg-gradient-to-r from-yellow-600 to-blue-500 text-white px-2
                   mx-auto text-center shadow-2xl"
        ></video>
        <div v-else>
            <img :src="photoData" alt="Captured Photo"
                 class="w-full lg:max-w-[400px] h-auto border border-amber-600 mb-2 rounded-xl  mx-auto text-center"
            />
        </div>
    </div>
    <div class="grid grid-cols-2 my-2 items-center text-center w-full md:w-5/6 lg:w-2/3 mx-auto gap-6 md:gap-2">
        <div @click="capturePhoto"
             class="w-12 sm:w-20 inline-block shrink-0 rounded-md
             p-0 md:p-4 text-lg font-medium text-gray-100 transition mx-auto
             bg-blue-300
             cursor-pointer
             hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500
             dark:hover:bg-blue-700 dark:hover:text-white">
            📷
        </div>
        <div @click="Restart"
             class="w-12 sm:w-20 inline-block shrink-0 rounded-md  mx-auto
             p-0 md:p-4 text-lg font-medium text-gray-100 transition
             bg-gray-200
             cursor-pointer
             hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500
             dark:hover:bg-blue-700 dark:hover:text-white">
            🔙
        </div>
    </div>

<!--    <br><br>-->
<!--    <input type="file" capture="environment" accept="video/*">-->
<!--    <br><br>-->
</template>

<script setup>
import {ref, onMounted, onUnmounted} from 'vue';

const emit = defineEmits(['photoCaptured']);

let video = ref(null);
let photoData = ref(null);

const startCamera2 = async () => {
    try {
        Restart()
        const constraints = {
              video: {
                  // facingMode: 'user',
                   facingMode: {
                      ideal: 'environment'
                    },
                    width: {ideal: 840, max: 840},
                    height: {ideal: 520, max: 520}
              }
        };
        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        video.value.srcObject = stream;
    } catch (error) {
        console.error('Error al acceder a la cámara: ', error);
    }
}

const startCamera = async () => {
    try {
        const constraints = {
              video: {
                  // facingMode: 'user',
                   facingMode: {
                      ideal: 'environment'
                    },
                  width: {ideal: 1280,max: 1920},
                  height: {ideal: 720,max: 1080}
              }
        };
        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        video.value.srcObject = stream;
    } catch (error) {
        console.error('Error accessing camera:', error);
    }
};

const capturePhoto = () => {
    const canvas = document.createElement('canvas');
    canvas.width = video.value.videoWidth;
    canvas.height = video.value.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video.value, 0, 0, canvas.width, canvas.height);
    photoData.value = canvas.toDataURL('image/png');
    emit('photoCaptured', photoData.value); // Emitimos el evento con la imagen capturada
};

onMounted(() => {
    startCamera();
});

const Restart = () => {
    photoData.value = null;  // Limpia el dato de la foto capturada
    startCamera();  // Reinicia la cámara
};

onUnmounted(() => {
    if (video.value && video.value.srcObject) {
        const stream = video.value.srcObject;
        const tracks = stream.getTracks();
        if(tracks)
            tracks.forEach((track) => track.stop());
    }
});
</script>
