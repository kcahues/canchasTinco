<?php
// Verificar si la solicitud es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera los datos enviados por AJAX
    $idRol = $_POST["idRol"];
    $nuevaDescripcion = $_POST["nuevaDescripcion"];

    // Realiza la validación de datos (puedes agregar más validaciones según tus necesidades)
    if (empty($idRol) || empty($nuevaDescripcion)) {
        echo "error"; // Envía una respuesta de error si los datos están incompletos
        exit();
    }

    // Conecta a la base de datos (reemplaza los valores con los de tu configuración)
    $conexion = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");


    // Verificación de la conexión
    if ($conexion->connect_error) {
        echo "error";
        exit();
    }

    // Prepara y ejecuta la consulta para actualizar el rol
    $consulta = "UPDATE rol SET descripcion = ? WHERE idRol = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("si", $nuevaDescripcion, $idRol);

    if ($stmt->execute()) {
        echo "success"; // La actualización se realizó con éxito
        
    } else {
        echo "error"; // Hubo un error al actualizar el rol
    }

    // Cierra la conexión y el statement
    $stmt->close();
    $conexion->close();
} else {
    // Si la solicitud no es POST, muestra un mensaje de error
    echo "error";
}
?>
