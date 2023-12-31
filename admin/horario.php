<?php
session_start();
$descripcionTipoCancha1 = "";
$idtc = "";
$descripcionTipoCancha12 = "";
$idtc2 = "";
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["idUsuario"])) {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    exit();
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

// Verificación de la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$registrosPorPagina = 5;

// Página actual (obtén el valor de la página desde la URL)
$paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Calcula el offset (inicio) para la consulta SQL
$offset = ($paginaActual - 1) * $registrosPorPagina;

$stmt = null;

// Procesamiento de la búsqueda
if (isset($_GET["filtro"])) {
    $filtro = "%" . $_GET["filtro"] . "%";
    $consulta = "SELECT * FROM horario WHERE idHorario LIKE ? ORDER BY idHorario ASC";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("i", $filtro);
    $stmt->execute();
    $resultados = $stmt->get_result();

    // Consulta para contar el número total de registros después de la búsqueda
    $totalRegistrosConsulta = "SELECT COUNT(*) as total FROM horario WHERE idHorario LIKE ?";
    $stmtTotal = $conexion->prepare($totalRegistrosConsulta);
    $stmtTotal->bind_param("i", $filtro);
    $stmtTotal->execute();
    $totalRegistrosResultado = $stmtTotal->get_result();
    $totalRegistros = $totalRegistrosResultado->fetch_assoc()['total'];
} else {
    // Consulta sin filtro de búsqueda
    $consulta = "SELECT * FROM horario ORDER BY idHorario ASC LIMIT $registrosPorPagina OFFSET $offset";
    $resultados = $conexion->query($consulta);

    // Consulta para contar el número total de registros sin filtro
    $totalRegistrosConsulta = "SELECT COUNT(*) as total FROM horario";
    $totalRegistrosResultado = $conexion->query($totalRegistrosConsulta);
    $totalRegistros = $totalRegistrosResultado->fetch_assoc()['total'];
}

// Calcula el número total de páginas
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios</title>
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


    <section class="container my-5 " >

<div class="row">
  <div class="col-md-12">
<h1 class="mb-4 text-orange ">Horarios</h1>
  </div>
</div>
<!-- Formulario de búsqueda -->
<div class="row">
  <div class="col-md-12">
<form method="get" class="mb-4">
<div class="input-group">
    <input type="text" class="form-control" name="filtro" placeholder="Filtrar por...">
    <button type="submit" class="btn btn-primary btn-orange">Buscar</button>
</div>
</form>
  </div>
</div>
    </section>
<?php

$stmt = null;
// Procesamiento de la búsqueda
if (isset($_GET["filtro"])) {
$filtro = "%" . $_GET["filtro"] . "%";
$consulta = "SELECT * FROM horario WHERE idHorario LIKE ? ORDER BY idHorario ASC";
$stmt = $conexion->prepare($consulta);
$stmt->bind_param("i", $filtro);
$stmt->execute();
$resultados = $stmt->get_result();
} else {
$consulta = "SELECT * FROM horario ORDER BY idHorario ASC LIMIT $registrosPorPagina OFFSET $offset";
$resultados = $conexion->query($consulta);

}
?>
  

<!-- Mostrar los resultados -->
<section class="container my-5 " >
<div class="row">
  <div class="col-md-12">
        <!-- Paginación -->
        
  <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
                <li class="page-item <?php echo ($i == $paginaActual) ? 'active' : ''; ?>">
                    <a class="page-link text-orange " href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
        <ul class="pagination justify-content-center">
            <?php if ($paginaActual > 1) : ?>
                <li class="page-item">
                    <a class="page-link text-orange " href="?pagina=<?php echo ($paginaActual - 1); ?>">Anterior</a>
                </li>
            <?php endif; ?>

            <?php if ($paginaActual < $totalPaginas) : ?>
                <li class="page-item">
                    <a class="page-link text-orange" href="?pagina=<?php echo ($paginaActual + 1); ?>">Siguiente</a>
                </li>
            <?php endif; ?>
        </ul>
        </div>  
</div>
<div class="row">
  <div class="col-md-12">
  <div class="table-responsive">
<table class="table table-striped table-dark" >
<thead>
    <tr>
        <th>ID Horario</th>
        <th>Id Cancha</th>
        <th>Id Tarifa</th>
        <th>Acciones</th>
        
    </tr>
</thead>
<tbody>
    <?php while ($fila = $resultados->fetch_assoc()): ?>
    <tr>
        <td><?php echo $fila["idHorario"]; ?></td>
        <td>
            <?php 
                $tcancha = $fila["idCancha"];
                $idtc = $tcancha;
                $consultaTiposCancha1 = "SELECT * FROM cancha where idCancha = $tcancha";
                $resultadosTiposCancha1 = $conexion->query($consultaTiposCancha1);
                while ($filaTipoCancha1 = $resultadosTiposCancha1->fetch_assoc()) {
                    $idTipoCancha1 = $filaTipoCancha1["idCancha"];
                    $descripcionTipoCancha1 = $filaTipoCancha1["descripcion"];
                    //echo "<option value='$idTipoCancha'>$descripcionTipoCancha</option>";
                }
                echo $descripcionTipoCancha1;
            ?>
 <!--   <select type="hidden"class="form-control" id="tipoCanchaSelect" name="tipoCancha">
        <?php
        // Consulta para obtener los valores de la tabla tipocancha
        $consultaTiposCancha = "SELECT * FROM cancha";
        $resultadosTiposCancha = $conexion->query($consultaTiposCancha);

        while ($filaTipoCancha = $resultadosTiposCancha->fetch_assoc()) {
            $idTipoCancha = $filaTipoCancha["idCancha"];
            $descripcionTipoCancha = $filaTipoCancha["descripcion"];
            echo "<option value='$idTipoCancha'>$descripcionTipoCancha</option>";
        }
        ?>
    </select> !-->
</td>
<input type="hidden" id="idTipoCanchaSeleccionado" name="idTipoCanchaSeleccionado">

<td>
            <?php 
                $tcancha2 = $fila["idTarifa"];
                $idtc2 = $tcancha2;
                $consultaTiposCancha12 = "SELECT * FROM tarifa where idTarifa = $tcancha2";
                $resultadosTiposCancha12 = $conexion->query($consultaTiposCancha12);
                while ($filaTipoCancha12 = $resultadosTiposCancha12->fetch_assoc()) {
                    $idTipoCancha12 = $filaTipoCancha12["idTarifa"];
                    $descripcionTipoCancha12 = $filaTipoCancha12["descripcion"];
                    //echo "<option value='$idTipoCancha'>$descripcionTipoCancha</option>";
                }
                echo $descripcionTipoCancha12;
            ?>
 <!--   <select type="hidden"class="form-control" id="tipoCanchaSelect" name="tipoCancha">
        <?php
        // Consulta para obtener los valores de la tabla tipocancha
        $consultaTiposCancha2 = "SELECT * FROM tarifa";
        $resultadosTiposCancha2 = $conexion->query($consultaTiposCancha2);

        while ($filaTipoCancha2 = $resultadosTiposCancha2->fetch_assoc()) {
            $idTipoCancha2 = $filaTipoCancha2["idTarifa"];
            $descripcionTipoCancha2 = $filaTipoCancha2["descripcion"];
            echo "<option value='$idTipoCancha2'>$descripcionTipoCancha2</option>";
        }
        ?>
    </select> !-->
</td>
<input type="hidden" id="idTipoCanchaSeleccionado2" name="idTipoCanchaSeleccionado2">

        <td>
        <button class="btn btn-info btn-modificar" data-id="<?php echo $fila["idHorario"]; ?>" 
        data-idcancha="<?php echo $fila["idCancha"]; ?>"
        data-idtarifa="<?php echo $fila["idTarifa"]; ?>">Modificar</button>
        <button class="btn btn-danger btn-eliminar" data-id="<?php echo $fila["idHorario"]; ?>">Eliminar</button>
            <!-- <button class="btn btn-info btn-modificar" data-id="<?php echo $fila["idCancha"]; ?>" data-descripcion="<?php echo $fila["descripcion"]; ?>">Modificar</button>
                Agregamos un botón "Modificar" con atributos de datos para el ID y descripción del rol -->
        </td>

    </tr>
    </tr>
    <?php endwhile; 
    
    if ($stmt !== null) {
        $stmt->close();
    }
    $conexion->close();
    
    
    ?>
</tbody>

</table>


<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4 text-orange">Crear Nuevo horario</h2>
        <form id="form-crear-rol">
        <div class="form-group">
                <label for="idCancha">Cancha</label>
                <select class="form-control" id="idCancha" name="idCancha">
                    <?php
                    // Conexión a la base de datos
                    //$conexion = new mysqli("localhost", "usuario", "contraseña", "nombre_base_de_datos");
                    $conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");
                    // Verificación de la conexión
                    if ($conexion->connect_error) {
                        die("Error de conexión: " . $conexion->connect_error);
                    }

                    // Consulta para obtener los roles desde la tabla "rol"
                    $consultaRoles = "SELECT idCancha, descripcion FROM cancha";
                    $resultadosRoles = $conexion->query($consultaRoles);

                    // Iterar a través de los resultados y crear opciones para el select
                    while ($filaRol = $resultadosRoles->fetch_assoc()) {
                        $idRol = $filaRol["idCancha"];
                        $descripcionRol = $filaRol["descripcion"];
                        echo "<option value='$idRol'>$descripcionRol</option>";
                    }

                    // Cerrar la conexión a la base de datos
                    $conexion->close();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="idTarifa">Tarifa</label>
                <select class="form-control" id="idTarifa" name="idTarifa">
                    <?php
                    // Conexión a la base de datos
                    //$conexion = new mysqli("localhost", "usuario", "contraseña", "nombre_base_de_datos");
                    $conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");
                    // Verificación de la conexión
                    if ($conexion->connect_error) {
                        die("Error de conexión: " . $conexion->connect_error);
                    }

                    // Consulta para obtener los roles desde la tabla "rol"
                    $consultaRoles2 = "SELECT idTarifa, descripcion FROM tarifa";
                    $resultadosRoles2 = $conexion->query($consultaRoles2);

                    // Iterar a través de los resultados y crear opciones para el select
                    while ($filaRol = $resultadosRoles2->fetch_assoc()) {
                        $idRol2 = $filaRol["idTarifa"];
                        $descripcionRol2 = $filaRol["descripcion"];
                        echo "<option value='$idRol2'>$descripcionRol2</option>";
                    }

                    // Cerrar la conexión a la base de datos
                    $conexion->close();
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Crear Horario</button>
        </form>
    </div>
</div>

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
    <script>
    // Script para manejar el clic en el botón "Modificar"

    // Script para manejar el clic en el botón "Modificar"


// Script para manejar el clic en el botón "Modificar"
/*$(document).on('click', '.btn-modificar', function () {
    const idCancha = $(this).data('id');
    const idTipoCancha = $(this).data('idTipoCancha');
    const nombreActual = $(this).data('nombre');
    const descripcionActual = $(this).data('descripcion');
    
    Swal.fire({
        title: 'Modificar Cancha',
        html: `<input id="swal-input1" class="swal2-input" value="${idCancha}" readonly>
               <input id="swal-input2" class="swal2-input" value="${idTipoCancha}" placeholder="Nuevo Tipo de Cancha">
               <input id="swal-input3" class="swal2-input" value="${nombreActual}" placeholder="Nuevo Nombre">
               <input id="swal-input4" class="swal2-input" value="${descripcionActual}" placeholder="Nueva Descripción">`,
        confirmButtonText: 'Guardar Cambios',
        preConfirm: () => {
            const nuevoIdTipoCancha = Swal.getPopup().querySelector('#swal-input2').value;
            const nuevoNombre = Swal.getPopup().querySelector('#swal-input3').value;
            const nuevaDescripcion = Swal.getPopup().querySelector('#swal-input4').value;
            return { nuevoIdTipoCancha: nuevoIdTipoCancha.trim(), nuevoNombre: nuevoNombre.trim(), nuevaDescripcion: nuevaDescripcion.trim() }
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const nuevoIdTipoCancha = result.value.nuevoIdTipoCancha;
            const nuevoNombre = result.value.nuevoNombre;
            const nuevaDescripcion = result.value.nuevaDescripcion;
            $.ajax({
                type: 'POST',
                url: 'ruta_para_modificar_cancha.php', // Reemplaza con la URL correcta en tu proyecto
                data: { idCancha: idCancha, nuevoIdTipoCancha: nuevoIdTipoCancha, nuevoNombre: nuevoNombre, nuevaDescripcion: nuevaDescripcion },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire('Éxito', 'La Cancha se ha modificado correctamente', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al modificar la Cancha', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Hubo un error al comunicarse con el servidor', 'error');
                }
            });
        }
    });
});*/

// Script para manejar el clic en el botón "Modificar"

var valorJS = <?php echo json_encode($idtc); ?>;
var valorJS2 = <?php echo json_encode($idtc2); ?>;
$(document).on('click', '.btn-modificar', function () {
    const idHorario = $(this).data('id');
    const idCancha = $(this).data('idcancha');
    const idTarifa = $(this).data('idtarifa');
    
    //var valorJS = <?php echo json_encode($descripcionTipoCancha1); ?>;
    console.log(valorJS);

    Swal.fire({
        title: 'Modificar Horario',
        html: `<input id="swal-input1" class="swal2-input" value="${idHorario}" readonly>
               <select class="form-control" id="tipoCanchaSelectSwal"></select>
               <select class="form-control" id="tipoCanchaSelectSwal2"></select>`,
               //<select class="form-control" id="tipoCanchaSelectSwal" value=""></select>`, // Agregar el menú desplegable
        confirmButtonText: 'Guardar Cambios',
        preConfirm: () => {
            const nuevoIdCancha = $('#tipoCanchaSelectSwal').val(); // Obtener el valor seleccionado
            const nuevoIdTarifa = $('#tipoCanchaSelectSwal2').val(); // Obtener el valor seleccionado
            $('#idTipoCanchaSeleccionado').val(nuevoIdCancha); // Actualizar el campo oculto
            $('#idTipoCanchaSeleccionado2').val(nuevoIdTarifa); // Actualizar el campo oculto
            //return { nuevoNombre: nuevoNombre.trim(), nuevaDescripcion: nuevaDescripcion.trim() }
        },
        onOpen: () => {
            // Cargar las opciones del menú desplegable en SweetAlert2
            $.ajax({
                type: 'GET',
                url: '/admin/canchas.php', // Reemplaza con la URL correcta en tu proyecto
                success: function (response) {
                    $('#tipoCanchaSelectSwal').html(response);
                    //$('#tipoCanchaSelectSwal').val(idTipoCanchaActual); // Establecer el valor actual
                    $('#tipoCanchaSelectSwal').val(idCancha);
                   // $('#tipoCanchaSelectSwal2').val(idTarifa);
                    //$('#tipoCanchaSelectSwal2').val();
                    //$('#tipoCanchaSelectSwal option[value="' + idTipoCanchaActual + '"]').prop('selected', true);

                },
                error: function () {
                    Swal.fire('Error', 'Hubo un error al cargar los Horarios', 'error');
                }
            });

            $.ajax({
                type: 'GET',
                url: '/admin/tarifas.php', // Reemplaza con la URL correcta en tu proyecto
                success: function (response) {
                    $('#tipoCanchaSelectSwal2').html(response);
                    //$('#tipoCanchaSelectSwal').val(idTipoCanchaActual); // Establecer el valor actual
                    $('#tipoCanchaSelectSwal2').val(idTarifa);
                   // $('#tipoCanchaSelectSwal2').val(idTarifa);
                    //$('#tipoCanchaSelectSwal2').val();
                    //$('#tipoCanchaSelectSwal option[value="' + idTipoCanchaActual + '"]').prop('selected', true);

                },
                error: function () {
                    Swal.fire('Error', 'Hubo un error al cargar los Horarios', 'error');
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const nuevoIdTipoCancha = $('#idTipoCanchaSeleccionado').val(); // Obtener el valor del campo oculto
            const nuevoIdTipoCancha2 = $('#idTipoCanchaSeleccionado2').val(); // Obtener el valor del campo oculto
            $.ajax({
                type: 'POST',
                url: '/admin/mantenimientos/modificar_horario.php', // Reemplaza con la URL correcta en tu proyecto
                data: { idHorario: idHorario, nuevoIdTipoCancha: nuevoIdTipoCancha, nuevoIdTipoCancha2: nuevoIdTipoCancha2 },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire('Éxito', 'El horario se ha modificado correctamente', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al modificar el Horario', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Hubo un error al comunicarse con el servidor', 'error');
                }
            });
        }
    });
});


// Script para manejar el clic en el botón "Eliminar"
$(document).on('click', '.btn-eliminar', function () {
    const idHorario = $(this).data('id');

    // Mostrar un SweetAlert de confirmación
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¡No podrás revertir esto!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo'
    }).then((result) => {
        if (result.isConfirmed) {
            // El usuario confirmó, enviar solicitud AJAX para eliminar el registro
            $.ajax({
                type: 'POST',
                url: '/admin/mantenimientos/eliminar_horario.php', // Reemplaza con la URL correcta en tu proyecto
                data: { idHorario: idHorario },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire('¡Eliminado!', 'El horario ha sido eliminada.', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al eliminar el horario', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Hubo un error al comunicarse con el servidor', 'error');
                }
            });
        }
    });
});



$(document).ready(function () {
        // Manejar el envío del formulario de creación de rol
        $('#form-crear-rol').submit(function (e) {
            e.preventDefault();
            const idCancha = $('#idCancha').val();
            const idTarifa = $('#idTarifa').val();
            console.log(idCancha);
            // Enviar la solicitud de creación a través de AJAX
            $.ajax({
                type: 'POST',
                url: '/admin/mantenimientos/crear_horario.php', // Reemplaza con la URL correcta en tu proyecto
                data: { idCancha: idCancha, idTarifa: idTarifa },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire('Éxito', 'El horario se ha creado correctamente', 'success').then(() => {
                            // Recargar la página o realizar alguna otra acción después de crear el rol
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al crear el horario', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Hubo un error al comunicarse con el servidor', 'error');
                }
            });
        });
    });



</script>
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