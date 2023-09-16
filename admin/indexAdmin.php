<?php
session_start();

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
    <title>Canchas Tinco</title>
    
  <!--  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    
    <script src="/dist/index.global.min.js"></script>
    
    <!-- Sweetalert2 -->

</head>
<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-light custom-navbar">
  <div class="container">
    <a class="navbar-brand mx-auto" href="#"><img src="/img/logo.png" alt="Logo" class="img-fluid me-2" style="max-width: 50px;"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="reporte_actividad.php">Reporte de visitas</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="/calendario.php">Eventos</a>
        </li>
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
            <a class="dropdown-item" href="horario.php">Usuario</a>
          </div>
        </li>
        <?php }?>
        <li class="nav-item">
          <a class="nav-link" href="/admin/cerrar_sesion.php">Cerrar Sesión</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Contenido principal -->
<section class="landing-page">
  <div class="overlay">
        <div class="content">
          <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
              <img src="/img/jugador-izq.png" alt="Portero izquierdo" class="img-fluid img-left">
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
              <!-- Segunda columna (textos) -->
              <h1 class="mb-3">CANCHAS</h1>
              <h1 class="mb-3">DEPORTIVAS</h1>
            </div>
            <div class="col-md-4">
              <!-- Tercera columna (imagen) que abarca las 2 filas -->
              <img src="/img/jugador-derec.png" alt="Portero derecho" class="img-fluid img-right">
            </div>
          </div>
          <div class="row">
            <div class="border border-white border-4 rounded p-4">
              <h1>SAN MIGUEL TINCO</h1>
            </div>
            <div class="col-md-12">
                    <p>Bienvenid@, <?php echo $_SESSION["nombre"]; ?>.</p>
                    <button class="btn btn-orange">Reservar</button>
                </div>  
          </div>
          
        </div>
        </div>
</section>

<!-- Scripts de Bootstrap (requieren jQuery) -->

<button id="btnSubir" class="btn btn-custom btn-floating btn-back-to-top"><i class="fas fa-arrow-up"></i></button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
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
