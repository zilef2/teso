<template>
  <div class="m-8">
      <div class="card work">
          <div class="img-section">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M5 22V14M5 14V4M5 14L7.47067 13.5059C9.1212 13.1758 10.8321 13.3328 12.3949 13.958C14.0885 14.6354 15.9524 14.7619 17.722 14.3195L17.8221 14.2945C18.4082 14.148 18.6861 13.4769 18.3753 12.9589L16.8147 10.3578C16.4732 9.78863 16.3024 9.50405 16.2619 9.19451C16.2451 9.06539 16.2451 8.93461 16.2619 8.80549C16.3024 8.49595 16.4732 8.21137 16.8147 7.64221L18.0932 5.51132C18.4278 4.9536 17.9211 4.26972 17.2901 4.42746C15.8013 4.79967 14.2331 4.69323 12.8082 4.12329L12.3949 3.95797C10.8321 3.33284 9.1212 3.17576 7.47067 3.50587L5 4M5 4V2" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
          </div>
          <div class="card-desc">
              <div class="card-header">
                  <div class="card-title">{{props.nombre}}</div>
              </div>
              <div class="card-time">{{ props.Indicador }}</div>
              <p class="recent">
                  Total transacciones: {{props.Total}}
              </p>
          </div>
      </div>
  </div>
</template>
<script setup>
const props = defineProps({
    Indicador:Object,
    Total:Object,
    nombre:String,
})

</script>

<style>
.card {
    --primary-clr: #000000;
    --dot-clr: #000002;
    --play: hsl(109, 100%, 22%);
    width: 200px;
    height: 170px;
    border-radius: 10px;
}

.card {
    font-family: "Arial",serif;
    color: #fff;
    display: grid;
    cursor: pointer;
    grid-template-rows: 50px 1fr;
}

.card:hover .img-section {
    transform: translateY(1em);
}

.card-desc {
    border-radius: 10px;
    padding: 15px;
    position: relative;
    top: -10px;
    display: grid;
    gap: 10px;
    background: var(--primary-clr);
}

.card-time {
    font-size: 1.8em;
    font-weight: 600;
}

.img-section {
    transition: 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    background: hsl(0, 0%, 100%);
}

.card-header {
    display: flex;
    align-items: center;
    width: 100%;
}

.card-title {
    flex: 1;
    font-size: 0.9em;
    font-weight: 500;
}

.card-menu {
    display: flex;
    gap: 4px;
    margin-inline: auto;
}

.card svg {
    float: right;
    max-width: 100%;
    max-height: 100%;
}

.card .dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--dot-clr);
}

.card .recent {
    line-height: 0;
    font-size: 0.8em;
}
</style>
