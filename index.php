<?php
session_start(); // Inicia la sesión si aún no está iniciada

// Si el usuario está autenticado, obtén su ID de usuario
$idUsuario = isset($_SESSION["idUsuario"]) ? $_SESSION["idUsuario"] : null;

// Obtén la página actual y la dirección IP del usuario
$paginaVisitada = $_SERVER["REQUEST_URI"];
$direccionIP = $_SERVER["REMOTE_ADDR"];

// Crea una conexión a la base de datos (reemplaza con tus propios datos)
$conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

// Verifica la conexión

if ($conexion->connect_error) {
    echo $conexion->connect_error;
    die("Error de conexión: " . $conexion->connect_error);
}

// Prepara la consulta para insertar un registro en la tabla de registro de actividad
$consulta = "INSERT INTO registroactividad (idUsuario, fechaHora, paginaVisitada, direccionIP) VALUES (?, NOW(), ?, ?)";
$stmt = $conexion->prepare($consulta);
if (!$stmt->bind_param("iss", $idUsuario, $paginaVisitada, $direccionIP)) {
  die("Error en bind_param(): " . $stmt->error);
  echo $stmt->error;
}
$stmt->bind_param("iss", $idUsuario, $paginaVisitada, $direccionIP);

// Ejecuta la consulta
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
    <title>Canchas Tinco</title>
    
  <!--  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="plugin/components/Font Awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="plugin/whatsapp-chat-support.css">
    <script src="/dist/index.global.min.js"></script>
    
    <!-- Sweetalert2 -->
<script src="/js/sweetalert2.all.min.js"></script>

<!-- Include FullCalendar JS & CSS library -->
<link href="/js/fullcalendar/lib/main.css" rel="stylesheet" />
<script src="/js/fullcalendar/lib/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    
    initialView: 'timeGridWeek',
    height: 650,
    events: 'fetchEvents.php',
    
    selectable: true,
    select: async function (start, end, allDay) {
      const { value: formValues } = await Swal.fire({
        title: 'Crear reservación',
        confirmButtonText: 'Crear',
        showCloseButton: true,
		    showCancelButton: true,
        html:
          '<input id="swalEvtTitle" class="swal2-input" placeholder="Ingresa titulo">' +
          '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Ingresa descripción"></textarea>' +
          '<input id="swalEvtURL" class="swal2-input" placeholder="Observaciones">',
        focusConfirm: false,
        preConfirm: () => {
          return [
            document.getElementById('swalEvtTitle').value,
            document.getElementById('swalEvtDesc').value,
            document.getElementById('swalEvtURL').value
          ]
        }
      });

      if (formValues) {
        // Add event
        fetch("eventHandler.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ request_type:'addEvent', start:start.startStr, end:start.endStr, event_data: formValues}),
        })
        .then(response => response.json())
        .then(data => {
          if (data.status == 1) {
            Swal.fire('Reservación creada correctamente!', '', 'success');
          } else {
            Swal.fire(data.error, '', 'error');
          }

          // Refetch events from all sources and rerender
          calendar.refetchEvents();
        })
        .catch(console.error);
      }
    },

    eventClick: function(info) {
      info.jsEvent.preventDefault();
      
      // change the border color
      info.el.style.borderColor = 'red';
      
      Swal.fire({
        title: info.event.title,
        //text: info.event.extendedProps.description,
        icon: 'info',
        html:'<p>'+info.event.extendedProps.description+'</p>',
        showCloseButton: true,
        showCancelButton: true,
        showDenyButton: true,
        cancelButtonText: 'Cerrar',
        confirmButtonText: 'Borrar',
        denyButtonText: 'Editar',
      }).then((result) => {
        if (result.isConfirmed) {
          // Delete event
          fetch("eventHandler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ request_type:'deleteEvent', event_id: info.event.id}),
          })
          .then(response => response.json())
          .then(data => {
            if (data.status == 1) {
              Swal.fire('Reservación borrada correctamente!', '', 'success');
            } else {
              Swal.fire(data.error, '', 'error');
            }

            // Refetch events from all sources and rerender
            calendar.refetchEvents();
          })
          .catch(console.error);
        } else if (result.isDenied) {
          // Edit and update event
          Swal.fire({
            title: 'Editar reservación',
            html:
              '<input id="swalEvtTitle_edit" class="swal2-input" placeholder="Ingresa titulo" value="'+info.event.title+'">' +
              '<textarea id="swalEvtDesc_edit" class="swal2-input" placeholder="Ingresa descripción">'+info.event.extendedProps.description+'</textarea>' +
              '<input id="swalEvtURL_edit" class="swal2-input" placeholder="Ingresa observaciones" value="'+info.event.url+'">',
            focusConfirm: false,
            confirmButtonText: 'Actualizar',
            preConfirm: () => {
            return [
              document.getElementById('swalEvtTitle_edit').value,
              document.getElementById('swalEvtDesc_edit').value,
              document.getElementById('swalEvtURL_edit').value
            ]
            }
          }).then((result) => {
            if (result.value) {
              // Edit event
              fetch("eventHandler.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ request_type:'editEvent', start:info.event.startStr, end:info.event.endStr, event_id: info.event.id, event_data: result.value})
              })
              .then(response => response.json())
              .then(data => {
                if (data.status == 1) {
                  Swal.fire('Reservación actualizada correctamente!', '', 'success');
                } else {
                  Swal.fire(data.error, '', 'error');
                }

                // Refetch events from all sources and rerender
                calendar.refetchEvents();
              })
              .catch(console.error);
            }
          });
        } else {
          Swal.close();
        }
      });
    }
  });
  
  document.getElementById('btnMonth').addEventListener('click', function() {
    calendar.changeView('dayGridMonth');
  });

  document.getElementById('btnWeek').addEventListener('click', function() {
    calendar.changeView('timeGridWeek');
  });

  document.getElementById('btnDay').addEventListener('click', function() {
    calendar.changeView('timeGridDay');
  });

  calendar.render();
});
</script>
</head>
<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-light custom-navbar">
  <div class="container">
    <a class="navbar-brand mx-auto" href="#"><img src="img/logo.png" alt="Logo" class="img-fluid me-2" style="max-width: 50px;"></a>
    <button class="navbar-toggler btn-outline-orange" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="#SobreNosotros">Sobre Nosotros</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Servicio">Servicios</a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="#Contacto">Contacto</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Tarifa">Tarifas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#Disponibilidad">Disponibilidad</a>
          </li>
        </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="login.php">Login</a>
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
              <img src="img/jugador-izq.png" alt="Portero izquierdo" class="img-fluid img-left">
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
              <!-- Segunda columna (textos) -->
              <h1 class="mb-3">CANCHAS</h1>
              <h1 class="mb-3">DEPORTIVAS</h1>
            </div>
            <div class="col-md-4">
              <!-- Tercera columna (imagen) que abarca las 2 filas -->
              <img src="img/jugador-derec.png" alt="Portero derecho" class="img-fluid img-right">
            </div>
          </div>
          <div class="row">
            <div class="border border-white border-4 rounded p-4">
              <h1>SAN MIGUEL TINCO</h1>
            </div>
            <div class="col-md-12">
              <p>Ven a jugar chamuscas con tus amigos, <br>compañeros de trabajo, familia,<br>tenemos para todos</p>
              <a href="/reserva.php"><button class="btn btn-orange" >Reservar</button></a>
            </div>
          </div>
        </div>
        </div>
</section>

<section class="container my-5" >
  <h2 class="text-center mb-4 text-orange" id="SobreNosotros">Sobre nosotros</h2>
  <div class="row">
    <div class="col-md-6">
      <h3>Nuestra Misión</h3>
      <p>Nuestra misión es proporcionar espacios deportivos de calidad donde las personas puedan disfrutar de actividades físicas y deportivas en un ambiente amigable y seguro.</p>
    </div>
    <div class="col-md-6">
      <h3>Nuestra Visión</h3>
      <p>Nuestra visión es convertirnos en el referente en la comunidad para la práctica de deportes y actividades recreativas, promoviendo la salud, el trabajo en equipo y la diversión.</p>
    </div>
  </div>
  <div class="row mt-4">
    <div class="col-md-12">
      <h3>Nuestro Compromiso</h3>
      <p>Estamos comprometidos en brindar instalaciones de alta calidad, fomentar el espíritu deportivo y contribuir al bienestar de la comunidad a través de nuestras ofertas deportivas y recreativas.</p>
    </div>
  </div>
</section>
</div>


  <section class="container mt-4" id="Servicio">
    <h2 class="text-center mb-4 text-orange">Nuestros Servicios</h2>
    <div class="row">
      <div class="col-md-6">
        <div class="card mb-4 bg-dark">
          <img src="img/servicio/cancha5.jpg" class="card-img-top img-fluid" alt="Cancha Fútbol 5">
          <div class="card-body">
            <h5 class="card-title text-light">Reserva Cancha Fútbol 5</h5>
            <p class="card-text text-light ">Reserva en nuestras canchas de fútbol 5 para poder jugar con tus amigos.</p>
            <a href="#" class="btn btn-primary btn-orange w-100">Reservar</a>
          </div>
        </div>
        <div class="card mb-4 bg-dark">
          <img src="img/servicio/cancha7.webp" class="card-img-top img-fluid" alt="Cancha Fútbol 7">
          <div class="card-body">
            <h5 class="card-title text-light">Reserva Cancha Fútbol 7</h5>
            <p class="card-text text-light">Reserva en nuestras canchas de fútbol 7 para poder jugar con tus amigos.</p>
            <a href="#" class="btn btn-primary btn-orange w-100">Reservar</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mb-4 bg-dark">
          <img src="img/servicio/canchatenis.jpg" class="card-img-top img-fluid" alt="Cancha Tenis">
          <div class="card-body">
            <h5 class="card-title text-light">Reserva Cancha de Tenis</h5>
            <p class="card-text text-light">Reserva en nuestras canchas de tenis para poder jugar con tus amigos y divertirte.</p>
            <a href="#" class="btn btn-primary btn-orange w-100">Reservar</a>
          </div>
        </div>
        <div class="card mb-4 bg-dark">
          <img src="img/servicio/cabania.jpg" class="card-img-top img-fluid" alt="Cabañas">
          <div class="card-body">
            <h5 class="card-title text-light">Ranchos para Actividades</h5>
            <p class="card-text text-light">Reserva las áreas verdes para poder tener actividades con tu familia y amigos.</p>
            <a href="#contacto" class="btn btn-primary btn-orange w-100">Contacto</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="container contact-section py-5" id="Contacto">
    <h2 class="text-center mb-4 text-orange">Contacto</h2>
      <div class="row">
        
        <div class="col-lg-6">
          <div id="miCarrusel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="img/carrousel/cancha1.jpg" class="d-block w-100 img-fluid carousel-image" alt="Imagen 1">
              </div>
              <div class="carousel-item">
                <img src="img/carrousel/cancha2.jpeg" class="d-block w-100 img-fluid carousel-image" alt="Imagen 2">
              </div>
              <div class="carousel-item">
                <img src="img/carrousel/cancha3.jpeg" class="d-block w-100 img-fluid carousel-image" alt="Imagen 3">
              </div>
              <!-- Agrega más elementos carousel-item según necesites -->
            </div>
            <a class="carousel-control-prev" href="#miCarrusel" role="button" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#miCarrusel" role="button" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Siguiente</span>
            </a>
          </div>
        </div>
        <div class="col-md-6">
          
          <form action="enviar_correo.php" method="post">
            <div class="form-group">
              <label for="nombre">Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="email">Correo electrónico:</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="mensaje">Mensaje:</label>
              <textarea name="mensaje" id="mensaje" class="form-control" rows="6" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-orange">Enviar</button>
          </form>
        </div>
        
      </div>
  </section>
  <section class="container my-5" id="Tarifa">
    <h2 class="text-center mb-4">Tarifas</h2>
    <div class="row">
      <div class="col-md-12">
        <table class="table table-bordered table-dark">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Tipo de Cancha</th>
              <th scope="col">Horario</th>
              <th scope="col">Precio</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td rowspan="3">Fútbol 5</td>
              <td>8:00 am - 5:00 pm</td>
              <td>Q150</td>
            </tr>
            <tr>
              <td>6:00 pm - 10:00 pm</td>
              <td>Q200</td>
            </tr>
            <tr>
              <td>11:00 pm en adelante</td>
              <td>Q250</td>
            </tr>
            <tr>
              <td rowspan="3">Fútbol 7</td>
              <td>8:00 am - 5:00 pm</td>
              <td>Q250</td>
            </tr>
            <tr>
              <td>6:00 pm - 10:00 pm</td>
              <td>Q300</td>
            </tr>
            <tr>
              <td>11:00 pm en adelante</td>
              <td>Q350</td>
            </tr>
            <tr>
              <td rowspan="3">Canchas de Tenis</td>
              <td>8:00 am - 5:00 pm</td>
              <td>Q200</td>
            </tr>
            <tr>
              <td>6:00 pm - 10:00 pm</td>
              <td>Q250</td>
            </tr>
            <tr>
              <td>11:00 pm en adelante</td>
              <td>Q300</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
  <section class="container my-5">
    <div class="col-md-12">
      <h2 class="text-orange">Ubicación</h2>
      <p>Nos encontramos en la siguiente dirección:</p>
      <address>
        Dirección de la empresa pendiente<br>
        De definir para cambiar el texto
      </address>
      
      <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15441.52393350407!2d-90.60031!3d14.634302!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8589a1c3f811f07b%3A0x68c91e766bb665f7!2sCanchas%20San%20Miguel%20Tinco!5e0!3m2!1ses-419!2sgt!4v1692423899222!5m2!1ses-419!2sgt" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </section>
  <section class=" container my-5">
  <div class="row" id="Disponibilidad">
    <div class="col-lg-12">
        <h1 class="text-orange">DISPONIBILIDAD</h1> 
        <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=1&bgcolor=%23616161&ctz=America%2FGuatemala&mode=WEEK&src=a2V2aW4uY2FodWVzQGdtYWlsLmNvbQ&color=%23F4511E" style="border:solid 1px #777" width="100%" height="600" frameborder="0" scrolling="no"></iframe>
          
    </div>
  </div>
</section>
<!--<section class="container my-5" >
<div>
         Barra de navegación con botones personalizados 
        <button id="btnMonth" class="dark-button">Mes</button>
        <button id="btnWeek" class="dark-button">Semana</button>
        <button id="btnDay" class="dark-button">Día</button>
    </div>  
<div id="calendar" class="text-orange">
  </div>
  

</section>-->
<!-- Scripts de Bootstrap (requieren jQuery) -->
<div class="whatsapp_chat_support wcs_fixed_left" id="button-w">
      
    <div class="wcs_button wcs_button_circle">
        <span class="fa fa-whatsapp"></span>
    </div>
    <div class="wcs_button_label">
            Contáctanos
        </div>  
 
    <div class="wcs_popup">
        <div class="wcs_popup_close">
            <span class="fa fa-close"></span>
        </div>
        <div class="wcs_popup_header">
            <span class="fa fa-whatsapp"></span>
            <strong>Servicio al cliente</strong>
            
            <div class="wcs_popup_header_description">¿Necesitas ayuda? Chatea con nosotros en Whatsapp</div>

        </div>  
        <div class="wcs_popup_input" 
            data-number="50254205249"
            data-availability='{ "monday":"08:00-22:00", "tuesday":"08:00-22:00", "wednesday":"08:00-22:00", "thursday":"08:00-22:00", "friday":"08:00-22:00", "saturday":"07:00-22:00", "sunday":"08:00-22:00" }'>
            <input type="text" placeholder="Escribir pregunta!" />
            <i class="fa fa-play"></i>
        </div>
        <div class="wcs_popup_avatar">
            <img src="/img/logo.png" alt="">
        </div>
    </div>
</div>
<button id="btnSubir" class="btn btn-custom btn-floating btn-back-to-top"><i class="fas fa-arrow-up"></i></button>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="plugin/components/jQuery/jquery-1.11.3.min.js"></script>
<script src="plugin/components/moment/moment.min.js"></script>
<script src="plugin/components/moment/moment-timezone-with-data.min.js"></script> <!-- spanish language (es) -->

<script src="plugin/whatsapp-chat-support.js"></script>
<script>
   $('#button-w').whatsappChatSupport({
        defaultMsg : '',
    });
</script>
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
