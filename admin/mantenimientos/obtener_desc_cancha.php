<?php
// Verificar si se proporcionó el parámetro 'idTipoCancha' en la solicitud GET
if (isset($_GET['idTipoCancha'])) {
    // Obtener el ID del tipo de cancha desde la solicitud GET
    $idTipoCancha = $_GET['idTipoCancha'];

    // Establecer la configuración de la base de datos
    $servername = "tu_servidor";
    $username = "tu_usuario";
    $password = "tu_contraseña";
    $dbname = "tu_base_de_datos";

    // Crear una conexión a la base de datos
    //$conn = new mysqli($servername, $username, $password, $dbname);
    $conn = new mysqli("localhost", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");


    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error en la conexión a la base de datos: " . $conn->connect_error);
    }

    // Consulta SQL para obtener la descripción del tipo de cancha
    $sql = "SELECT descripcion FROM tipos_cancha WHERE id = $idTipoCancha";

    // Ejecutar la consulta
    $result = $conn->query($sql);

    // Verificar si se encontró una fila
    if ($result->num_rows > 0) {
        // Obtener la fila y la descripción
        $row = $result->fetch_assoc();
        $descripcion = $row["descripcion"];

        // Devolver la descripción como respuesta
        echo $descripcion;
    } else {
        echo 'Descripción no encontrada para el ID de Tipo de Cancha especificado.';
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    echo 'Parámetro "idTipoCancha" no especificado.';
}
?>
