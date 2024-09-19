<template>
    <div>
        <div>
            <h2 class="mx-auto text-center shadow-sm text-[#499884] font-extrabold animate-pulse p-4 bg-blue-100 rounded-lg">
                {{ titulote }}
            </h2>
            <div class="px-1 flex py-2 text-gray-700 items-center text-center bg-transparent p-0">
                <div class="mx-auto relative overflow-x-auto shadow-md rounded-2xl">
                    
                    <table class="w-full text-xl text-center rounded-xl text-gray-700 dark:text-gray-400">
                        <thead class="text-gray-700 bg-gray-200 dark:bg-gray-700 dark:text-gray-400 rounded-xl">
                        <tr class="rounded-xl">
                            <th scope="col" class="p-6">#</th>
                            <th scope="col" class="col-span-2 p-6">Necesidad</th>
                            
                            <th scope="col" class="col-span-2 p-6">Justificación</th>
                            <th scope="col" class="col-span-2 p-6">Actividad</th>
                            
                            <th scope="col" class="p-6">Categoria</th>
<!--                            <th scope="col" class="p-6">Unidad de medida</th>-->
                            <th scope="col" class="p-6">Cantidad</th>
                            <th scope="col" class="p-6 min-w-[250px]">Valor unitario</th>
                            
                            <th scope="col" class="p-6 min-w-[250px]">Valor total</th> 
                            <th scope="col" class="p-6">Periodo de inicio de ejecucion</th> 
<!--                            <th scope="col" class="p-6">Vigencias anteriores</th> -->
                            <th scope="col" class="p-6 min-w-[270px]">Asignado en la vigencia anterior</th>
                            
                            
                            <th scope="col" class="p-6 min-w-[500px]">Procesos articulados</th> 
                            <th scope="col" class="p-6 min-w-[500px]">Plan de mejoramiento y mejora</th> 
                            <th scope="col" class="p-6 min-w-[750px]">Líneas del Plan de Desarrollo Institucional 2024-2028</th>
                            
                            <th scope="col" class="p-6">Frecuencia de uso</th>  
                            <th scope="col" class="p-6">Mantenimientos requeridos</th>  
                            <th scope="col" class="p-6">Capacidad instalada</th>  
                            <th scope="col" class="p-6">Riesgo de la inversion</th>  
                            <th scope="col" class="p-6">Anexos</th>
                        </tr>
                        </thead>
                        <tbody class="items-center">
                            <tr v-for="(element, indexNecesidad) in infoEnviada"
                                class="bg-gray-100 even:dark:bg-gray-900 border-b dark:border-white rounded-xl hover:bg-sky-50"
                                :class="{ 'bg-gray-300' : indexNecesidad % 2 === 0}">
                                
                                <td class="w-12 text-center py-2 -px-2">{{ indexNecesidad + 1 }} </td>
                                <td v-if="element.necesidad && element.necesidad.length > 300" class="w-full text-center py-2 col-span-2">
                                    {{ element.necesidad.slice(0, 300) }} ... 
                                </td>
                                <td v-else-if="element.necesidad && element.necesidad.length <= 300" class="w-full text-center py-2 col-span-2">
                                    {{ element.necesidad }}
                                </td>
                                
                                <td v-if="element.justificacion && element.justificacion.length > 300" class="w-full text-center py-2 col-span-2">
                                    {{ element.justificacion.slice(0, 300) }} ... 
                                </td>
                                <td v-else-if="element.justificacion && element.justificacion.length <= 300" class="w-full text-center py-2 col-span-2">
                                    {{ element.justificacion }}
                                </td>
                                <td class="w-full text-center py-2">{{ (element.actividad) }}</td>
    
                                <td class="w-full text-center py-2">
                                    {{ (categoria.find((item) => item.value == element.categoria))?.label }} 
                                </td>
    
    <!--                            <td class="w-full text-center py-2">{{ (element.unidad_de_medida) }}</td>-->
                                <td class="w-full text-center py-2">{{ (element.cantidad) }} {{ (element.unidad_de_medida) }}</td>
                                <td class="w-full text-center py-2">{{ number_format(element.valor_unitario,0,1) }}</td>
                                <td class="w-full text-center py-2">{{ number_format(element.valor_total_solicitatdo_por_necesidad, 0, 1) }}</td>
                                
                                
                                <td class="w-full text-center py-2">{{ (element.periodo_de_inicio_de_ejecucion) }}</td>
    <!--                            <td class="w-full text-center py-2">{{ (element.vigencias_anteriores) }}</td>-->
                                <td class="w-full text-center py-2">{{ number_format(element.valor_asignado_en_la_vigencia_anterior,0,1) }}</td>
                                
                                
                                <td class="w-full text-center py-2">{{ ProInvolucrados(element.procesos_involucrados) }}</td>
                                <td class="w-full text-center py-2">{{ Pmejoramientonecesidad(element.plan_de_mejoramiento_al_que_apunta_la_necesidad) }}</td>
                                <td class="w-full text-center py-2">{{ LineaPlan(element.linea_del_plan_desarrollo_al_que_apunta_la_necesidad) }}</td>
                                
                                
                                <td class="w-full text-center py-2">{{ (element.frecuencia_de_uso) }}</td>
                                <td class="w-full text-center py-2">{{ (element.mantenimientos_requeridos) }}</td>
                                <td class="w-full text-center py-2">{{ (element.capacidad_instalada) }}</td>
                                <td class="w-full text-center py-2">{{ (element.riesgo_de_la_inversion) }}</td>
                                <td class="w-full text-center py-2">{{ (element.anexos) }}</td>
                            </tr>
                            <tr>
                                <td class="w-full text-center py-2">  </td>
                                <td class="w-full text-center py-2">  </td>
                                <td class="w-full text-center py-2">  </td>
                                <td class="w-full text-center py-2">  </td>
                                <td class="w-full text-center py-2">  </td>
                                <td class="w-full text-center py-2">  </td>
                                <td class="w-full text-center py-2">  </td>
                                <td class="w-full text-center py-2 text-xl font-bold">{{ number_format(Eltotal,0,1) }}</td>
    <!--                            <td class="w-full text-center py-2">{{ number_format(totalnecesidades,0,1) }}</td>-->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {number_format} from "@/global";

export default {
name: 'TablaResumenEnviado',
    data() {
        return {
            totalnecesidades:0,
            titulote:'',
            // selects multiples
            planmejoramientonecesidad: [
                {value: 1, label: 'PMM Institucional'},
                {value: 2, label: 'PMM Programas'},
                {value: 3, label: 'PMM Auditorías'},
                {value: 0, label: 'Ninguno'},
            ],
            lineadelplan: [
                {value: 1, label: '1. Academia Transformadora de Vidas'},
                {value: 2, label: '2. Intercambio de Saberes para la Transformación del Entorno Social, Productivo y Científico'},
                {value: 3, label: '3. Ecosistema Tecnológico Colmayor'},
                {value: 4, label: '4. Sostenibilidad y Gestión Humana Integral'},
            ],
            proceso_que_solicita_presupuesto: [
                {label: "Admisiones, Registro y Control", value: 1},
                {label: "Aseguramiento de la Calidad Académica", value: 2},
                {label: "Biblioteca", value: 3},
                {label: "Bienes y Servicios", value: 4},
                {label: "Bienestar Institucional", value: 5},
                {label: "Centro de Lenguas", value: 6},
                {label: "Comunicación y Mercadeo", value: 7},
                {label: "Contabilidad", value: 8},
                {label: "Control Interno", value: 9},
                {label: "Extensión Académica y Proyección Social", value: 10},
                {label: "Facultad de Administración", value: 11},
                {label: "Facultad de Arquitectura e Ingeniería", value: 12},
                {label: "Facultad de Ciencias de la Salud", value: 13},
                {label: "Facultad de Ciencias Sociales y Educación", value: 14},
                {label: "Formación para el Trabajo y el Desarrollo Humano", value: 15},
                {label: "Gestión Administrativa y Financiera", value: 16},
                {label: "Gestión Ambiental", value: 17},
                {label: "Gestión de Infraestructura Física", value: 18},
                {label: "Gestión de la Calidad", value: 19},
                {label: "Gestión de la Seguridad y Salud en el Trabajo", value: 20},
                {label: "Gestión de Tecnología y Medios Audiovisuales", value: 21},
                {label: "Gestión del Talento Humano", value: 22},
                {label: "Gestión Documental", value: 23},
                {label: "Gestión Jurídica", value: 24},
                {label: "Graduados", value: 25},
                {label: "Ingreso, Permanencia y Graduación", value: 26},
                {label: "Innovación, Emprendimiento y Transferencia Tecnológica", value: 27},
                {label: "Internacionalización", value: 28},
                {label: "Investigación", value: 29},
                {label: "Laboratorios Facultad Arquitectura e Ingeniería", value: 30},
                {label: "Laboratorios Facultad Ciencias de la Salud", value: 31},
                {label: "Laboratorios Facultad de Administración", value: 32},
                {label: "LACMA", value: 33},
                {label: "Planeación Institucional", value: 34},
                {label: "Presupuesto", value: 35},
                {label: "Presupuesto Participativo", value: 36},
                {label: "Rectoría", value: 37},
                {label: "Secretaria General", value: 38},
                {label: "Tesorería", value: 39},
                {label: "Vicerrectoría Académica", value: 40},
                {label: "Vicerrectoría de Investigación y Extensión", value: 41},
                {label: "Virtualidad", value: 42},
            ],

            
            actividades: [
                {value: 1, label: 'Realizar apoyo técnico o profesional'},
                {value: 2, label: 'Realizar apoyo para administración software Investiga y Gestión de la información'},
                {
                    value: 3,
                    label: 'Realizar apoyo a la gestión estrategíca de grupos de invetigación y el mejoramiento en la categorización de investigadores ante MinCiencias, dirigir el proceso editorial de la revista SINERGIA y apoyar los trámites de certificación de productos de investigació'
                },
                {
                    value: 4,
                    label: 'Realizar apoyo en temas de protección de la propiedad intelectual y la transferencia tecnológica y de conocimiento de la Institución '
                },
                {value: 5, label: 'Realizar el apoyo administrativo, técnico y logístico al  proceso de Investigación'},
                {value: 6, label: 'Prestar servicios de apoyo educativo dentro del proceso de Investigación '},
                {
                    value: 7,
                    label: 'Realizar apoyo y acompañamiento en la gestión de los programas del Centro de Emprendimiento, Innovación y trasnferencia tecnologica '
                },
                {
                    value: 8,
                    label: 'Realizar apoyo para desarrollar la evaluación de Proyectos de investigación, productos, artículos revista y escalafonamiento docente'
                },
                {value: 9, label: 'Apoyar la generacion de productos derivados de proyectos de investigacion '},
                {
                    value: 10,
                    label: 'Prestar los servicios de preparación y presentación de impuestos y tasas para la protección de propiedad intelectual '
                },
                {
                    value: 11,
                    label: 'Realizar salidas académicas para presentación de resultados de investigación y  Actividades de trabajo de campo para recolección de datos, toma de muestras, Movilidad aerea'
                },
                {
                    value: 12,
                    label: 'Realizar salidas académicas para presentación de resultados de investigación y  Actividades de trabajo de campo para recolección de datos, toma de muestras, Movilidad terrestre'
                },
                {
                    value: 13,
                    label: 'Prestar servicio de hospedaje para las salidas academicas  y  Actividades de trabajo de campo para recolección de datos, toma de muestras'
                },
                {value: 14, label: 'Realizar la compra de equipos requeridos en proyectos de investigación'},
                {value: 15, label: 'Realizar compra de insumos para proyectos de investigación'},
                {value: 16, label: 'Realizar afiliaciones a redes académicas e investigación y pagos de membresía'},
                {value: 17, label: 'Realizar publicaciones y procesos editoriales.'},
                {value: 18, label: 'Realizar  soporte tecnico de los sistemas academicos al proceso de  Admisiones , Registro y Control '},
                {value: 19, label: 'Realizar  apoyo  y asesoria  a estudiantes en los procesos de de  Admisiones , Registro y Control '},
                {value: 20, label: 'Realizar la compra de insumos como diplomas, actas, portadiplomas  para ceremonias de grado de la institución'},
                {
                    value: 21,
                    label: 'Prestar servicios profesionales para apoyar el logro de las metas de programas e institucional, propuestas en el Proceso de Aseguramiento de la Calidad Académica'
                },
                {value: 22, label: 'Preservar los libros, revistas, colecciones , bases de datos de la biblioteca Institucinal '},
                {value: 23, label: 'Actualizar programas y herramientas digitales de la Biblioteca '},
                {value: 24, label: 'Realizar apoyo técnico y/o profesional al proceso Biblioteca '},
                {value: 25, label: 'Realizar Mantenimiento de equipos (Antenas, Equipos RFID y TAG).'},
                {value: 26, label: 'Desarrollar proyectos, organización y participación en eventos.'},
                {value: 27, label: 'Realizar afiliaciones a redes académicas y pagos de membresía'},
                {value: 28, label: 'Realizar apoyo tecnico, operativo y logistico al proceso de internacionalización'},
                {value: 29, label: 'Realizar movilidad saliente y entrante para estudiantes  que  particpen en  eventos academicos '},
                {value: 30, label: 'Realizar apoyo tecnico y/o profesional en educación artistica y cultural al proceso de Bienestar Institucional'},
                {
                    value: 31,
                    label: 'Realizar apoyo tecnico y/o profesional en la línea de deportes ofertada desde el proceso de Bienestar Institucional'
                },
                {
                    value: 32,
                    label: 'Brindar apoyo asistencial a los programas de promoción de la salud y y prevención desarrollados para la comunidad institucional'
                },
                {value: 33, label: 'Realizar apoyo tecnico y/o profesional en la parte administratova y logistica del proceso de Bienestar'},
                {value: 34, label: 'Implementar Programa de seguridad alimentaria'},
                {value: 35, label: 'Implementar servicios de bienestar institucional'},
                {value: 36, label: 'Realizar afiliación anual ASCUN, fortalecimiento de las líneas de desarrollo humano, deporte y cultura.'},
                {value: 37, label: 'Realizar mantenimiento a los implementos de las aulas de Bienestar Institucional'},
                {value: 38, label: 'Realizar traslados de los grupos de deporte, cultura y otras activiades '},
                {value: 39, label: 'Realizar Montaje, diseño y proyección de obras de arte'},
                {value: 40, label: 'Realizar adquisición y mantenimiento de instrumentos musicales '},
                {value: 41, label: 'Desarrollar actividades deportivas y recreativas '},
                {
                    value: 42,
                    label: 'Realizar envío masivo de correos, sms, IVR y recolección de leads e interesados, usada para comunicación interna y externa con los públicos de interés'
                },
                {
                    value: 43,
                    label: 'Realizar apoyo en la planeación y desarrollo de estrategias en marketing e imagen en el proceso de Comunicaciones_Bienestar'
                },
                {value: 44, label: 'Implementar el Plan de medios masivos y pauta digital, y fortalecimiento de la página web institucional'},
                {value: 45, label: 'Realizar productos audiovisuales para campañas internas y externas de la Institución.'},
                {value: 46, label: 'Realizar apoyo en diseño gráfico de material publicitario Institucional y diseño de campañas.'},
                {value: 47, label: 'Realizar apoyo en el desarrollo de estrategias de comunicaciones y mercadeo de la Institución'},
                {value: 48, label: 'Implementar la señalética que se debe actualizar en el campus institucional'},
                {value: 49, label: 'Adquirir souvenirs para estretagias institucionales y de comunicaciones'},
                {
                    value: 50,
                    label: 'Realizar compra de equipos para el fortalecimiento del material audiovisual del Proceso de Comunicación y Mercadeo'
                },
                {
                    value: 51,
                    label: 'Relizar apoyo profesionales de comunicación y diseño gráfico de los programas y servicios de bienestar a la comunidad institucional. '
                },
                {value: 52, label: 'Implementar la carnetización en la comunidad Institucional'},
                {value: 53, label: 'Realizar apoyo en el desarrollo de estrategias de comunicaciones y mercadeo de la Institución. Virtualidad - '},
                {value: 54, label: 'Implementar las estrategias del Plan de Comunicaciones y Mercadeo Institucional'},
                {
                    value: 55,
                    label: 'Realizar los  programas de promoción de la salud y y prevención desarrollados para la comunidad institucional por parte del proceso de Bienestar Institucional ( Universidades preventivas)'
                },
                {value: 56, label: 'Realizar apoyo al proceso de Vicerectoria Académica '},
                {value: 57, label: 'Realizar apoyo técnico y/o profesional al Centro de graduados '},
                {value: 58, label: 'Adquirir software para administración y gestión de la bolsa de empleo'},
                {value: 59, label: 'Adquirir placas para los estudiantes de práctica de la institución'},
                {value: 60, label: 'Realizar eventos académicos y de relacionamiento con graduados  y sector productivo '},
                {value: 61, label: 'Realizar pasantias empresariales  para estudiantes y docentes '},
                {value: 62, label: 'Realizar la gestión de la información del Observatorio de Permanencia y Calidad '},
                {value: 63, label: 'Realizar apoyo educativo en el proceso  de ingreso, permanencia y graduación '},
                {
                    value: 64,
                    label: 'Realizar  apoyo técnico y/o profesional en  producción audiovisual para la grabación, edición y posproduccion de videos y contenido audiovisual que acompañan los cursos '
                },
                {
                    value: 65,
                    label: 'Realizar apoyo técnico y/o profesional en  diseño gráfico, para la realización de piezas graficas y animaciones del  proceso de Virtualidad'
                },
                {value: 66, label: 'Realizar apoyo técnico y/o profesional al proceso de  Virtualidad de cara al ensamble y diseño instruccional '},
                {value: 67, label: 'Realizar apoyo Profesional  con experiencia en instalación y migración de sistemas o plataformas tipo Moodle'},
                {value: 68, label: 'Realizar apoyo logistico y administrativo en el Centro de Lenguas'},
                {value: 69, label: 'Participar de membresias organizaciones nacionales e internacionales .  Fac. Administración.'},
                {value: 70, label: 'Realizar acompañamiento al desarrollo de las estrategias académicas desarrolladas   de la Fac. Administación'},
                {value: 71, label: 'Realizar apoyo logistico y administrativo en la Facultad de Administración.'},
                {
                    value: 72,
                    label: 'Realizar la compra de materia prima como alimentos, granos, cereales, proteinas animales y vegetales y elementos de aseo para gastronomia.  Fac. Administración '
                },
                {
                    value: 73,
                    label: 'Realizar mantenimiento  (preventivo , correctivo)  de las aulas moviles, economato  y laboratorios de los programas de Gastronomia  Fac. Administración. '
                },
                {value: 74, label: 'Realizar salidas académicas regionales y nacionales. Facultad de Administración.'},
                {
                    value: 75,
                    label: 'Prestar el servicio de acompañamiento a los procesos administrativos y logisticos de la Facultad de Arquitectura e Ingenieria. '
                },
                {value: 76, label: 'Participar de redes y   membresias   Fac. Arquitectura e ingenieria.'},
                {value: 77, label: 'Realizar mantenimiento a los  laboratorios (preventivo , correctivo) , facultad de Arquitectura e Ingenieria'},
                {
                    value: 78,
                    label: 'Realizar salidas académicas  asociadas a los curriculos de los programas y visitas de  practicas   de la facultad de Arquitectura e Ingenieria'
                },
                {value: 79, label: 'Participar en membresias  u organizaciones académicas . Fac Salud'},
                {value: 80, label: 'Prestar los servicios como ingeniero biomédico para la Facultad de Ciencias de la Salud. '},
                {value: 81, label: 'Prestar los servicios como químico para la Facultad de Ciencias de la Salud. '},
                {
                    value: 82,
                    label: 'Realizar diagnóstico y asesoría en biotecnología, como parte de las actividades de extensión de la Facultad de  Ciencias de la Salud (biotecnología). '
                },
                {value: 83, label: 'Realizar apoyo operativo y administrativo en los laboratorios  en la  facultad de Ciencias de la Salud'},
                {value: 84, label: 'Realizar calibración de equipos. '},
                {value: 85, label: 'Realizar la compra de insumos, químicos, reactivos y kits de  diagnóstico'},
                {value: 86, label: 'Realizar mantenimiento y reparación de equipos '},
                {value: 87, label: 'Realizar salidas académicas regionales y nacionales. Facultad de Salud'},
                {
                    value: 88,
                    label: 'Prestar servicio de hospedaje para las salidas academicas  y  Actividades de trabajo de campo para recolección de datos, toma de muestras. Fac sociales'
                },
                {value: 89, label: 'Realizar apoyo técnico y/o profesional a la facultad de Ciencias Sociales y de educación'},
                {value: 90, label: 'Realizar afiliaciones a redes académicas. facultad de Ciencias Sociales y de educación '},
                {
                    value: 91,
                    label: 'Realizar apoyo al desarrollo de estrategias que favorezcan a los  procesos academicos  de la Facultad de Ciencias Sociales y de Educación'
                },
                {
                    value: 92,
                    label: 'Realizar movilidad académicas hacia diferentes experiencias representativas para los programas. facultad de Ciencias Sociales y de educación'
                },
                {
                    value: 93,
                    label: 'Realizar apoyo al desarrollo de estrategias que favorezcan a los procesos académicos de la institución. Vicerrectoría Académica'
                },
                {value: 94, label: 'Realizar mantenimiento y reparación de equipos'},
                {value: 95, label: 'Realizar calibración de equipos. LACMA'},
                {value: 96, label: 'Prestar servicios de LACMA en el Valle de Aburra y municipios cercanos al departamento de Antioquia.'},
                {value: 97, label: 'Participar en ensayos interlaboratorios de calidad externos'},
                {value: 98, label: 'Prestar servicios profesionales en el laboratorio LACMA'},
                {value: 99, label: 'Realizar el apoyo administrativo, técnico y logístico  en el proceso de Tecnologia y medios audiovisuales'},
                {value: 100, label: 'Realizar la dotación de infraestructrua tecnologica requerida'},
                {value: 101, label: 'Realizar mantenimiento de infraestructrua tecnologica requerida'},
                {value: 102, label: 'Dotar y actualizar aplicativos de software de uso institucional'},
                {value: 103, label: 'Realizar la Formulación del plan maestro integral de infraestructura física institucional'},
                {value: 104, label: 'Mejorar Infraestructura física de la Institución Universitaria Colegio Mayor de Antioquia'},
                {value: 105, label: 'Realizar mejoramiento  y adecuación de de las áreas de estudio de los bloques institucionales'},
                {value: 106, label: 'Realizar mejoramiento y recuperación de las zonas verdes de la institución'},
                {value: 107, label: 'Realizar el apoyo administrativo, técnico y logístico al  proceso de Infraestructura Fisica '},
                {value: 108, label: 'Realizar el apoyo profesional al proceso de Infraestructura Fisica '},
                {value: 109, label: 'Realizar la automatización de puertas principales.'},
                {value: 110, label: 'Reserva Realizar la Formulación del plan maestro integral de infraestructura física intitucional'},
                {value: 111, label: 'Realizar adecuaciones electricas para el fortalecimiento de la infraestructura'},
                {
                    value: 112,
                    label: 'Realizar montajes, instalaciones y las adecuaciones  requeridas dentro de la infraestructura física de la institución'
                },
            ],
            //selects
            categoria: [
                {label: "Area Protegida", value: 1},
                {label: "ARL", value: 2},
                {label: "Bases de Datos", value: 3},
                {label: "Calibraciones", value: 4},
                {label: "Caja menor", value: 5},
                {label: "Contratista", value: 6},
                {label: "Cuota de fiscalizacion", value: 7},
                {label: "Edictos", value: 8},
                {label: "Equipos e intrumentos", value: 9},
                {label: "Estimulos docentes", value: 10},
                {label: "Eventos", value: 11},
                {label: "Exemenes medicos", value: 12},
                {label: "Gastos de viaje alimentacion", value: 13},
                {label: "Gastos de viaje hospedaje", value: 14},
                {label: "Gastos de viaje trasnporte", value: 15},
                {label: "Gastos legales", value: 16},
                {label: "GMF", value: 17},
                {label: "Inscripciones,afiliaciones y renovaciones", value: 18},
                {label: "Insumos", value: 19},
                {label: "Instalaciones y reparaciones", value: 20},
                {label: "Licencias y membresias", value: 21},
                {label: "Mantenimiento", value: 22},
                {label: "Mobiliario", value: 23},
                {label: "Movilidad academica", value: 24},
                {label: "Patentes", value: 25},
                {label: "Plan de capacitacion", value: 26},
                {label: "Plan de comunicación y mercado y plan de medios", value: 27},
                {label: "Plataforma de envío masivo de correos, sms, ivr y recolección de leads e interesados", value: 28},
                {label: "Practicante", value: 29},
                {label: "Publicaciones y procesos editoriales", value: 30},
                {label: "Regionalizacion alimentacion", value: 31},
                {label: "Regionalizacion hospedaje", value: 32},
                {label: "Regionalizacion transporte", value: 33},
                {label: "Seguros y polizas", value: 34},
                {label: "Seguridad alimentaria", value: 35},
                {label: "Servicio de impresoras y de fotocopias", value: 36},
                {label: "Servicio de mensajeria", value: 37},
                {label: "Servicios de auditoria y acreditacion", value: 38},
                {label: "Servicios publicos", value: 39},
                {label: "Software", value: 40},
                {label: "Soporte", value: 41},
                {label: "Souvenirs", value: 42},
                {label: "Subvencion", value: 43},
                {label: "Suministro de comidad y refrigerios", value: 44},
                {label: "Telefoniacelular e internet", value: 45},
                {label: "Viaticos", value: 46}
            ],
            vigencias_anteriores: [
                {
                    'label': "Si",
                    'value': "Si"
                },
                {
                    'value': "No",
                    'label': "No",
                }
            ],
        }
    },
    watch: {
        infoEnviada(newVal) {
            if (newVal) {
                this.recuperarTitulo()
            }
        }
    },
    computed: {
        Eltotal() {
            this.infoEnviada.forEach(element => {
                this.totalnecesidades += parseInt(element.valor_total_solicitatdo_por_necesidad)
            });
            this.titulote = this.proceso_que_solicita_presupuesto.find((item) => item.value == this.infoEnviada[0]?.proceso_que_solicita_presupuesto)?.label
            return this.totalnecesidades
        },
    },
    
    props: {
        identificacion_user: String,
        infoEnviada: Object,
        lista_pros_presupuestp:Object
    },
    methods: {
        number_format,
        recuperarTitulo(){
            this.titulote = this.proceso_que_solicita_presupuesto.find((item) => item.value == this.infoEnviada[0]?.proceso_que_solicita_presupuesto)?.label
        },
        ProInvolucrados(texto) {
            if (!texto || texto.length < 1) return ''
            let Arraytexto = texto.split(',')
    
            let result = []
            Arraytexto.forEach(element => {
                let busqueda = (this.lista_pros_presupuestp.find((item) => item.value == element))
                result.push(busqueda.label)
            });
            return result.join(', ');
        },
        Pmejoramientonecesidad(texto) {
            if (!texto || texto.length < 1) return ''
            let Arraytexto = texto.split(',')
    
            let result = []
            Arraytexto.forEach(element => {
                let busqueda = (this.planmejoramientonecesidad.find((item) => item.value == element))
                result.push(busqueda.label)
            });
            return result.join(', ');
        },
        LineaPlan(texto) {
            if (!texto || texto.length < 1) return ''
            let Arraytexto = texto.split(',')
    
            let result = []
            Arraytexto.forEach(element => {
                let busqueda = (this.lineadelplan.find((item) => item.value == element))
                result.push(busqueda.label)
            });
            return result.join(', ');
        },
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