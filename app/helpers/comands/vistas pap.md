//misterdebug - crud-generator-laravel
(example-project: tomag)


//INICIO DEL PROYECTO
// 9JULIO2024
php artisan make:crud Inspeccion "codigo:string,campo1:string,campoAprobo:boolean"
php artisan make:crud AreaInspeccion "nombre:string"
php artisan make:crud Responsable "nombre:string,cargo:string,firma:text" // BORRADO!!
php artisan make:crud aspecto "nombre:string,categoria:string"

//myGeneric
php artisan copy:u Inspeccion
php artisan copy:u AreaInspeccion
php artisan copy:u Aspecto
php artisan copy:u Seguimiento

//muchos a muchos
php artisan make:migration pivot_aspecto_inspeccion_table


//just model
php artisan make:model Generico --all
php artisan make:model aspecto_inspeccion --all
php artisan make:model area_inspeccion --all
//control
php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer"
//its donest need a Model -> materia_user



//#Utilidades
para borrar:  php artisan rm:crud post --force
    php artisan rm:crud centroTrabajo --force
//#laravel excel
php artisan make:0import PersonalImport --model=User
php artisan make:0import PersonalUniversidadImport --model=User

php artisan make:export TodaBDExport

// node
vue-datapicker
// laravel
composer require maatwebsite/excel

						
						

CONDICIONES LOCATIVAS						
Los pisos, las ventanas, muros y techos se encuentran en buen estado 						
La escaleras se encuentra en buen estado (peldaños con cinta antideslizante, huellas y pasamanos)						
El espacio de trabajo es suficiente para movilización de personas, objetos y materiales						
Las áreas de trabajo se encuentran señalizadas y demarcadas						
Se realiza uso eficiente de la energía, el agua y los recursos naturales						
RIESGO ERGONÓMICO						
Se cuenta con ayudas mecánicas para el levantamiento, desplazamiento y transporte de materiales e insumos y estas son utilizadas correctamente.						
Las actividades generan sobreesfuerzos por levantamiento, desplazamiento, almacenamiento y transporte de materiales e insumos, se aplican técnicas de higiene postural.						
La  altura de la silla es graduable  y cuenta con reposa brazos o tiene donde apoyar los brazos						
Los antebrazos quedan paralelos al suelo y las muñecas no se doblan						
La zona lumbar cómodamente apoyada						
Los pies quedan de forma plana sobre el suelo (con o sin reposapiés)						
La pantalla del computador esta vertical que no refleja puntos de luz						
El Monitor del computador esta a una distancia entre 60 y 80 cm						
La línea superior de la pantalla del computador  no excede la altura de los ojos						
Los puestos de trabajo tienen el mouse y el teclado en una misma superficie						
El puesto de trabajo dispone de una superficie  mínima por trabajador de dos (2) metros cuadrados.						
Las sillas (Rodachinas, cojinería, sistemas mecánicos) se encuentran en buen estado						
Se cuenta con descansa pies en todos los puestos de trabajo que lo requieren						
CONDICIONES DE HIGIENE Y SEGURIDAD						
Los niveles de temperatura del área de trabajo están entre 18 y 23° C						
En el área se cumple con los niveles de confort de ruido						
La iluminación del área de trabajo es adecuada para el tipo de labor allí realizada (El numero de luminarias es suficiente, están paralelas al plano de trabajo, se encuentran en buen estado, se  les realiza limpieza de forma regular)						
"El área cuenta con persianas o cortinas para controlar la cantidad de luz natural que ingresa al área.
(Validar en los casos en que aplique)"						
En el área de trabajo se tiene presencia de luz natural y luz de apoyo						
"El área cuenta con buena ventilación.
Se cumple con niveles de temperatura confortables."						
"El área cuenta con sistema de aire acondicionado, y este funciona correctamente.
Se tienen registros del mantenimiento a los ductos del sistema de aire acondicionado"						
"El área cuenta con suministro de agua potable.
El tanque de almacenamiento de agua esta protegido y es de capacidad suficiente y se limpia  y desinfecta periódicamente."						
"El área cuenta con sistemas de extracción de gases y vapores, estos funcionan correctamente.
(verificar en los casos en que aplique (laboratorios, cocinas)"						
Existe señalización de riesgos y de los elementos de protección personal - EPP que se deben utilizar en el área.  						
RIESGO ELÉCTRICO						
Los cables eléctricos se encuentra protegidos y canalizados 						
Todas las instalaciones eléctricas cuentan con interruptores y tomacorrientes, sin sobrecarga y en buen estado.						
El cable de las extensiones eléctricas se encuentran en buen estado, sin uniones y aisladas.						
Los tableros eléctricos, toma corrientes, cajas de Breakes o fuentes de energía, se encuentran demarcados y señalizados con el voltaje correspondiente.						
ORDEN Y ASEO						
Aplica para oficinas y áreas en general.						
Las oficinas y puestos de trabajo se encuentran debidamente ordenados y aseados (incluye estantes, cajones, paredes, detrás y debajo de mesas, escritorios, cables y equipos).						
Las Impresoras, fotocopiadoras y equipos de computo están limpios y en buen estado.						
Los puestos de trabajo cuenta con cajoneras donde el personal pueda guardar sus objetos personales.						
Las puertas de gabinetes, cajoneras, archivadores están cerradas y/o ajustados.						
"Los equipos y utensilios de trabajo se almacenan correctamente. 
El lugar de almacenamiento se encuentra rotulado (en los casos o áreas en los que aplique)"						
Los pisos están limpios y libres de regueros y obstáculos.						
La superficie y  los elementos para calentar y consumir los alimentos son higiénicos.						
El lugar de almacenamiento de los implementos de aseo está limpio. Los elementos de aseo se guardan limpios. 						
Se cuenta con puntos ecológicos dotados con recipientes con bolsa y tapa, del color correspondiente con la clase de residuo a depositar en ellos (reciclables, no reciclables y peligrosos), adecuados, suficiente, bien ubicados  e identificados para la recolección.						
Se realiza una adecuada separación y disposición de los residuos, de acuerdo al recipiente que le corresponde.						
Después de desocupados los recipientes se lavan y se desinfectan antes de ser colocados en el sitio respectivo.						
La empresa de aseo realiza la recolección de residuos con la frecuencia necesaria acorde a las normas vigentes evitar generación de olores, molestias sanitarias al entorno, contaminación de los productos, superficies y proliferación de plagas.						
Se esta desarrollando el programa de orden y aseo						
Aplica para cafeterías y laboratorios de gastronomía						
El personal encargado de la manipulación de alimentos mantiene las manos limpias, sin joyas, uñas cortas, y sin esmalte.						
Cuentan con cronograma de limpieza de aseo de hornos microondas, nevera, refrigerador y estufas.						
Las campanas de extracción se encuentran en buen estado y se tiene registros documental de mantenimiento.						
Se realiza operaciones de limpieza y desinfección en todas las áreas, utensilios, equipos, superficies  que entran en contacto con los alimentos a través  de métodos adecuados (químicos, físicos)						
El lavado de utensilios se realiza con agua potable corriente, jabón, o detergente y cepillo, en especial en donde se pican o fraccionan alimentos, los cuales están en buenas condiciones,  de conservación de higiene.						
El menaje y demás utensilios de cocina se encuentran limpios y ordenados 						
La greca o cafetera esta a una altura que facilita el llenado y manipulación.						
La zona alrededor de grecas, cafeteras, hornos y neveras están limpias y se evita el rebose de los recipientes. 						
Los mesones y mesas están limpios y aseados 						
"Las superficies para el picado son de material sanitario (plástico, nylon, polietileno, o teflón).
Los Equipos y superficies  en contacto con los alimentos están fabricados con materiales inertes, no tóxicos, resistentes a la corrosión, de fácil limpieza y desinfección."						
Los sitios de almacenamiento se encuentran debidamente aseados y rotulados (incluye estantes, cajones, paredes, detrás de mesas, cables, equipos).						
La vajilla esta almacenada en gabinetes y soportes adecuados que impiden la caída de los objetos.						
La materias primas o alimentos sin procesar se reciben en un lugar limpio y protegidos del medio ambiente.						
Los productos se encuentran en condiciones de conservación requeridas (congelación, refrigeración, medio ambiente).						
Las hortalizas y verduras que se consumen crudas se lavan y desinfectan con sustancias permitidas.						
Se lavan los alimentos o materias primas crudas como carne, verduras, hortalizas, y productos hidrobiológicos con agua potable corriente antes de la preparación.						
Los alimentos crudos, (cárnicos, lácteos, pescados), se almacenan separadamente de los cosidos o preparados de tal manera que se evite la contaminación cruzada.  						
Los productos susceptibles de contaminar o de ser contaminados como: leche y sus derivados, carnes y sus derivados, preparados, productos de pesca, se encuentran debidamente almacenados, en recipientes separado bajo condiciones de refrigeración y/o congelación adecuadas, para evitar la contaminación cruzada  y proceden de proveedores que garanticen su calidad.						
El servicio de los alimentos se realiza con utensilios adecuados y se evita el contacto con las manos.						
Los alimentos preparados para consumo inmediato que no se consumen dentro de las 24 horas siguientes son desechados.						
Los alimentos y bebidas expuestos a la venta están en vitrinas, campanas plásticas, o cualquier otro sistema que las proteja del medio exterior. 						
Cuando se requiere, el establecimiento dispone de utensilios desechables (cubiertos, platos, vasos, etc.)						
El persona que realiza la manipulación de alimentos utiliza uniformes adecuados de color claro, limpio y calzado cerrado.						
Los manipuladores se lavan y desinfectan las manos hasta el codo cada vez que sea necesario.						
El personal del área evita practicas antihigiénicas tales como rascarse, toser, escupir, etc.						
El personal que realiza manipulación de alimentos cuenta con la Certificación como "Manipulador de alimentos"						
El proceso de expendio y venta al consumidor se realiza en forma sanitaria						
Se realiza una adecuada separación en la fuente de los residuos sólidos (Orgánicos, recuperables)						
Los recipientes para residuos se encuentran cerca de la fuente de generación, limpios, tapados y señalizados.						
Los productos que lo requieren tienen, registro sanitario, se encuentran dentro de su vida útil y son aptos para consumo humano						
Los productos se encuentran rotulados y envasados de conformidad con las normatividades sanitarias vigentes.						
RIESGO BIOLOGICO						
Se cuenta con control de vacunación del personal expuesto						
Se cuenta con protocolo de manejo de sustancias biológicas y el personal lo conoce						
RIESGO QUÍMICO						
Esta documentada y publicada la tabla de compatibilidad de los productos químicos almacenados en el área.						
Los productos químicos son almacenados en lugares asignados y según la tabla de compatibilidades.						
Están disponibles en físico las hojas de seguridad y tarjetas de emergencia de las sustancias químicas almacenadas.						
Los recipientes de almacenamiento de sustancias químicas están debidamente rotulados y etiquetados, y los envases cumplen con las especificaciones para manejo seguro (Según lo establece el Sistema Globalmente Armonizado y el Manual de Riesgo Químico)						
La ventilación e iluminación adecuada en las área de almacenamiento de sustancias químicas.						
Los residuos generados en el laboratorio se encuentran debidamente etiquetados y/o rotulados y dispuestos en el centro de acopio.						
Los residuos químicos almacenados en el centro de acopio cuentan con tarjeta de emergencia.						
VERTIMIENTOS						
Se cumple con el control de  vertimientos al sistema de alcantarillado publico (cumpliendo los procedimientos establecidos en el Manual de Riesgo químico)						
El manejo de los residuos líquidos dentro del establecimiento no representa riesgo de contaminación para los alimentos o para las   superficies que entra en contacto con estas.						
MAQUINAS Y EQUIPOS						
Aplica para laboratorios de salud						
El empaque de la tapa de los Autoclaves se encuentra en buenas condiciones 						
La válvula de seguridad de los Autoclaves fue calibrada en el ultimo año (verificar registro periodo no mayor a un año)						
El personal que manipula los Autoclaves cuenta con guantes de seguridad, trajes completos de seguridad petos de caucho y pantalla de protección facial resistentes a altas temperaturas que permitan proteger correctamente.						
Se cuenta con estándar de seguridad para la manipulación del Autoclave, se tiene publicado y debidamente señalizado.						
Aplica para taller de maderas, suelos, patio de construcción y taller de Infraestructura						
"Las maquinas, herramientas que se utilizan: esmeril, prensa, herramientas manuales, se encuentran en buenas condiciones.
Los cables de los equipo se encuentran en buen estado (sin empates).                    (Verificar en caso de que aplique)
Las válvulas y reguladores están en buen estado (sin golpes).  (Verificar en caso de que aplique)"						
"Las máquinas y equipos cuentan con guardas de seguridad, estas se encuentran en buen estado y son utilizadas. 
(Verificar en caso de que aplique)"						
Las maquinas, herramientas que se utilizan en el área se almacenan de manera adecuada. (De forma ordenada, en el lugar establecido)						
"Los cilindros están asegurados (con cadenas), almacenados en posición vertical, en un lugar aislado, ventilado, lejos de fuentes de ignición y  señalizados, los cilindros vacíos se almacenan en un lugar diferente al de los llenos.
Los lugares de almacenamiento de los cilindros se encuentran dentro de las zona de trabajo, están aislados por paredes construidas de materiales incombustibles, con salidas de emergencia.
(Verificar en caso de que aplique)"						
Cuando el personal del área manipula maquinaría y equipos que generen movimiento, están libres de elementos que puedan generar atrapamientos: Pulseras, anillos, relojes, cadenas, etc.						
"Las maquinas, equipos y herramienta se inspeccionan de manera periódica.
Existe un cronograma de inspección, se cuenta con registro de inspección.
(Verificar en caso de que aplique)"						
ELEMENTOS DE PROTECCION PERSONAL						
Se tienen identificadas los elementos de protección personas requeridos para las actividades - riesgos presentes en las actividades desarrolladas en el área.						
Se tiene documentada la necesidad de elementos de protección personal para las actividades del área en la Matriz elementos de protección personal.						
El personal del área tiene sabe cuales son los elementos de protección personal necesarios para las actividades - riesgos de las actividades que realiza.						
Existe señalización en el área de los riesgos y de los elementos de protección personal que deben utilizar.						
Se cuenta con registros de entrega de elementos de protección personal y capacitación sobre el uso de los mismos.						
Se requiere ropa especial para la ejecución de las actividades, los colaboradores hacen uso adecuado y se encuentra en buen estado. 						
Cuenta con calzado de seguridad adecuado para la ejecución de las actividades, se encuentra en buen estado y los colaboradores hacen uso adecuado de los mismos.						
Cuenta con gafas de seguridad adecuadas para la actividad, se encuentran en buen estado y se evidencia un adecuado uso de las mismas. 						
Se requiere el uso de careta facial para la ejecución de las actividades, se encuentra en buen estado y los colaboradores hacen uso adecuado de las mismas.						
Se cuenta con los guantes indicados para la actividad, se encuentran en buen estado y se hace uso adecuado por parte de los colaboradores. 						
Se cuenta con protección respiratoria de acuerdo a la actividad desarrollada, se encuentra en bien estado y se hace uso adecuado por parte de los colaboradores. 						
Se requiere protección auditiva para la ejecución de las actividades, se encuentra en buen estado y los colaboradores hacen uso adecuado de los mismos. 						
Se requiere casco de seguridad con barbuquejo para la ejecución de las actividades, se encuentra en buen estado y se hace uso adecuado por parte de los colaboradores. 						
Se requiere elementos de protección contra caídas para la ejecución de las actividades, se encuentran en buen estado y los colaboradores hacen uso adecuado de los mismos. 						
"El personal hace uso de elementos de protección personal - EPP adecuados para las actividades que ejecutan. 
(verificar registros de entrega de elementos de protección personal en caso de que aplique)."						
CONTROL DE EMERGENCIAS						
Se encuentran señalizadas las Salidas de Emergencias y Rutas de Evacuación						
El personal conoce  el procedimiento de notificación de emergencias, está publicada lista de entidades y teléfonos de emergencia.						
El personal identifica la señalización de salidas de emergencia , ruta de evacuación y puntos de encuentro y al coordinador de evacuación del área						
Se cuenta con extintores suficientes para ser utilizados en caso de emergencia, estos se encuentran señalizados y son de fácil acceso						
"El personal identifica  la ubicación de los extintores mas cercanos al área.
Los extintores ubicados en el área o mas cercanos, están ubicados en un lugar de fácil acceso, libre de obstáculos.
Se cuenta con señalización de ""Extintor""."						
"El área cuenta con botiquín de primeros auxilios, este se encuentra dotado, los elementos cuentan con fechas vigentes.
Se cuenta con señalización de ""botiquín de primeros auxilios""."						
El botiquín cuenta con rótulos y bolsa roja para residuos						
"El área cuenta con kit de atención de derrames, este se encuentra ubicado en un lugar de fácil acceso.
Se cuenta con señalización de ""Kit de derrames"""						
El kit de derrames cuenta con rótulos y bolsa roja para residuos						
Las duchas y lavaojos se encuentran funcionando correctamente, señalizadas y con especificaciones de uso						
Los pulsadores para emergencias funcionan correctamente, están señalizados.						
REGISTROS DOCUMENTALES						
Cuenta con registros de mantenimiento y calibración  de los equipos e instrumentos de trabajo.						
El personal cuenta con la documentación actualizada (concepto médico y vacunación según profesiograma, certificado de manipulación de alimentos)  						
El personal cuenta con la documentación actualizada (concepto médico según profesiograma, certificado de alturas)  						
El personal cuenta con la documentación actualizada (concepto médico y vacunación según profesiograma)  						
Se cuenta con registro de socializaciones programadas por SG - Ambiental y por SG- SST.						
Se cuentan con registros que soportan el  reporte de los residuos generados en los laboratorios (fisicoquímicos, biológicos, hospitalarios)						
Se cuenta con registros de fumigación realizada en las diferentes áreas (aplica para Infraestructura)						
