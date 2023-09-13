<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["idUsuario"])) {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idUsuario"])) {
    $idUsuario = $_POST["idUsuario"];

    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");


    // Verificación de la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Sentencia SQL para eliminar el rol por su ID
    $consulta = "DELETE FROM usuario WHERE idUsuario = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("i", $idUsuario);

    if ($stmt->execute()) {
        echo "success"; // Éxito: devuelve "success" como respuesta
    } else {
        echo "error"; // Error: devuelve "error" como respuesta
    }

    $stmt->close();
    $conexion->close();
} else {
    // Si no se proporciona un ID de rol válido, devuelve un mensaje de error
    echo "error";
}
