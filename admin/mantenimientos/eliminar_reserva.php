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
            $date = $row['fechaReserva'];
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
    $conexion->close();


    // Conectar a la base de datos
    $conexion = new mysqli("srv1107.hstgr.io", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error en la conexión: " . $conexion->connect_error);
    }

    // Consulta para eliminar la reserva
    $eliminarConsulta = "DELETE FROM reserva WHERE idReserva = ?";
    $stmtEliminar = $conexion->prepare($eliminarConsulta);
    $stmtEliminar->bind_param("i", $idReserva);

    if ($stmtEliminar->execute()) {
        $_SESSION['evento'] = "3"; //Modifica
        $_SESSION['calendariog'] = $calendar;
        $_SESSION['last_event_id'] = $idReserva;
        header("Location: $googleOauthURL");
        // Redirigir de nuevo a la página de reservas pendientes después de la eliminación
      //  header("Location: /admin/indexAdmin.php");
        exit();
    } else {
        echo "Error al eliminar la reserva: " . $stmtEliminar->error;
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {
    // Si no se proporcionó un ID válido en la URL, mostrar un mensaje de error o redirigir a una página de error
}
?>
