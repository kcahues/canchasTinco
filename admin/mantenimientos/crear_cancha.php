<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["idUsuario"])) {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar si se recibió la descripción del nuevo rol
    if (isset($_POST["descripcion"])) {
        // Conexión a la base de datos
        $conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

        // Verificación de la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Obtener la descripción del nuevo rol desde el formulario
        $idTipoCancha = $_POST["idTipoCancha"];
        $nombre = $_POST["nombre"];
        $descripcion = $_POST["descripcion"];
        
        // Preparar la consulta SQL para insertar el nuevo rol

        $consulta = "INSERT INTO cancha (idTipoCancha, nombre, descripcion) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("iss", $idTipoCancha, $nombre, $descripcion);


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
    header("Location: cancha.php");
    exit();
}
