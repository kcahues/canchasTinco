<?php
// Conecta a tu base de datos (ajusta los detalles de conexión)
$servername = "srv1107.hstgr.io";
$username = "u340286682_adminTinco";
$password = "=Uj03A?*";
$dbname = "u340286682_canchas_tinco";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verifica si la vista solicitada es "week", "month" o "day" y ajusta la consulta SQL en consecuencia
$view = $_POST['view']; // Recibe la vista desde FullCalendar

if ($view === 'week') {
    $sql = "SELECT id, title, description, location, date, time_from, time_to, google_calendar_event_id, created FROM events";
} elseif ($view === 'month') {
    $sql = "SELECT id, title, description, location, date, time_from, time_to, google_calendar_event_id, created FROM events WHERE MONTH(date) = MONTH(NOW())";
} elseif ($view === 'day') {
    $sql = "SELECT id, title, description, location, date, time_from, time_to, google_calendar_event_id, created FROM events WHERE DATE(date) = DATE(NOW())";
} else {
    $sql = "SELECT id, title, description, location, date, time_from, time_to, google_calendar_event_id, created FROM events";
}

$result = $conn->query($sql);

$events = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = array(
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'location' => $row['location'],
            'date' => $row['date'],
            'start' => $row['date'] . ' ' . $row['time_from'],
            'end' => $row['date'] . ' ' . $row['time_to'],
            'google_calendar_event_id' => $row['google_calendar_event_id'],
            'created' => $row['created']
        );
    }
}

echo json_encode($events);

$conn->close();
?>
