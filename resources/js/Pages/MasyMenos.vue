<template>
  <div :class="classMini">
    <button @click="borrarFila(conteoi)" type="button" class="group cursor-pointer outline-none hover:rotate-12 duration-700">
      <svg xmlns="http://www.w3.org/2000/svg" width="50px" height="50px" viewBox="0 0 24 24"
           class="stroke-slate-400 fill-none group-hover:fill-slate-800 group-active:stroke-slate-200 group-active:fill-slate-600 group-active:duration-0 duration-300">
        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" stroke-width="1.5"></path>
        <path d="M8 12H16" stroke-width="1.5"></path>
      </svg>
    </button>
    <button @click="AumentarMasUno()" type="button" class="group cursor-pointer outline-none hover:rotate-90 duration-300">
      <svg xmlns="http://www.w3.org/2000/svg" width="50px" height="50px" viewBox="0 0 24 24"
           class="stroke-slate-400 fill-none group-hover:fill-slate-800 group-active:stroke-slate-200 group-active:fill-slate-600 group-active:duration-0 duration-300">
        <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" stroke-width="1.5"></path>
        <path d="M8 12H16" stroke-width="1.5"></path>
        <path d="M12 16V8" stroke-width="1.5"></path>
      </svg>
    </button>
  </div>
</template>
<script>
export default {
  name: 'MasyMenos',
  props: {
    conteoi: Number
  },
  methods: {
    AumentarMasUno() {
      this.$emit('aumentar');
    },
    borrarFila() {
      this.$emit('disminuir',this.conteoi);
    }
  },

  data() {
    return {
      classMini: "min-w-[100px] max-h-[110px] p-4"
    };
  }
}
</script>
<style>
::-webkit-scrollbar {
  height: 14px; /* Altura del scrollbar vertical */
  width: 4px; /* Ancho del scrollbar horizontal */
  border-radius: 2px;
}

/* Estilo del thumb del scrollbar */
::-webkit-scrollbar-thumb {
  background-color: #00004f; /* Color del thumb */
  border-radius: 1px; /* Radio de borde del thumb */
}

</style>