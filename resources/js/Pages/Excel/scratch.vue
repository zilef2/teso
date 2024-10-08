<div class="p-4 md:w-1/3">
<div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
    <ArrowUpCircleIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center"/>

    <div class="p-6">
        <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Subir Cuentas</h3>
        <p class="leading-relaxed mb-3"> El excel debe contar con el formato para cuentas</p>

        <form @submit.prevent="uploadFileCuentas" id="upload">
            <input type="file" @input="form.archivo1 = $event.target.files[0]"
                   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
            <br><br>
            <progress v-if="form.progress" :value="form.progress.percentage" max="100"
                      class="bg-sky-200">
                {{ form.progress.percentage }}%
            </progress>
            <div class="flex">
                <!--                                                can(['create user']) && -->
                <PrimaryButton v-show="form.archivo1 !== null" :disabled="form.archivo1 == null"
                               class=" my-4 rounded-md mx-2" :class="{ 'bg-gray-200' : form.archivo1 == null}">
                    {{ lang().button.subir }} excel
                </PrimaryButton>
            </div>
        </form>

        <h2 class="text-xl text-gray-900 dark:text-white mt-12">El formato necesita las siguientes columnas</h2>
        <ul class="list-decimal my-6 mx-5">
            <li v-for="(campos,indicice) in formatoNecesita" class="text-lg">
                {{ Abecedario[indicice] }}.{{ campos }}
            </li>
        </ul>

        <div class="flex items-center flex-wrap my-6">
            <!--                                            <a class="text-gray-500 inline-flex items-center md:mb-2 lg:mb-0">Numero de formularios enviados: </a>-->
            <span
                class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                                <svg class="w-1 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none"
                                                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <p class="text-lg">Cuentas inscritas: {{ props.ncuenta }}</p>
                                            </span>
        </div>
    </div>
</div>
</div>
<script setup lang="ts">
import {ArrowUpCircleIcon} from "@heroicons/vue/24/solid";
import PrimaryButton from "@/Components/PrimaryButton.vue";
</script>
