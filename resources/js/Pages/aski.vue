<template>
    <Toast :flash="$page.props.flash"/>
    <div class="form-container">
        <h1 class="title">Pregunta a OpenAI</h1>

        <form @submit.prevent="create" class="form">
            <label for="expertise">Selecciona un rol:</label>
            <select v-model="expertise" id="expertise" class="input-select">
                <option value="finanzas">Experto en Finanzas</option>
                <option value="programacion">Experto en Programación (Laravel)</option>
            </select>

            <label for="question">Escribe tu pregunta:</label>
            <input
                type="text"
                v-model="form.question"
                id="question"
                placeholder="Escribe tu pregunta"
                class="input-text"
            />
            <span v-if="errorMessage" class="error-message">{{ errorMessage }}</span>

            <button type="submit" class="btn-submit">Preguntar</button>
        </form>

        <div v-if="answer" class="answer-box">
            <h2>Respuesta de OpenAI:</h2>
            <!-- Caja para mostrar la respuesta en un bloque de texto formateado -->
            <pre class="answer-content">{{ formattedAnswer }}</pre>
        </div>

        <div v-if="error" class="error-box">
            <p>{{ error }}</p>
        </div>
    </div>
</template>

<script>
import {computed, ref} from 'vue';
import {useForm} from '@inertiajs/vue3';

export default {
    setup() {
        // Inicializamos las variables
        const form = useForm({
            question: '',  // Utilizamos `form.question` para la pregunta
        });

        const expertise = ref('finanzas');  // Rol del experto
        const answer = ref(null);
        const error = ref(null);
        const errorMessage = ref('');

        // Función de envío similar al ejemplo que proporcionaste
        const create = () => {
            // Limpiar mensajes previos
            answer.value = null;
            error.value = null;
            errorMessage.value = '';

            // Validar la pregunta antes de enviarla
            if (!form.question.trim()) {
                errorMessage.value = 'Por favor ingresa una pregunta.';
                return;
            }

            // Preparamos el prompt para OpenAI
            const prompt = `Actúa como ${expertise.value}. ${form.question}`;

            form.post(route('openai-question'), {
                preserveScroll: true,
                onSuccess: () => {
                    answer.value = form.data.answer; // Obtener respuesta desde la solicitud
                    // form.reset(); // Reiniciar formulario tras el éxito
                },
                onError: () => {
                    error.value = 'Error en la solicitud. Inténtalo de nuevo.';
                },
                onFinish: () => {
                    // Puedes añadir lógica adicional al finalizar
                },
            }, {
                question: prompt, // Datos enviados en la solicitud
            });
        };

        // Formatear la respuesta para mejorar su visualización
        const formattedAnswer = computed(() => {
            return answer.value ? answer.value.replace(/\n/g, '<br>') : ''; // Reemplaza saltos de línea
        });

        return {
            form,
            expertise,
            answer,
            error,
            errorMessage,
            create,
            formattedAnswer,
        };
    },
};
</script>

<style scoped>
.form-container {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.title {
    text-align: center;
    font-size: 24px;
    margin-bottom: 20px;
}

.form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.input-text, .input-select {
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #ddd;
    font-size: 16px;
}

.btn-submit {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-submit:hover {
    background-color: #45a049;
}

.answer-box, .error-box {
    margin-top: 20px;
    padding: 15px;
    border-radius: 4px;
}

.answer-box {
    background-color: #e7f3fe;
    border: 1px solid #b3d4fc;
}

.error-box {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.error-message {
    color: red;
    font-size: 14px;
}
</style>
