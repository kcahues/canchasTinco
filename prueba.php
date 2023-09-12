<?php

$servername = "srv1107.hstgr.io";
$username = "u340286682_adminTinco";
$password = "=Uj03A?*";
$dbname = "u340286682_canchas_tinco";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$title = 'Titulo';
$description = 'Descripcion';
$location = 'Locacion';
$date = '2023-09-07';
$time_from = '18:00:00';
$time_to = '19:00:00';

// Inserta un nuevo evento en la base de datos
$sql = "INSERT INTO events (title, description, location, date, time_from, time_to, google_calendar_event_id, created) 
VALUES ('$title', '$description', '$location', '$date', '$time_from', '$time_to', NULL, NOW())";

if ($conn->query($sql) === TRUE) {
    echo json_encode(array('status' => 1));
} else {
    echo json_encode(array('status' => 0, 'error' => 'Error al agregar evento.'));
}
?>