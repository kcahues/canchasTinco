<?php
session_start(); // Inicia la sesión si aún no está iniciada

if (!isset($_SESSION["idUsuario"])) {
  header("Location: login.php"); // Redirigir a la página de inicio de sesión si no ha iniciado sesión
  exit();
}
// Si el usuario está autenticado, obtén su ID de usuario
$idUsuario = isset($_SESSION["idUsuario"]) ? $_SESSION["idUsuario"] : null;

// Obtén la página actual y la dirección IP del usuario
$paginaVisitada = $_SERVER["REQUEST_URI"];
$direccionIP = $_SERVER["REMOTE_ADDR"];

// Crea una conexión a la base de datos (reemplaza con tus propios datos)
$conexion = new mysqli("srv1107.hstgr.io", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

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
    slotDuration: '01:00:00',
    allDaySlot: false,
    height: 650,
    events: 'fetchEvents2.php',

    eventSources: [
      {
        url: 'fetchEvents2.php', // URL para obtener eventos
        method: 'POST',
        extraParams: {
          view: 'week', // Inicialmente, mostrar la vista de semana
        },
        failure: function() {
          alert('Error al cargar eventos');
        },
      },
    ],
    
    selectable: true,
    select: async function (start, end, allDay) {
         // Crear objetos Date a partir de las fechas
         const startDateISO = start.startStr;
         const endDateISO = start.endStr;

        // Formatear las fechas desde y hasta para que coincidan con tus campos de fecha
            const startDate = startDateISO.split('T')[0];
            const startTime = startDateISO.split('T')[1];
            const horaMinutos = startTime.split("-")[0];
            const endTime = endDateISO.split('T')[1];
            const endH = endTime.split("-")[0];
        
      const { value: formValues } = await Swal.fire({
        title: 'Crear reservación',
        confirmButtonText: 'Crear',
        showCloseButton: true,
        showCancelButton: true,
        html:
          '<input id="swalEvtTitle" class="swal2-input" placeholder="Ingresa título">' +
          '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Ingresa descripción"></textarea>' +
          '<input id="swalEvtLocation" class="swal2-input" placeholder="Ingresa ubicación">' +
          '<input type="date" id="swalEvtDate" class="swal2-input" placeholder="Fecha" value="'+ startDate + '" readonly>' +
          '<input type="time" id="swalEvtTimeFrom" class="swal2-input" placeholder="Hora inicio" value="'+ horaMinutos + '" readonly>' +
          '<input type="time" id="swalEvtTimeTo" class="swal2-input" placeholder="Hora fin" value="'+ endH + '" readonly>',
        focusConfirm: false,
        preConfirm: () => {
          return [
            document.getElementById('swalEvtTitle').value,
            document.getElementById('swalEvtDesc').value,
            document.getElementById('swalEvtLocation').value,
            document.getElementById('swalEvtDate').value,
            document.getElementById('swalEvtTimeFrom').value,
            document.getElementById('swalEvtTimeTo').value,
        
          ];
        }
      });

      if (formValues) {
        
  const formData = new FormData();
  formData.append('request_type', 'addEvent');
  formData.append('start', start.startStr);
  formData.append('end', start.endStr);
  formData.append('event_data', JSON.stringify(formValues));

  fetch("eventHandler2.php", {
    method: "POST",
    body: formData,
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

  // Accede a los datos del evento
  var eventData = info.event.extendedProps;
  var date = "";
  $from = "";
  $to   = "";

   // Crea un objeto FormData con la solicitud
   var formData = new FormData();
  formData.append('request_type', 'getEventById');
  formData.append('event_id', info.event.id);
      
  // Realiza una solicitud POST a eventHandler2.php
  fetch('eventHandler2.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    
    if (data === 'not_found') {
      // El evento no se encontró en la base de datos
      console.log('Evento no encontrado');
    } else if (data === 'error') {
      // Hubo un error al procesar la solicitud
      console.log('Error al obtener el evento');
    } else {
      date = data.date;
      //console.log(date);
      //console.log(data.date);
      // La respuesta contiene la información del evento en formato JSON
      //console.log('Información del evento:', data);
      
      $from = data.time_from;
      $to   = data.time_to;
      // Aquí puedes utilizar la información del evento según tus necesidades
      // Por ejemplo, puedes mostrar los detalles del evento en una ventana emergente
     

  // Muestra la información en una ventana emergente de edición
  Swal.fire({
    title: 'Editar o borrar reservación',
    html:
      '<input id="swalEvtTitle_edit" class="swal2-input" placeholder="Ingresa título" value="' + info.event.title + '">' +
      '<textarea id="swalEvtDesc_edit" class="swal2-input" placeholder="Ingresa descripción">' + info.event.extendedProps.description + '</textarea>' +
      '<input id="swalEvtLocation_edit" class="swal2-input" placeholder="Ingresa ubicación" value="' + info.event.extendedProps.location + '">' +
      '<input type="date" id="swalEvtDate_edit" class="swal2-input" placeholder="Selecciona fecha" value="' + date + '">' +
      '<input type="time" id="swalEvtTimeFrom_edit" class="swal2-input" placeholder="Hora inicio" value="' + $from + '">' +
      '<input type="time" id="swalEvtTimeTo_edit" class="swal2-input" placeholder="Hora fin" value="' + $to + '">',
    focusConfirm: false,
    confirmButtonText: 'Actualizar',
    showCancelButton: true, // Agregamos un botón de cancelar
    cancelButtonText: 'Borrar',
    preConfirm: () => {
      return [
        document.getElementById('swalEvtTitle_edit').value,
        document.getElementById('swalEvtDesc_edit').value,
        document.getElementById('swalEvtLocation_edit').value,
        document.getElementById('swalEvtDate_edit').value,
        document.getElementById('swalEvtTimeFrom_edit').value,
        document.getElementById('swalEvtTimeTo_edit').value,
      ];
    }
  }).then((result) => {
    if (result.value) {
      // Obtén los valores del formulario de edición
      var title = document.getElementById('swalEvtTitle_edit').value;
      var description = document.getElementById('swalEvtDesc_edit').value;
      var location = document.getElementById('swalEvtLocation_edit').value;
      var date = document.getElementById('swalEvtDate_edit').value;
      var time_from = document.getElementById('swalEvtTimeFrom_edit').value;
      var time_to = document.getElementById('swalEvtTimeTo_edit').value;

      // Crea un objeto FormData para enviar los datos al servidor
      var formData = new FormData();
      formData.append('request_type', 'editEvent');
      formData.append('event_id', info.event.id);
      formData.append('title', title);
      formData.append('description', description);
      formData.append('location', location);
      formData.append('date', date);
      formData.append('time_from', time_from);
      formData.append('time_to', time_to);

      // Envía los datos al servidor mediante una solicitud POST
      fetch("eventHandler2.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        if (data === 'success') {
          Swal.fire('Reservación actualizada correctamente!', '', 'success');
          // Refetch events from all sources and rerender
          calendar.refetchEvents();
        } else {
          Swal.fire('Error al actualizar la reservación', '', 'error');
        }
      })
      .catch(console.error);
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      // Si se hace clic en el botón de cancelar (borrar evento)
      Swal.fire({
  title: '¿Estás seguro?',
  text: 'Esta acción eliminará el evento de forma permanente.',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Sí, borrar',
  cancelButtonText: 'Cancelar'
}).then((deleteResult) => {
  if (deleteResult.isConfirmed) {
    // Crea un formulario con los datos necesarios
    var formData = new FormData();
    formData.append('request_type', 'deleteEvent');
    formData.append('event_id', info.event.id);

    // Realiza una solicitud POST tradicional
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'eventHandler2.php', true);
    xhr.onload = function() {
      if (xhr.status === 200) {
        // La solicitud se completó con éxito
        var response = xhr.responseText;
        if (response === 'success') {
          Swal.fire('Reservación borrada correctamente!', '', 'success');
          // Refetch events from all sources and rerender
          calendar.refetchEvents();
        } else {
          Swal.fire('Error al borrar la reservación', '', 'error');
        }
      } else {
        // Hubo un error en la solicitud
        Swal.fire('Error en la solicitud al servidor', '', 'error');
      }
    };
    xhr.send(formData);
  }
});


  //    });

    /*  if (formValues) {
        
  const formData = new FormData();
  formData.append('request_type', 'addEvent2');
  formData.append('event_id', info.event.id);
      formData.append('title', title);
      formData.append('description', description);
      formData.append('location', location);
      formData.append('date', date);
      formData.append('time_from', time_from);
      formData.append('time_to', time_to);


  fetch("eventHandler2.php", {
    method: "POST",
    body: formData,
  })
  .then(response => response.text())
      .then(data => {
        if (data === 'success') {
          Swal.fire('Reservación actualizada correctamente!', '', 'success');
          // Refetch events from all sources and rerender
          calendar.refetchEvents();
        } else {
          Swal.fire('Error al actualizar la reservación', '', 'error');
        }
      })
  .catch(console.error);
}*/
}
  })
  .catch(error => {
    console.error('Error en la solicitud:', error);
  });
    }
    
  });
} 
  });
  

  // Agregar botones para cambiar la vista
  document.getElementById('btnMonth').addEventListener('click', function() {
    calendar.changeView('dayGridMonth');
  });

  document.getElementById('btnWeek').addEventListener('click', function() {
    calendar.changeView('timeGridWeek');
  });

  document.getElementById('btnDay').addEventListener('click', function() {
    calendar.changeView('timeGridDay');
  });

  document.getElementById('btnAddEvent2').addEventListener('click', function() {
  // Obtener la fecha actual
  const currentDate = new Date();
  // Formatear la fecha actual como YYYY-MM-DD
  const currentDateFormatted = currentDate.toISOString().split('T')[0];

  Swal.fire({
    title: 'Crear reservación',
    confirmButtonText: 'Crear',
    showCloseButton: true,
    showCancelButton: true,
    html:
      '<input id="swalEvtTitle" class="swal2-input" placeholder="Ingresa título">' +
      '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Ingresa descripción"></textarea>' +
      '<input id="swalEvtLocation" class="swal2-input" placeholder="Ingresa ubicación">' +
      '<input type="date" id="swalEvtDate" class="swal2-input" placeholder="Fecha" value="'+ currentDateFormatted + '">' +
      '<input type="time" id="swalEvtTimeFrom" class="swal2-input" placeholder="Hora inicio">' +
      '<input type="time" id="swalEvtTimeTo" class="swal2-input" placeholder="Hora fin">',
    focusConfirm: false,
    preConfirm: () => {
      return [
        document.getElementById('swalEvtTitle').value,
        document.getElementById('swalEvtDesc').value,
        document.getElementById('swalEvtLocation').value,
        document.getElementById('swalEvtDate').value,
        document.getElementById('swalEvtTimeFrom').value,
        document.getElementById('swalEvtTimeTo').value,
      ];
    }
  }).then((result) => {
    if (result.value) {
      // Add event
      fetch("eventHandler2.php", { // Cambio en la URL
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          request_type: 'addEvent',
          start: currentDateFormatted, // Utiliza la fecha formateada
          end: currentDateFormatted, // Utiliza la misma fecha para el inicio y el fin
          event_data: result.value
        }),
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
  });
});


  calendar.render();
});
</script>
</head>
<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-light cust">
  <div class="container">
    <a class="navbar-brand mx-auto" href="#"><img src="/img/logo.png" alt="Logo" class="img-fluid me-2" style="max-width: 50px;"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="/admin/reporte_actividad.php">Reporte de visitas</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="#">Eventos</a>
        </li>
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




  
<section class="container my-5" >
<div>
        <!-- Barra de navegación con botones personalizados -->
        <button id="btnMonth" class="dark-button">Mes</button>
        <button id="btnWeek" class="dark-button">Semana</button>
        <button id="btnDay" class="dark-button">Día</button>
        <button id="btnAddEvent2" class="dark-button">Agregar Evento</button>

    </div>  
    
<div id="calendar" class="text-orange">
  </div>
  

</section>
<!-- Scripts de Bootstrap (requieren jQuery) -->

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
