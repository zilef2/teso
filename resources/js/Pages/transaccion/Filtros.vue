<template>
  <div class="flex justify-items-end p-3 gap-6">
    <div class="flex space-x-2">
      <!-- <DangerButton @click="data.deleteBulkOpen = true"
          v-show="data.selectedId.length != 0 && can(['delete transaccion'])" class="px-3 py-1.5"
          v-tooltip="lang().tooltip.delete_selected">
          <TrashIcon class="w-5 h-5" />
      </DangerButton> -->
    </div>
    <TextInput v-model="props.search" type="text"
               class="block w-4/6 md:w-3/6 lg:w-1/6 rounded-lg" placeholder="Codigo cuenta contable"/>
    <TextInput v-model="props.searchContrapartida" type="text"
               class="block w-4/6 md:w-3/6 lg:w-1/6 rounded-lg" placeholder="# de CP"/>
    <TextInput v-model="props.searchConcepto" type="text"
               class="block w-4/6 md:w-3/6 lg:w-1/6 rounded-lg" placeholder="Concepto de flujo"/>
      <label><input v-model="props.OnlyCP" value=false type="checkbox" id="cbox1" />
          Solo CP  -- {{props.OnlyCP}}
      </label
      > <br/>
  </div>
</template>
<script setup lang="ts">
import { defineProps } from 'vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    search: String,
    searchContrapartida: String,
    searchConcepto: String,
    OnlyCP: Boolean,
});

const emit = defineEmits(['update:search', 'update:searchContrapartida', 'update:searchConcepto','update:OnlyCP']);

// Se encarga de emitir los cambios en los campos
const updateSearch = (value) => emit('update:search', value);
const updateSearchContrapartida = (value) => emit('update:searchContrapartida', value);
const updateSearchConcepto = (value) => emit('update:searchConcepto', value);
const updateOnlyCP = (value) => emit('update:OnlyCP', value);
</script>
