<?php
session_start();

$flag = true;
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["idUsuario"])) {
    $flag = false;
}
// Si el usuario está autenticado, obtén su ID de usuario
$idUsuario = isset($_SESSION["idUsuario"]) ? $_SESSION["idUsuario"] : 1;

// Obtén la página actual y la dirección IP del usuario
$paginaVisitada = $_SERVER["REQUEST_URI"];
$direccionIP = $_SERVER["REMOTE_ADDR"];

// Crea una conexión a la base de datos (reemplaza con tus propios datos)
$conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

$consulta = "INSERT INTO registroactividad (idUsuario, fechaHora, paginaVisitada, direccionIP) VALUES (?, NOW(), ?, ?)";
$stmt = $conexion->prepare($consulta);
$stmt->bind_param("iss", $idUsuario, $paginaVisitada, $direccionIP);

$query = "SELECT idCancha, descripcion FROM cancha";
$resultado = $conexion->query($query);

$stmt->execute();

// Cierra la consulta y la conexión
$stmt->close();
$conexion->close();

// Función para validar un número de teléfono de Guatemala
function validarNumeroGuatemala($numero) {
    // Expresión regular para validar un número de teléfono de Guatemala
    $patron = "/^(?:502)?\s*(?:\d\s*){8}$/";

    // Utiliza la función preg_match para comprobar si el número coincide con el patrón
    return preg_match($patron, $numero);
}
$nombre = "";
$telefono = "";
$hora_inicio = "";
$fecha = "";
$email = "";
$tipoCancha = "";
$idCliente = "";
$idC = "";
// Verifica si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conexion2 = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");
    $conexion3 = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");
    // Obtén el número de teléfono del formulario
    //$telefono = $_POST["telefono"];
  //  $email = $_POST["email"];
    //$telefono = str_replace(' ', '', $_POST["telefono"]);
    
    $nombre = $_POST["nombre"];
    $telefono = str_replace(' ', '', $_POST["telefono"]);
    $hora_inicio = $_POST["hora_inicio"];
    $fecha = $_POST["fecha"];
    $email = $_POST["email"];
    $tipoCancha = $_POST["tipoCancha"];
    
    $consultaCliente = "SELECT idCliente FROM cliente WHERE telefono = ? OR correo = ?";
        $stmtCliente = $conexion2->prepare($consultaCliente);
        $stmtCliente->bind_param("ss", $telefono, $email);
        $stmtCliente->execute();
        $stmtCliente->bind_result($idCliente);
        //echo $idCliente;
        //$stmtCliente->store_result();
        
        

        $inserta = "INSERT INTO cliente ( nombre, correo, telefono) VALUES (?, ?, ?)";
        $stmtCliente2 = $conexion3->prepare($inserta);
        $stmtCliente2->bind_param("sss", $nombre,$email, $telefono);
        

    // Valida el número de teléfono
    if (!validarNumeroGuatemala($telefono)) {
        $mensajeError = "El número de teléfono no es válido. Por favor, introduce un número de teléfono de Guatemala válido.";
    } else {
        
        // Verificar si el teléfono o correo ya existen en la tabla Cliente
        if ($stmtCliente->fetch()) {
            // El correo o teléfono existe en la tabla cliente, y $idCliente contiene el valor
            //echo "entro";
            //echo $idCliente;
            $idC = $idCliente;
        } else {
            // El correo o teléfono no existe en la tabla cliente
          //  $mensajeError = "El número de teléfono o correo electrónico ya están registrados en la base de datos." . $idCliente;
            //Inserta el cliente nuevo
            $stmtCliente2->execute();
            $stmtCliente2->store_result();
            $idC = $stmtCliente2->insert_id;
            
        }
        
      /*  if ($stmtCliente->num_rows === 0) {
           // $stmtCliente->bind_result($idCliente); // Vincular el resultado a una variable
           // $stmtCliente->fetch(); // Obtener el valor de idCliente
    
            // El teléfono o correo ya existen en la tabla Cliente
           
        } else {

            
        }*/
    }
    $idHorario = "";
    //Sigue el proceso de creacion
    if (empty($mensajeError)) {
        //No hay error y procede
        $conexion5 = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco"); 
        $sql_tarifa = "SELECT idTarifa
               FROM tarifa
               WHERE idCancha = '$tipoCancha' and '$hora_inicio' BETWEEN horaIni AND horaFin";

        $result_tarifa = $conexion5->query($sql_tarifa);

        if ($result_tarifa->num_rows > 0) {
            $row_tarifa = $result_tarifa->fetch_assoc();
            $idTarifa = $row_tarifa["idTarifa"];

            // Paso 2: Buscar idHorario en la tabla horario usando idTarifa
            $sql_horario = "SELECT idHorario
                            FROM horario
                            WHERE idCancha = $tipoCancha 
                            AND   idTarifa = $idTarifa";

            $result_horario = $conexion5->query($sql_horario);

            if ($result_horario->num_rows > 0) {
                $row_horario = $result_horario->fetch_assoc();
                $idHorario = $row_horario["idHorario"];
             //   echo "El idHorario es: " . $idHorario;
            } else {
             //   echo "No se encontró un idHorario para la hora especificada.";
            }
        } else {
           // echo "No se encontró una tarifa para la hora especificada.";
        }



        // Sumar 1 hora
        $hora_fin = date("H:i:s", strtotime($hora_inicio . " +1 hour"));
        $estadoReserva = 1;
        //Nueva conexión para crear solicitudes de reservas
        $conexion4 = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");
        $inserta2 = "INSERT INTO reserva ( idUsuario, idHorario, idCliente, idEstadoReserva, fechaReserva, time_from, time_to, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtCliente3 = $conexion4->prepare($inserta2);
        $stmtCliente3->bind_param("iiiissss", $idUsuario,$idHorario, $idC,$estadoReserva,$fecha,$hora_inicio,$hora_fin, $fecha);
        $stmtCliente3->execute();
        $stmtCliente3->store_result();
        $idS = $stmtCliente3->insert_id;
        $stmtCliente3->close();
        $conexion4->close();
        $conexion5->close();

            
        $nombre = "";
        $telefono = "";
        $hora_inicio = "";
        $fecha = "";
        $email = "";
        $tipoCancha = "";
        $mensajeExito = "Se ha generado tu solicitud de reservacion con número: " . $idS;
    }
    $stmtCliente2->close();
    
    $stmtCliente->close();
    $conexion2->close();
    $conexion3->close();
    
}
date_default_timezone_set('America/Guatemala'); // Establece la zona horaria a la de Guatemala o la que necesites

$fecha = (!empty($fecha)) ? $fecha : date('Y-m-d');

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
    <link rel="icon" href="/img/logo.png" type="image/x-icon">

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
        }

        .container2 {
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
            max-width: 800px; /* Cambio: Limitar el ancho del contenedor */
            margin: 0 auto; /* Centrar el contenedor */
        }

        .container-content {
        }

        form {
            margin-top: 20px;
        }
        button.btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light cust" style="z-index: 1000;" >
  <div class="container">
    <a class="navbar-brand mx-auto" href="index.php"><img src="img/logo.png" alt="Logo" class="img-fluid me-2" style="max-width: 50px;"></a>
    <button class="navbar-toggler btn-orange" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <?php 
        if (!isset($_SESSION["idUsuario"])) {
         
        ?>

      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="index.php#SobreNosotros">Sobre Nosotros</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#Servicio">Servicios</a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="index.php#Contacto">Contacto</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#Tarifa">Tarifas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index.php#Disponibilidad">Disponibilidad</a>
          </li>
        </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="login.php">Login</a>
            </li>
          </ul>
        <?php }else{?>
            <ul class="navbar-nav">
            <li class="nav-item dropdown"> <!-- Agrega un elemento de menú desplegable -->
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Reportes
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="/admin/reporte_actividad.php">Reporte de visitas</a>
          <a class="dropdown-item" href="/admin/reportes_reservas.php">Reportes de reservas</a>
          
          </div>
        </li>
        
        <?php
        
        if (isset($_SESSION["idRol"]) && $_SESSION["idRol"] !== 5) {
      ?>
       <li class="nav-item dropdown"> <!-- Agrega un elemento de menú desplegable -->
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Reservas
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="/reserva.php">Realizar reserva</a>
          <a class="dropdown-item" href="/admin/reservas_aceptadas.php">Reservas aceptadas</a>
          <a class="dropdown-item" href="/admin/reservas_pendientes.php">Reservas pendientes</a>
          </div>
        </li>
        
        <?php
        
        }
        ?>
       
        <?php
        
          if (isset($_SESSION["idRol"]) && $_SESSION["idRol"] === 1) {
        ?>
        <li class="nav-item dropdown"> <!-- Agrega un elemento de menú desplegable -->
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Mantenimiento
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            
            <a class="dropdown-item" href="/admin/rol.php">Roles</a>
            <a class="dropdown-item" href="/admin/tipo_cancha.php">Tipo de canchas</a>
            <a class="dropdown-item" href="/admin/tipo_anticipo.php">Tipo de anticipos</a>
            <a class="dropdown-item" href="/admin/cancha.php">Cancha</a>
            <a class="dropdown-item" href="/admin/tarifa.php">Tarifa</a>
            <a class="dropdown-item" href="/admin/estado_reserva.php">Estado Reserva</a>
            <a class="dropdown-item" href="/admin/cliente.php">Cliente</a>
            <a class="dropdown-item" href="/admin/horario.php">Horario</a>
            <a class="dropdown-item" href="/admin/usuario.php">Usuario</a>
          </div>
        </li>
        <?php }?>
        
        <li class="nav-item">
          <a class="nav-link" href="/admin/cerrar_sesion.php">Cerrar Sesión</a>
        </li>
        <!--
        <li class="nav-item">
          <a class="nav-link" href="/admin/solicitud-cambio-contrasenia.php">Cambiar contraseña</a>
        </li>-->
      </ul>
        <?php }?>
    </div>
  </div>
</nav>
    <section class="container2 my-5" >
        <div class="row">
            <div class="container">
                <div class="container-content">
                    <div class="text-center">
                        <h1>Solicitud de reservación</h1>
                        <img src="/img/logo.png" alt="Logo" class="img-fluid" width="100">
                    </div>
                    <form method="post">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de quien reserva</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Escribe tu nombre" value="<?php echo $nombre; ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                                <select class="form-select" id="hora_inicio" name="hora_inicio">
                                    <!-- Las opciones de hora se generan automáticamente a través de JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $fecha; ?>" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Escribe tu correo" value="<?php echo $email; ?>" >
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Número de Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono"  value="<?php echo $telefono; ?>" placeholder="Escribe tu número de teléfono" required>
                        </div>
                        <?php 
                            if ($resultado->num_rows > 0) {
                                // Crear el elemento select con las opciones de la base de datos
                                echo '<div class="mb-3">';
                                echo '<label for="tipoCancha" class="form-label">Tipo de Cancha</label>';
                                echo '<select class="form-select" id="tipoCancha" name="tipoCancha">';
                            
                                while ($fila = $resultado->fetch_assoc()) {
                                    $idCancha = $fila["idCancha"];
                                    $descripcion = $fila["descripcion"];
                                    echo '<option value="' . $idCancha . '">' . $descripcion . '</option>';
                                }
                            
                                echo '</select>';
                                echo '</div>';
                            } else {
                                echo "No se encontraron resultados en la tabla 'cancha'.";
                            }
                            
                            // Cerrar la conexión a la base de datos
                            //$conexion->close();
                            ?>
                        
                        
                       <!-- <div class="mb-3">
                            <label for="tipoCancha" class="form-label">Tipo de Cancha</label>
                            <select class="form-select" id="tipoCancha">
                                <option value="Futbol5">Futbol 5</option>
                                <option value="Futbol7">Futbol 7</option>
                                <option value="Tenis">Tenis</option>
                            </select>
                        </div> -->
                        
                        <button type="submit" class="btn btn-primary btn-orange">Guardar</button>
                    </form>
                    <?php
                    // Mostrar mensajes de éxito o error
                    if (isset($mensajeExito)) {
                        echo '<div class="alert alert-success" role="alert">' . $mensajeExito . '</div>';
                    }
                    if (isset($mensajeError)) {
                        echo '<div class="alert alert-danger" role="alert">' . $mensajeError . '</div>';
                    }
                    // Mostrar la hora local del cliente
                    echo '<p>Hora local del cliente: <span id="horaLocal"></span></p>';
                    ?>
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

    <script>
    // Obtener la hora local del cliente y mostrarla
    function mostrarHoraLocal() {
        const horaLocal = new Date().toLocaleTimeString();
        document.getElementById("horaLocal").textContent = horaLocal;
    }

    // Llamar a la función para mostrar la hora local al cargar la página
    mostrarHoraLocal();

    // Agregar un temporizador para actualizar la hora local cada segundo
    setInterval(mostrarHoraLocal, 1000);
    
    // Función para actualizar las opciones de hora según la fecha seleccionada
    function actualizarHoras() {    
        let vl = "";
        vl = "<?php echo $hora_inicio; ?>";
        let f1 = "<?php echo $fecha; ?>"
        //console.log(vl);
        const fechaSeleccionada = new Date(document.getElementById("fecha").value);

        const horaInicioSelect = document.getElementById("hora_inicio");

        // Calcula la hora de inicio permitida (si la fecha es hoy)
        const horaActual = new Date().getHours();
        let horaInicioPermitida = "";
        
            horaInicioPermitida = 5; // Hora de inicio predeterminada
            let ft = new Date();
        ft.setDate(ft.getDate() - 1);
        //new Date().toDateString()
        if (fechaSeleccionada.toDateString() === ft.toDateString()) {
            horaInicioPermitida = (horaActual < 23) ? (horaActual + 1) : 23;
        }
        //console.log(fechaSeleccionada.toDateString());
        

        //console.log(ft);
        //console.log(horaInicioPermitida);
        // Limpia las opciones actuales
        horaInicioSelect.innerHTML = '5';

        // Agrega las nuevas opciones
        for (let hora = horaInicioPermitida; hora <= 23; hora++) {
            const horaFormateada = hora.toString().padStart(2, '0');
            const option = new Option(`${horaFormateada}:00`, `${horaFormateada}:00`);
            horaInicioSelect.appendChild(option);
        }
    }

    // Agregar un evento de cambio al campo de fecha para actualizar las horas
    document.getElementById("fecha").addEventListener("change", actualizarHoras);

    // Llamar a la función para configurar las horas iniciales al cargar la página
    actualizarHoras();
    </script>
</body>
</html>
