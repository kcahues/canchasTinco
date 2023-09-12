<?php
session_start();

$flag = true;
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["idUsuario"])) {
    $flag = false;
}
// Si el usuario está autenticado, obtén su ID de usuario
$idUsuario = isset($_SESSION["idUsuario"]) ? $_SESSION["idUsuario"] : null;

// Obtén la página actual y la dirección IP del usuario
$paginaVisitada = $_SERVER["REQUEST_URI"];
$direccionIP = $_SERVER["REMOTE_ADDR"];

// Crea una conexión a la base de datos (reemplaza con tus propios datos)
$conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

$consulta = "INSERT INTO registroactividad (idUsuario, fechaHora, paginaVisitada, direccionIP) VALUES (?, NOW(), ?, ?)";
$stmt = $conexion->prepare($consulta);
$stmt->bind_param("iss", $idUsuario, $paginaVisitada, $direccionIP);

$stmt->execute();

// Cierra la consulta y la conexión
$stmt->close();
$conexion->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reserva</title>
    <!-- Agregar los enlaces a las hojas de estilo de Bootstrap y Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="plugin/components/Font Awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="plugin/whatsapp-chat-support.css">
    <script src="/dist/index.global.min.js"></script>
    
    <!-- Sweetalert2 -->
<script src="/js/sweetalert2.all.min.js"></script>

<!-- Include FullCalendar JS & CSS library -->
<link href="/js/fullcalendar/lib/main.css" rel="stylesheet" />
<script src="/js/fullcalendar/lib/main.js"></script>
    

    <style>
        
        body {
            background: linear-gradient(45deg, #000 50%, #ff6600 50%);
            background-size: cover;
            background-attachment: fixed;
            color: #fff;
            /*display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;*/
        }

        .container2 {
            background-color: rgba(0, 0, 0, 0.7); /* Fondo del formulario transparente */
            backdrop-filter: blur(10px); /* Efecto de desenfoque para el fondo */
            padding: 20px;
            border-radius: 10px;
            
        }

        .container-content{
            /*margin-top: 300px;*/
        }

        form {
            margin-top: 20px;
        }
        button.btn-primary {
            width: 100%; /* Establece el ancho al 100% del contenedor padre (form) */
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark cust">
       <div class="container">
            <!-- Logo -->
            <a class="navbar-brand mx-auto" href="#">
                <img src="/img/logo.png" alt="Logo" class="img-flud me-2" width="50">
            </a>
            
            <!-- Toggler para dispositivos móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Opciones de navegación -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Acerca de</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li>
                </ul>
           </div>
        </div>
    </nav>
    <section class="container2 my-5" >
        
    <div class="row">
    <div class="container">
    <div class="container-content">
        <div class="text-center">
            <!-- Agregar tu logo aquí -->
            <h1>Solicitud de reservacion</h1>
            <img src="/img/logo.png" alt="Logo" class="img-fluid" width="100">
        </div>
        <form>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de quien reserva</label>
                <input type="text" class="form-control" id="nombre" placeholder="Escribe tu nombre">
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                    <select class="form-select" id="hora_inicio">
                        <option value="00">00:00</option>
                        <option value="01">01:00</option>
                        <option value="02">02:00</option>
                        <option value="03">03:00</option>
                        <option value="04">04:00</option>
                        <option value="05">05:00</option>
                        <option value="06">06:00</option>
                        <option value="07">07:00</option>
                        <option value="08">08:00</option>
                        <option value="09">09:00</option>
                        <option value="10">10:00</option>
                        <option value="11">11:00</option>
                        <option value="12">12:00</option>
                        <option value="13">13:00</option>
                        <option value="14">14:00</option>
                        <option value="15">15:00</option>
                        <option value="16">16:00</option>
                        <option value="17">17:00</option>
                        <option value="18">18:00</option>
                        <option value="19">19:00</option>
                        <option value="20">20:00</option>
                        <option value="21">21:00</option>
                        <option value="22">22:00</option>
                        <!-- Agrega más opciones según tus necesidades -->
                        <option value="23">23:00</option>
                    </select>
                </div>
                <div class="col" type="hidden">
                    <label for="hora_fin" class="form-label">Hora de Fin</label>
                    <input type="time" class="form-control" id="hora_fin" step="3600">
                </div>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" placeholder="Escribe tu correo">
            </div>
            <div class="mb-3">
                <label for="tipoCancha" class="form-label">Tipo de Cancha</label>
                <select class="form-select" id="tipoCancha">
                    <option value="Futbol5">Futbol 5</option>
                    <option value="Futbol7">Futbol 7</option>
                    <option value="Tenis">Tenis</option>
                </select>
            </div>
            <?php
// Verificar si el usuario ha iniciado sesión
if ($flag) {
?>
            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <div class="row">
                
                <div class="col-8">
                <select class="form-select" id="estado">
                    <option value="solicitado" style="background-color: purple; color: white;">Solicitado</option>
                    <option value="pendiente" style="background-color: orange; color: white;">Pendiente</option>
                    <option value="aprobado" style="background-color: green; color: white;">Aprobado</option>
                    <option value="cancelado" style="background-color: red; color: white;">Cancelado</option>
                </select>
                </div>
                <div class="col">
                <input type="text" class="form-control col-auto" id="estadoInput" readonly>
                </div>    
            </div>
            </div>
            <?php
// Verificar si el usuario ha iniciado sesión
}
?>
            <div class="mb-3">
                <label for="telefono" class="form-label">Número de Teléfono</label>
                <input type="tel" class="form-control" id="telefono" placeholder="Escribe tu número de teléfono">
            </div>
            <button type="submit" class="btn btn-primary btn-orange">Guardar</button>
        </form>
    </div>
    </div>
    </div>
    </section>
    <!-- Agregar los enlaces a las bibliotecas de Bootstrap y jQuery (necesarias para Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.min.js"></script>
    <button id="btnSubir" class="btn btn-custom btn-floating btn-back-to-top"><i class="fas fa-arrow-up"></i></button>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="plugin/components/jQuery/jquery-1.11.3.min.js"></script>
<script src="plugin/components/moment/moment.min.js"></script>
<script src="plugin/components/moment/moment-timezone-with-data.min.js"></script> <!-- spanish language (es) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // Función para validar la hora de inicio y fin
    function validarHoras() {
        // Obtén los valores de las horas de inicio y fin
        var horaInicio = document.getElementById("hora_inicio").value;
        var horaFin = document.getElementById("hora_fin").value;

        // Convierte los valores en objetos de fecha
        var fechaInicio = new Date("1970-01-01T" + horaInicio + ":00Z");
        var fechaFin = new Date("1970-01-01T" + horaFin + ":00Z");

        // Compara las fechas
        if (fechaInicio >= fechaFin) {
            // Muestra un mensaje de error
            alert("La hora de inicio debe ser menor que la hora de fin.");
            return false; // Evita que el formulario se envíe
        }

        return true; // Permite que el formulario se envíe si todo está correcto
    }

    // Agrega un evento de submit al formulario
    document.querySelector("form").addEventListener("submit", function (e) {
        if (!validarHoras()) {
            e.preventDefault(); // Evita que el formulario se envíe si la validación falla
        }
    });
        
        function actualizarColorDeFondo() {
            var estadoSeleccionado = document.getElementById("estado").value;
            var container = document.getElementById("estadoInput");

            switch (estadoSeleccionado) {
                case "solicitado":
                    container.style.backgroundColor = "purple";
                    break;
                case "pendiente":
                    container.style.backgroundColor = "orange";
                    break;
                case "aprobado":
                    container.style.backgroundColor = "green";
                    break;
                case "cancelado":
                    container.style.backgroundColor = "red";
                    break;
                default:
                    container.style.backgroundColor = "transparent";
                    break;
            }
        }

        // Agrega un evento de cambio al campo de selección
        document.getElementById("estado").addEventListener("change", actualizarColorDeFondo);

        // Llama a la función para establecer el color de fondo inicial
        actualizarColorDeFondo();
    
</script>
</body>

</html>
