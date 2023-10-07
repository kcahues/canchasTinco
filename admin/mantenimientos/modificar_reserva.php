<?php
session_start();
require_once 'dbConfig.php';
if (!isset($_SESSION["idUsuario"])) {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    exit();
}
// Verificar si se recibió un ID válido en la URL
if (isset($_GET['id'])) {
    $idReserva = $_GET['id'];

    // Conectar a la base de datos
    $conexion = new mysqli("srv1107.hstgr.io", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error en la conexión: " . $conexion->connect_error);
    }

    // Consulta para obtener los datos de la reserva a modificar
    $consulta = "SELECT * FROM reserva WHERE idReserva = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("i", $idReserva);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Obtener los datos de la reserva
            $row = $result->fetch_assoc();
            $title = $row['title'];
            $description = $row['description'];
            $location = $row['location'];
            //$date = $row['fechaReserva'];
            $date = $row['date'];
            $time_from = $row['time_from'];
            $time_to = $row['time_to'];
            $idAnticipo = $row['idAnticipo'];
            $idEstadoReserva = $row['idEstadoReserva'];
            
            $calendar = $row['google_calendar_event_id'];
            
        } else {
            echo "No se encontró la reserva con el ID proporcionado.";
        }
    } else {
        echo "Error en la consulta: " . $stmt->error;
    }
    

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {
    // Si no se proporcionó un ID válido en la URL, mostrar un mensaje de error o redirigir a una página de error
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos enviados por el formulario
    $nuevoTitle = $_POST['title'];
    $nuevaDescription = $_POST['description'];
    $nuevaLocation = $_POST['location'];
    $nuevoAnticipo = $_POST['anticipo'];
    $nuevaDate = "";
    
    $nuevoTimeFrom = $_POST['time_from'];
    $nuevoTimeTo = $_POST['time_to'];
    $nuevoIdEstadoReserva = $_POST['idEstadoReserva'];
    //if($nuevoIdEstadoReserva === 1){
      //  $nuevaDate = $_POST['fechaReserva'];    
    //}else{
        $nuevaDate = $_POST['date'];
    //}
    

    // Verificar si se recibió un ID válido en la URL
    if (isset($_GET['id'])) {
        $idReserva = $_GET['id'];

        // Conectar a la base de datos
        $conexion = new mysqli("srv1107.hstgr.io", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error en la conexión: " . $conexion->connect_error);
        }

        // Consulta SQL para actualizar los datos de la reserva
        $actualizarConsulta = "UPDATE reserva SET title = ?, idAnticipo = ?, idEstadoReserva = ?, description = ?, location = ?, date = ?, time_from = ?, time_to = ? WHERE idReserva = ?";
        $stmtActualizar = $conexion->prepare($actualizarConsulta);
        $stmtActualizar->bind_param("siisssssi", $nuevoTitle, $nuevoAnticipo, $nuevoIdEstadoReserva, $nuevaDescription, $nuevaLocation, $nuevaDate, $nuevoTimeFrom, $nuevoTimeTo, $idReserva);
        //die($nuevoIdEstadoReserva);
        if ($stmtActualizar->execute()) {
            //echo $nuevoIdEstadoReserva;
            //Si el estado es diferente a 1 actualiza el anticipo
            if($nuevoIdEstadoReserva !== 1 ){
                
                if((int)$nuevoIdEstadoReserva === (int)$idEstadoReserva){
                        
                    //Solo modifica el evento de google
                    $_SESSION['evento'] = "2"; //Modifica
                    $_SESSION['calendariog'] = $calendar;
                    $_SESSION['last_event_id'] = $idReserva;
                }elseif($idEstadoReserva === 1){ //Id anterior
                    //Creación
                    $_SESSION['last_event_id'] = $idReserva;
                    $_SESSION['calendariog'] = $calendar;
                    $_SESSION['evento'] = "1";
                }
                $actualizarConsulta2 = "UPDATE anticipo SET asignado = true WHERE idAnticipo = ?";
                $stmtActualizar2 = $conexion->prepare($actualizarConsulta2);
                $stmtActualizar2->bind_param("i", $nuevoAnticipo);
                $stmtActualizar2->execute();
                header("Location: $googleOauthURL");
                
            }
            
            // Redirigir de nuevo a la página de reservas pendientes después de la modificación
           // header("Location: /admin/reservas_pendientes.php");
            exit();
        } else {
            echo "Error al actualizar la reserva: " . $stmtActualizar->error;
        }

        // Cerrar la conexión a la base de datos
        $conexion->close();
    } else {
        // Si no se proporcionó un ID válido en la URL, mostrar un mensaje de error o redirigir a una página de error
        echo "ID de reserva no válido.";
    }
}
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
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/custom.css">
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
    <a class="navbar-brand mx-auto" href="/admin/indexAdmin.php"><img src="/img/logo.png" alt="Logo" class="img-fluid me-2" style="max-width: 50px;"></a>
    <button class="navbar-toggler btn-orange" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
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
      
    </div>
  </div>
</nav>
    <section class="container2 my-5" >
        <div class="row">
            <div class="container">
                <div class="container-content">
                    <div class="text-center">
                        <h1>Modificar reserva</h1>
                        <img src="/img/logo.png" alt="Logo" class="img-fluid" width="100">
                    </div>
                    <form method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título:</label>
                        <input type="text" class="form-control" name="title" value="<?php echo $title; ?>"><br>
                    </div>
                    <div class="mb-3">
                        <label for="description">Descripción:</label>
                        <textarea name="description" class="form-control"><?php echo $description; ?></textarea><br>
                    </div>
                    <div class="mb-3">
                        <label for="location">Ubicación:</label>
                        <input type="text" class="form-control" name="location" value="<?php echo $location; ?>"><br>
                    </div>
                    <div class="mb-3">
                    <div class="mb-3">
                        <label for="date">Fecha:</label>
                        <input type="date" class="form-control" name="date" value="<?php echo $date; ?>"><br>
                    </div>
                    <div class="mb-3">
                        <label for="time_from">Hora de inicio:</label>
                        <input type="time" class="form-control" name="time_from" value="<?php echo $time_from; ?>"><br>
                    </div>
                    <div class="mb-3">
                        <label for="time_to">Hora de fin:</label>
                        <input type="time" class="form-control" name="time_to" value="<?php echo $time_to; ?>"><br>
                    </div>
                    <label for="idEstadoReserva">Estado de Reserva:</label>
                    <select class="form-control" name="idEstadoReserva">
                        <?php
                        // Conectar a la base de datos y obtener las opciones de estado de reserva
                        $conexion = new mysqli("srv1107.hstgr.io", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");
                        $consultaEstados = "SELECT idEstadoReserva, descripcion FROM estadoreserva";
                        $resultEstados = $conexion->query($consultaEstados);

                        if ($resultEstados) {
                            while ($rowEstado = $resultEstados->fetch_assoc()) {
                                $estadoId = $rowEstado['idEstadoReserva'];
                                $estadoDescripcion = $rowEstado['descripcion'];

                                // Compara el valor actual con el valor de la opción y marca como seleccionada si coinciden
                                $selected = ($estadoId == $idEstadoReserva) ? 'selected' : '';

                                echo '<option value="' . $estadoId . '" ' . $selected . '>' . $estadoDescripcion . '</option>';
                            }
                        }
                        $conexion->close();
                        ?>
                    </select>
                     <!--   <label for="idEstadoReserva">Estado de Reserva:</label>
                        <input type="text" class="form-control" name="idEstadoReserva" value="<?php echo $idEstadoReserva; ?>"><br> -->
                    </div>

                    <div class="mb-3">
                        <label for="anticipo">Anticipo:</label>
                        <select class="form-control" name="anticipo">
                            <option value="">Selecciona un anticipo</option>
                            <?php
                            // Conectar a la base de datos
                            $conexion = new mysqli("srv1107.hstgr.io", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

                            // Verificar la conexión
                            if ($conexion->connect_error) {
                                die("Error en la conexión: " . $conexion->connect_error);
                            }

                            // Consulta SQL para obtener los anticipos disponibles
                            $consultaAnticipos = "SELECT idAnticipo, montoAnticipo, fechaAnticipo, noDocumento, motivo, asignado FROM anticipo";
                            $resultAnticipos = $conexion->query($consultaAnticipos);

                            if ($resultAnticipos) {
                                while ($rowAnticipo = $resultAnticipos->fetch_assoc()) {
                                    $anticipoId = $rowAnticipo['idAnticipo'];
                                    $montoAnticipo = $rowAnticipo['montoAnticipo'];
                                    $fechaAnticipo = $rowAnticipo['fechaAnticipo'];
                                    $noDocumento = $rowAnticipo['noDocumento'];
                                    $motivo = $rowAnticipo['motivo'];
                                    $asignado = $rowAnticipo['asignado'];

                                    echo "Anticipo ID: " . $anticipoId . ", Anticipo Seleccionado: " . $anticipoSeleccionado . "<br>";

                                    // Verificar si este anticipo coincide con el seleccionado previamente
                                    $selected = ($anticipoId == $idAnticipo) ? 'selected' : '';

                                    // Generar la opción del select con la información del anticipo
                                    if($anticipo)
                                    echo '<option value="' . $anticipoId . '" ' . $selected . '>' . 'Monto: ' . $montoAnticipo . ', Fecha: ' . $fechaAnticipo . ', No. de Documento: ' . $noDocumento . ', Motivo: ' . $motivo . '</option>';
                                }
                            } else {
                                echo "Error en la consulta de anticipos: " . $conexion->error;
                            }

                            // Cerrar la conexión a la base de datos
                            $conexion->close();
                            ?>
                        </select>
                    </div>


                    
                    
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary btn-orange" value="Guardar Cambios">
                    </div>
                    </form>
                   
                </div>
            </div>
        </div>
    </section>




    <button id="btnSubir" class="btn btn-custom btn-floating btn-back-to-top"><i class="fas fa-arrow-up"></i></button>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<footer class="footer bg-dark text-white py-4">
<div class="container">
<div class="row">
  <div class="col-md-4">
    <h4>Contacto</h4>
    <p><i class="fas fa-phone"></i> Llamada telefónica: (502) 5420-5249</p>
    <p><i class="fab fa-whatsapp"></i> WhatsApp: (502) 5420-5249</p>
  </div>
  <div class="col-md-4">
    <h4>Síguenos en redes sociales</h4>
    <a class="text-white" href="#"><i class="fab fa-facebook"></i> Facebook</a><br>
    <a class="text-white" href="#"><i class="fab fa-instagram"></i> Instagram</a>
  </div>
  <div class="col-md-4">
    <h4>Horario de atención</h4>
    <p>Lunes a Viernes: 9:00 AM - 6:00 PM</p>
    <p>Sábado y Domingo: Cerrado</p>
  
</div>
</div>
</div>
</footer>
</html>
