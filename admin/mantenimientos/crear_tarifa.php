<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["idUsuario"])) {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar si se recibió la descripción del nuevo rol
    if (isset($_POST["descripcion"]) && isset($_POST["horaIni"]) && isset($_POST["horaFin"]) && isset($_POST["precio"])) {
        // Conexión a la base de datos
        $conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

        // Verificación de la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Obtener la descripción del nuevo rol desde el formulario
        $horaIni = $_POST["horaIni"];
        $horaFin = $_POST["horaFin"];
        $precio = $_POST["precio"];
        $descripcion = $_POST["descripcion"];

        // Preparar la consulta SQL para insertar el nuevo rol
        $consulta = "INSERT INTO tarifa (horaIni, horaFin, precio, descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("ssss", $horaIni, $horaFin, $precio, $descripcion);

        if ($stmt->execute()) {
            // Éxito: el rol se ha creado correctamente
            echo "success";
        } else {
            // Error: hubo un problema al crear el rol
            echo "error";
        }

        // Cerrar la conexión y liberar recursos
        $stmt->close();
        $conexion->close();
    } else {
        // Error: no se recibieron los datos del formulario
        echo "error";
    }
} else {
    // Redireccionar si la solicitud no es POST
    header("Location: tarifa.php");
    exit();
}
