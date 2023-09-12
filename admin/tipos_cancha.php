<?php
// ruta_para_obtener_tipos_cancha.php
$conexion = new mysqli("srv1107.hstgr.io", "u340286682_adminTinco", "=Uj03A?*", "u340286682_canchas_tinco");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$consultaTiposCancha = "SELECT * FROM tipocancha";
$resultadosTiposCancha = $conexion->query($consultaTiposCancha);

while ($filaTipoCancha = $resultadosTiposCancha->fetch_assoc()) {
    $idTipoCancha = $filaTipoCancha["idTipoCancha"];
    $descripcionTipoCancha = $filaTipoCancha["descripcion"];
    echo "<option value='$idTipoCancha'>$descripcionTipoCancha</option>";
}

$conexion->close();
?>
