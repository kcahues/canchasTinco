<?php
// Datos de conexión a la base de datos

// Datos del usuario a modificar
$idUsuario = 2; // ID del usuario que deseas modificar
$nuevaContrasenia = "12345"; // Nueva contraseña sin hash

// Generar el hash de la nueva contraseña
$hashNuevaContrasenia = password_hash($nuevaContrasenia, PASSWORD_DEFAULT);
$host = "localhost";
    $usuario_db = "root";
    $contrasenia_db = "";
    $nombre_db = "canchas_tinco";
// Crear la conexión a la base de datos
$conexion = new mysqli($host, $usuario_db, $contrasenia_db, $nombre_db);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Preparar la consulta
$consulta = "UPDATE usuario SET contrasenia = ? WHERE idUsuario = ?";
$stmt = $conexion->prepare($consulta);
$stmt->bind_param("si", $hashNuevaContrasenia, $idUsuario);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Contraseña modificada con éxito.";
} else {
    echo "Error al modificar la contraseña: " . $stmt->error;
}

// Cerrar la conexión y liberar recursos
$stmt->close();
$conexion->close();
?>
