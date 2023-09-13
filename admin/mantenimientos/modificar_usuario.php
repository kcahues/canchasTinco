<?php
session_start();

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

// Obtener datos del formulario
$idUsuario = $_POST["idUsuario"];
$nuevoRol = $_POST["nuevoIdTipoCancha"];
$nuevoNombre = $_POST["nuevoNombre"];
$nuevoApellido = $_POST["nuevoApellido"];
$nuevoCorreoElectronico = $_POST["nuevoCorreoElectronico"];
$nuevoTelefono = $_POST["nuevoTelefono"];

// Consulta SQL para actualizar la cancha
$sql = "UPDATE usuario SET nombre = ?, apellido = ?, correoElectronico = ?, telefono = ?, idRol = ? WHERE idUsuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssii", $nuevoNombre, $nuevoApellido, $nuevoCorreoElectronico, $nuevoTelefono, $nuevoRol,  $idUsuario);

if ($stmt->execute()) {
    echo "success"; // Éxito, la cancha se actualizó correctamente
} else {
    echo "error"; // Hubo un error al actualizar la cancha
}

$stmt->close();
$conexion->close();
?>
