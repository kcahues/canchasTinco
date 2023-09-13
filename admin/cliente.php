<?php
session_start();
$descripcionTipoCancha1 = "";
$idtc = "";
// Verificar si el cliente ha iniciado sesión
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
    $consulta = "SELECT * FROM cliente WHERE nombre LIKE ? ORDER BY idCliente ASC";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("s", $filtro);
    $stmt->execute();
    $resultados = $stmt->get_result();

    // Consulta para contar el número total de registros después de la búsqueda
    $totalRegistrosConsulta = "SELECT COUNT(*) as total FROM cliente WHERE nombre LIKE ?";
    $stmtTotal = $conexion->prepare($totalRegistrosConsulta);
    $stmtTotal->bind_param("s", $filtro);
    $stmtTotal->execute();
    $totalRegistrosResultado = $stmtTotal->get_result();
    $totalRegistros = $totalRegistrosResultado->fetch_assoc()['total'];
} else {
    // Consulta sin filtro de búsqueda
    $consulta = "SELECT * FROM cliente ORDER BY idCliente ASC LIMIT $registrosPorPagina OFFSET $offset";
    $resultados = $conexion->query($consulta);

    // Consulta para contar el número total de registros sin filtro
    $totalRegistrosConsulta = "SELECT COUNT(*) as total FROM cliente";
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
    <title>Gestión de usuarios</title>
    <!-- Incluir los estilos de Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/custom.css">
    
    <script src="/dist/index.global.min.js"></script>
    

<!-- Include FullCalendar JS & CSS library -->
<link href="/js/fullcalendar/lib/main.css" rel="stylesheet" />
<script src="/js/fullcalendar/lib/main.js"></script>

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
            <a class="dropdown-item" href="tipo_usuario.php">Tipo de usuarios</a>
            <a class="dropdown-item" href="tipo_anticipo.php">Tipo de anticipos</a>
            <a class="dropdown-item" href="cliente.php">Cancha</a>
            <a class="dropdown-item" href="tarifa.php">Tarifa</a>
            <a class="dropdown-item" href="estado_reserva.php">Estado Reserva</a>
            <a class="dropdown-item" href="cliente.php">Usuarios</a>
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


    <section class="container my-5 " >

<div class="row">
  <div class="col-md-12">
<h1 class="mb-4 text-orange ">Clientes</h1>
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
$consulta = "SELECT * FROM cliente WHERE nombre LIKE ? ORDER BY idCliente ASC";
$stmt = $conexion->prepare($consulta);
$stmt->bind_param("s", $filtro);
$stmt->execute();
$resultados = $stmt->get_result();
} else {
$consulta = "SELECT * FROM cliente ORDER BY idCliente ASC LIMIT $registrosPorPagina OFFSET $offset";
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
        <th>ID cliente</th>
        
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Telefono</th>
        <th>Telefono Adicional</th>
        <th>Correo Electronico</th>
        <th>Acciones</th>
        
    </tr>
</thead>
<tbody>
    <?php while ($fila = $resultados->fetch_assoc()): ?>
    <tr>
        <td><?php echo $fila["idCliente"]; ?></td>
        <td><?php echo $fila["nombre"]; ?></td>
        <td><?php echo $fila["apellido"]; ?></td>
        <td><?php echo $fila["telefono"]; ?></td>
        <td><?php echo $fila["telefono2"]; ?></td>
        <td><?php echo $fila["correo"]; ?></td>

        <td>
        <?php
        
        if ($fila["idCliente"] !== "1") {
      ?>
        <button class="btn btn-info btn-modificar" data-id="<?php echo $fila["idCliente"]; ?>" 
        
        data-nombre="<?php echo $fila["nombre"]; ?>"
        data-apellido="<?php echo $fila["apellido"]; ?>"
        data-telefono="<?php echo $fila["telefono"]; ?>"
        data-telefono2="<?php echo $fila["telefono2"]; ?>"
        data-correo="<?php echo $fila["correo"]; ?>"
         ?>Modificar</button>
        <button class="btn btn-danger btn-eliminar" data-id="<?php echo $fila["idCliente"]; ?>">Eliminar</button>
            <!-- <button class="btn btn-info btn-modificar" data-id="<?php echo $fila["idCliente"]; ?>" data-descripcion="<?php echo $fila["descripcion"]; ?>">Modificar</button>
                Agregamos un botón "Modificar" con atributos de datos para el ID y descripción del rol -->
           
             <?php } ?>
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
        <h2 class="mb-4 text-orange">Crear Nuevo cliente</h2>
        <form id="form-crear-rol">
            <div class="form-group">
                <label for="descripcion">Descripción de la cliente</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
            </div>
            
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>
            <div class="form-group">
                <label for="telefono">Telefono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="telefono2">Telefono Alternativo</label>
                <input type="text" class="form-control" id="telefono2" name="telefono2" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electronico</label>
                <input type="text" class="form-control" id="correo" name="correo" required>
            </div>  
            
            
            <button type="submit" class="btn btn-primary btn-orange">Crear cliente</button>
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
    const idCliente = $(this).data('id');
    const idRol = $(this).data('idRol');
    const nombreActual = $(this).data('nombre');
    const descripcionActual = $(this).data('descripcion');
    
    Swal.fire({
        title: 'Modificar Cancha',
        html: `<input id="swal-input1" class="swal2-input" value="${idCliente}" readonly>
               <input id="swal-input2" class="swal2-input" value="${idRol}" placeholder="Nuevo Tipo de Cancha">
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
                url: 'ruta_para_modificar_usuario.php', // Reemplaza con la URL correcta en tu proyecto
                data: { idCliente: idCliente, nuevoIdTipoCancha: nuevoIdTipoCancha, nuevoNombre: nuevoNombre, nuevaDescripcion: nuevaDescripcion },
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
$(document).on('click', '.btn-modificar', function () {
    const idCliente = $(this).data('id');
    const nombreActual = $(this).data('nombre');
    const apellidoActual = $(this).data('apellido');
    const telefonoActual = $(this).data('telefono');
    const telefono2Actual = $(this).data('telefono2');
    const correoElectronicoActual = $(this).data('correo');
    const idRolActual = $(this).data('rol'); // Obtener el ID actual

    //var valorJS = <?php echo json_encode($descripcionTipoCancha1); ?>;
    console.log(valorJS);
    console.log(correoElectronicoActual);
    Swal.fire({
        title: 'Modificar cliente',
        html: `<input id="swal-input1" class="swal2-input" value="${idCliente}" readonly>
               <input id="swal-input2" class="swal2-input" value="${nombreActual}" placeholder="Nuevo Nombre">
               <input id="swal-input3" class="swal2-input" value="${apellidoActual}" placeholder="Nuevo Apellido">
               <input id="swal-input4" class="swal2-input" value="${telefonoActual}" placeholder="Nuevo Telefono">
               <input id="swal-input5" class="swal2-input" value="${telefono2Actual}" placeholder="Nuevo Telefono adicional">
               <input id="swal-input6" class="swal2-input" value="${correoElectronicoActual}" placeholder="Nuevo Correo">`,
               //<select class="form-control" id="tipoCanchaSelectSwal" value=""></select>`, // Agregar el menú desplegable
        confirmButtonText: 'Guardar Cambios',
        preConfirm: () => {
            const nuevoNombre = Swal.getPopup().querySelector('#swal-input2').value;
            const nuevoApellido = Swal.getPopup().querySelector('#swal-input3').value;
            const nuevoTelefono = Swal.getPopup().querySelector('#swal-input4').value;
            const nuevoTelefono2 = Swal.getPopup().querySelector('#swal-input5').value;
            const nuevoCorreoElectronico = Swal.getPopup().querySelector('#swal-input6').value;
            return { nuevoNombre: nuevoNombre.trim(), nuevoApellido: nuevoApellido.trim(),
                     nuevoTelefono: nuevoTelefono.trim(), nuevoTelefono2: nuevoTelefono2.trim(),
                      nuevoCorreoElectronico: nuevoCorreoElectronico.trim() }
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const nuevoNombre = result.value.nuevoNombre;
            const nuevoApellido = result.value.nuevoApellido;
            const nuevoCorreoElectronico = result.value.nuevoCorreoElectronico;
            const nuevoTelefono = result.value.nuevoTelefono;
            const nuevoTelefono2 = result.value.nuevoTelefono2;
           
            $.ajax({
                type: 'POST',
                url: '/admin/mantenimientos/modificar_cliente.php', // Reemplaza con la URL correcta en tu proyecto
                data: { idCliente: idCliente, nuevoNombre: nuevoNombre, nuevoApellido: nuevoApellido, nuevoCorreoElectronico: nuevoCorreoElectronico, nuevoTelefono: nuevoTelefono, nuevoTelefono2: nuevoTelefono2 },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire('Éxito', 'El usuari se ha modificado correctamente', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al modificar el cliente', 'error');
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
    const idCliente = $(this).data('id');

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
            // El cliente confirmó, enviar solicitud AJAX para eliminar el registro
            $.ajax({
                type: 'POST',
                url: '/admin/mantenimientos/eliminar_cliente.php', // Reemplaza con la URL correcta en tu proyecto
                data: { idCliente: idCliente },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire('¡Eliminado!', 'El cliente ha sido eliminada.', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al eliminar el cliente', 'error');
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
            
            const nombre = $('#nombre').val();
            const apellido = $('#apellido').val();
            const correoElectronico = $('#correo').val();
            const telefono = $('#telefono').val();
            const telefono2 = $('#telefono2').val();
            console.log(correoElectronico);
            // Enviar la solicitud de creación a través de AJAX
            $.ajax({
                type: 'POST',
                url: '/admin/mantenimientos/crear_cliente.php', // Reemplaza con la URL correcta en tu proyecto
                data: { nombre: nombre, apellido: apellido, correoElectronico: correoElectronico,
                        telefono: telefono, telefono2: telefono2 },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire('Éxito', 'El cliente se ha creado correctamente', 'success').then(() => {
                            // Recargar la página o realizar alguna otra acción después de crear el rol
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al crear el cliente', 'error');
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