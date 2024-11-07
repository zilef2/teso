<script setup>
import { router } from '@inertiajs/vue3';
import { pickBy } from "lodash";
import { reactive, watchEffect } from "vue";
import Icon from "@/Components/Icon.vue";

const props = defineProps({
    links: Object,
    filters: Object
})

const data = reactive({
    params: {
        search: props.filters?.search,
        search2: props.filters?.search2,
        search3: props.filters?.search3,
        search4: props.filters?.search4,
        searchNumCuenta: props.filters?.searchNumCuenta,
        searchBanco: props.filters?.searchBanco,
        searchtipo: props.filters?.searchtipo,

        searchContrapartida: props.filters?.searchContrapartida,
        searchDocumento: props.filters?.searchDocumento,
        searchCodigo: props.filters?.searchCodigo,
        searchConcepto: props.filters?.searchConcepto,
        searchDocRef: props.filters?.searchDocRef,
        OnlyCP: props.filters?.OnlyCP,
        OnlyEmptyCP: props.filters?.OnlyEmptyCP,
        concepto_flujo_omologaci: props.filters?.concepto_flujo_omologaci,
        sin_afectacion: props.filters?.sin_afectacion,

        codigo: props.filters?.codigo,
        numero_documento: props.filters?.numero_documento,
        valor_debito: props.filters?.valor_debito,
        valor_credito: props.filters?.valor_credito,

        resultado_asientos: props.filters?.resultado_asientos,

        field: props.filters?.field,
        order: props.filters?.order,
        perPage: props.filters?.perPage,
    },
})
const goto = (link) => {
    let params = pickBy(data.params);
    router.get(link, params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}
watchEffect(() => {
    data.params.search = props.filters?.search
    data.params.search2 = props.filters?.search2
    data.params.search3 = props.filters?.search3
    data.params.search4 = props.filters?.search4

    data.params.searchNumCuenta = props.filters?.searchNumCuenta
    data.params.searchBanco = props.filters?.searchBanco
    data.params.searchtipo = props.filters?.searchtipo

    data.params.searchContrapartida = props.filters?.searchContrapartida
    data.params.searchDocumento = props.filters?.searchDocumento
    data.params.searchCodigo = props.filters?.searchCodigo
    data.params.searchConcepto = props.filters?.searchConcepto
    data.params.searchDocRef = props.filters?.searchDocRef
    data.params.OnlyCP = props.filters?.OnlyCP
    data.params.OnlyEmptyCP = props.filters?.OnlyEmptyCP
    data.params.concepto_flujo_omologaci = props.filters?.concepto_flujo_omologaci
    data.params.sin_afectacion = props.filters?.sin_afectacion

    data.params.codigo = props.filters?.codigo
    data.params.numero_documento = props.filters?.numero_documento
    data.params.valor_debito = props.filters?.valor_debito
    data.params.valor_credito = props.filters?.valor_credito
    data.params.resultado_asientos = props.filters?.resultado_asientos

    data.params.field = props.filters?.field
    data.params.order = props.filters?.order
    data.params.perPage = props.filters?.perPage
})

</script>
<template>
    <div class="ml-2 mx-2" v-if="links.data.length !== 0">
        {{ links.from }}-{{ links.to }} {{ lang().label.of }} {{ links.total }}
    </div>
    <div class="flex flex-col space-y-2 mx-auto p-6 text-lg" v-if="links.data.length === 0">
        <Icon :name="'nodata'" class="w-auto h-16" />
        <p>{{ lang().label.no_data }}</p>
    </div>
    <div v-if="links.links.length > 3">
<!--        hidden-->
        <ul
            class="flex justify-center items-center rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <li v-for="(link, index) in links.links" :key="index">
                <button v-on:click="goto(link.url)" class="px-4 py-2 hover:bg-primary hover:text-white"
                    :class="{ 'bg-primary text-white': link.active }" v-html="link.label"
                    :disabled="link.url == null"></button>
            </li>
        </ul>
<!--         <ul class="flex justify-center items-center rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">-->
<!--            <li>-->
<!--                <button v-on:click="goto(links.prev_page_url)" class="px-4 py-2" v-html="'&laquo;'"-->
<!--                    :disabled="links.prev_page_url == null"></button>-->
<!--            </li>-->
<!--            <li>-->
<!--                <p class="px-4 py-2 bg-primary text-white" v-html="links.current_page"></p>-->
<!--            </li>-->
<!--            <li>-->
<!--                <button v-on:click="goto(links.next_page_url)" class="px-4 py-2" v-html="'&raquo;'"-->
<!--                    :disabled="links.next_page_url == null"></button>-->
<!--            </li>-->
<!--        </ul> -->
    </div>
</template>
