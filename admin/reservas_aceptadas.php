<?php
session_start();
$descripcionTipoCancha1 = "";
$idtc = "";
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["idUsuario"])) {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas aceptadas</title>
    <!-- Incluir los estilos de Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/custom.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
    <script src="/dist/index.global.min.js"></script>
    

<!-- Include FullCalendar JS & CSS library -->
<link href="/js/fullcalendar/lib/main.css" rel="stylesheet" />
<script src="/js/fullcalendar/lib/main.js"></script>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light cust" style="z-index: 1000;" >
  <div class="container">
    <a class="navbar-brand mx-auto" href="indexAdmin.php"><img src="/img/logo.png" alt="Logo" class="img-fluid me-2" style="max-width: 50px;"></a>
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
          <a class="dropdown-item" href="reporte_actividad.php">Reporte de visitas</a>
          <a class="dropdown-item" href="reportes_reservas.php">Reportes de reservas</a>
          
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
          <a class="dropdown-item" href="reservas_aceptadas.php">Reservas aceptadas</a>
          <a class="dropdown-item" href="reservas_pendientes.php">Reservas pendientes</a>
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
            
            <a class="dropdown-item" href="rol.php">Roles</a>
            <a class="dropdown-item" href="tipo_cancha.php">Tipo de canchas</a>
            <a class="dropdown-item" href="tipo_anticipo.php">Tipo de anticipos</a>
            <a class="dropdown-item" href="cancha.php">Cancha</a>
            <a class="dropdown-item" href="tarifa.php">Tarifa</a>
            <a class="dropdown-item" href="estado_reserva.php">Estado Reserva</a>
            <a class="dropdown-item" href="cliente.php">Cliente</a>
            <a class="dropdown-item" href="horario.php">Horario</a>
            <a class="dropdown-item" href="usuario.php">Usuario</a>
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

<section class="container my-5" >
<div class="row">
<h2 class="text-orange">Solicitudes pendientes de procesar</h2>
</div>
</section>
<section class="container my-5" >
<div class="row">
<div class="col-md-12">
<div class="table-responsive">
    <table class="table table-striped table-dark">
        <thead>
            <tr>
                <th>ID Reserva</th>
                <th>Usuario que registra</th>
                <th>Horario</th>
                <th>ID Cliente</th>
                <th>Estado Reserva</th>
                <th>Fecha Reserva</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            date_default_timezone_set('America/Guatemala');
            $fechaLocal = date('Y-m-d');
            echo  $fechaLocal;
            $conexion2 = new mysqli("srv1107.hstgr.io", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");
           $consulta2 = "SELECT r.idReserva, u.nombre AS usuario, t.horaIni, t.horaFin, CONCAT(c.nombre, ' - Hora Ini: ', r.time_from, ' - Hora Fin: ', r.time_to, ' - Precio: ', t.precio) AS horario, 
            CONCAT(cl.nombre, ' ', cl.apellido) AS cliente, er.descripcion AS estadoReserva, r.date, r.fechaReserva 
            FROM reserva r 
            INNER JOIN usuario u ON r.idUsuario = u.idUsuario
            INNER JOIN horario h ON r.idHorario = h.idHorario
            INNER JOIN cancha c ON h.idCancha = c.idCancha
            INNER JOIN tarifa t ON h.idTarifa = t.idTarifa
            INNER JOIN cliente cl ON r.idCliente = cl.idCliente
            INNER JOIN estadoreserva er ON r.idEstadoReserva = er.idEstadoReserva
            WHERE ( r.idEstadoReserva = 3 or r.idEstadoReserva = 4 ) 
            and  r.date >= '$fechaLocal'
            ORDER BY r.idReserva ASC";
            
            $result = $conexion2->query($consulta2);

            if ($result->num_rows === 0) {
                echo "No hay reservas pendientes.";
            } else {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['idReserva'] . '</td>';
                    echo '<td>' . $row['usuario'] . '</td>';
                    echo '<td>' . $row['horario'] . '</td>';
                    echo '<td>' . $row['cliente'] . '</td>';
                    echo '<td>' . $row['estadoReserva'] . '</td>';
                    echo '<td>' . $row['date'] . '</td>';
                    echo '<td>';
                    
                    // Botón "Modificar" que redirige a la página de modificación con los datos necesarios
                    echo '<a href="/admin/mantenimientos/modificar_reserva.php?id=' . $row['idReserva'] . '" class="btn btn-warning">Modificar</a>';
                    
                    // Botón "Eliminar" que redirige a la página de eliminación con los datos necesarios
                    echo '<a href="/admin/mantenimientos/eliminar_reserva.php?id=' . $row['idReserva'] . '" class="btn btn-danger">Eliminar</a>';
        
                    echo '</td>';
                    echo '</tr>';
                }
            }
        
            $conexion2->close();
            ?>
        </tbody>
    </table>
</div>
</div>
</div>
</section>

    
<!-- Agregamos el script para SweetAlert2 y el script personalizado -->



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