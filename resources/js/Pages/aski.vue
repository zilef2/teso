<template>
    <Toast :flash="$page.props.flash" />

    <div class="m-4 p-6 bg-white shadow-md rounded-lg mx-auto max-w-2xl">
        <h1 class="text-2xl font-bold mb-4">Pregunta a Fluef I.A.</h1>

        <form @submit.prevent="submitForm" class="space-y-4">
            <label for="question" class="block text-sm font-medium text-gray-700">Escribe tu pregunta:</label>
            <textarea
                cols="20" rows="4"
                v-model="form.question"
                id="question"
                placeholder="Escribe tu pregunta"
                class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-500"
            />
            <span v-if="error" class="text-red-500 text-sm">{{ error }}</span>

            <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700">Preguntar</button>
        </form>

        <div v-if="answer" class="mt-6 p-4 bg-gray-100 border border-gray-300 rounded-md">
            <h2 class="text-lg font-semibold">Respuesta de OpenAI:</h2>
            <p class="text-justify mt-2 w-full text-gray-800">{{ answer }}
            </p>
        </div>
    </div>
</template>


<script>
/*
Algunos de los aspectos importantes que podrías considerar para mostrar en un tablero de Power BI en un reporte de transacciones son:


1. Monto total de transacciones: Puedes mostrar el monto total de transacciones realizadas en un período determinado para tener una visión general del volumen d
2. Distribución por tipo de transacción: Puedes mostrar la cantidad de transacciones realizadas categorizadas por tipo (por ejemplo, ventas, compras, devolucion
3. Análisis de tendencias: Mostrar la evolución de las transacciones a lo largo del tiempo te permitirá identificar patrones estacionales o tendencias que puede
4. Análisis de segmentación: Puedes segmentar las transacciones por diferentes criterios como ubicación geográfica, tipo de cliente o producto para identificar
5. Indicadores de desempeño: Puedes incluir indicadores clave de desempeño (KPIs) relacionados con las transacciones, como porcentaje de cumplimiento de pedidos, tiempo promedio de procesamiento, entre otros.
 ◀
 */
import { useForm } from '@inertiajs/vue3';
import Toast from "@/Components/Toast.vue";

export default {
    components: {Toast},
    props: {
        answer: String,  // Recibe la respuesta de OpenAI
        error: String,   // Recibe el mensaje de error si lo hay
    },
    setup(props) {
        const form = useForm({
            question: '',
        });

        const submitForm = () => {
            form.post(route('openai-question'), {
                preserveScroll: true,
                onSuccess: () => {
                    // La respuesta 'answer' ya estará disponible como prop
                    console.log('Solicitud exitosa.');

                },
                onError: () => {
                    console.log('Hubo un error en la solicitud');
                },
                onFinish: () => null,
            });
        };

        return {
            form,
            submitForm,
            answer: props.answer,  // Usamos la prop 'answer'
            error: props.error,    // Usamos la prop 'error'
        };
    }
};
</script>

<style scoped>
/* Estilos similares al ejemplo anterior */
</style>
