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

$request_type = $_POST['request_type'];

/*if ($request_type === 'addEvent' ) {
    $start = $_POST['start'];
    $end = $_POST['end'];
    $event_data = $_POST['event_data'];
    
     // Prepara los datos para la consulta
     $title = $event_data[0];
     $description = $event_data[1];
     $location = $event_data[2];
     $date = strval($event_data[3]);
     $time_from = strval($event_data[4]);
     $time_to = strval($event_data[5]);

     // Inserta un nuevo evento en la base de datos
    $sql = "INSERT INTO events (title, description, location, date, time_from, time_to, google_calendar_event_id, created) 
    VALUES ('$title', '$description', '$location', '$date', '$time_from', '$time_to', NULL, NOW())";

    // Inserta un nuevo evento en la base de datos (ajusta la consulta SQL)
  //  $sql = "INSERT INTO events (title, description, location, date, time_from, time_to, google_calendar_event_id, created) VALUES (?, ?, ?, ?, ?, ?, NULL, NOW())";
  //  $stmt = $conn->prepare($sql);
  //  $stmt->bind_param("ssssss", $event_data[0], $event_data[1], $event_data[2], $event_data[3], $event_data[4], $event_data[5]);

  if ($conn->query($sql) === TRUE) {
    echo json_encode(array('status' => 1));
} else {
    echo json_encode(array('status' => 0, 'error' => 'Error al agregar evento.'));
}
   // if ($stmt->execute() === TRUE) {
   //     echo json_encode(array('status' => 1));
   // } else {
    //    echo json_encode(array('status' => 0, 'error' => 'Error al agregar evento.'));
    //}
    
    //$stmt->close();
}*/
if ($request_type === 'addEvent' ) {
    $start = $_POST['start'];
    $end = $_POST['end'];
    $event_data = json_decode($_POST['event_data'], true);
    
    // Inserta un nuevo evento en la base de datos
    $sql = "INSERT INTO events (title, description, location, date, time_from, time_to, google_calendar_event_id, created) VALUES (?, ?, ?, ?, ?, ?, NULL, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $event_data[0], $event_data[1], $event_data[2], $event_data[3], $event_data[4], $event_data[5]);

    if ($stmt->execute() === TRUE) {
        echo json_encode(array('status' => 1));
    } else {
        echo json_encode(array('status' => 0, 'error' => 'Error al agregar evento.'));
    }

    $stmt->close();
}

if ($request_type === 'addEvent2') {
    // Validación de datos (puedes agregar más validaciones según tus requisitos)
    if (empty($event_data['title']) || empty($event_data['date']) || empty($event_data['time_from']) || empty($event_data['time_to'])) {
        echo 'error'; // Retorna un error si faltan datos obligatorios
        exit;
    }

    // Aquí puedes realizar la inserción en la base de datos con los datos recibidos
    // Asegúrate de ajustar la consulta SQL y los nombres de las columnas de acuerdo a tu base de datos
    $title = $POST['title'];
    $description = $POST['description'];
    $location = $POST['location'];
    $date = $POST['date'];
    $time_from = $POST['time_from'];
    $time_to = $POST['time_to'];

    // Realiza la inserción en la base de datos
    $sql = "INSERT INTO events (title, description, location, date, time_from, time_to, google_calendar_event_id, created) VALUES (?, ?, ?, ?, ?, ?, NULL, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $title, $description, $location, $date, $time_from, $time_to);

    if ($stmt->execute()) {
        echo 'success'; // Retorna 'success' si la inserción se realizó correctamente
    } else {
        echo 'error'; // Retorna 'error' si hubo un error al insertar el evento
    }

    $stmt->close();
}

if ($request_type === 'editEvent') {
    // Obtener los datos del evento a editar
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $time_from = $_POST['time_from'];
    $time_to = $_POST['time_to'];

    // Realiza la edición del evento en la base de datos
    // Aquí debes escribir tu lógica para actualizar los datos del evento

    // Por ejemplo, puedes actualizar la descripción y la ubicación del evento
    $new_description = $description;
    $new_location = $location;

    $sql =  "UPDATE events SET title=?, description=?, location=?, date=?, time_from=?, time_to=? WHERE id=?";
   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $title, $description, $location, $date, $time_from, $time_to, $event_id);

    if ($stmt->execute()) {
        echo 'success'; // Envía una respuesta de éxito al cliente
    } else {
        echo 'error'; // Envía una respuesta de error al cliente
    }
    /*if ($stmt->execute() === TRUE) {
        echo json_encode(array('status' => 1));
    } else {
        echo json_encode(array('status' => 0, 'error' => 'Error al editar evento.'));
    }*/

    $stmt->close();
    // Realiza una consulta SQL para actualizar los datos del evento
    // ... (tu código de actualización aquí)

    // Si la actualización fue exitosa, envía 'success' como respuesta
    // Si ocurrió un error, envía un mensaje de error apropiado
    // ...

    //echo 'success';
}

/*if ($request_type === 'editEvent') {
    $event_id = $_POST['event_id'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $event_data = $_POST['event_data'];

    // Actualiza un evento existente en la base de datos (ajusta la consulta SQL)
    $sql = "UPDATE events SET title=?, description=?, location=?, date=?, time_from=?, time_to=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $event_data[0], $event_data[1], $event_data[2], $event_data[3], $event_data[4], $event_data[5], $event_id);

    if ($stmt->execute() === TRUE) {
        echo json_encode(array('status' => 1));
    } else {
        echo json_encode(array('status' => 0, 'error' => 'Error al editar evento.'));
    }

    $stmt->close();
}
*/
if ($request_type === 'deleteEvent') {
    $event_id = $_POST['event_id'];

    // Elimina un evento de la base de datos (ajusta la consulta SQL)
    $sql = "DELETE FROM events WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo 'success'; // Envía una respuesta de éxito al cliente
    } else {
        echo 'error'; // Envía una respuesta de error al cliente
    }
}

if ($request_type === 'getEventById') {
    // Obtiene el ID del evento que deseas consultar desde la solicitud POST
    $event_id = $_POST['event_id'];

    // Realiza una consulta SQL para obtener la información del evento por su ID
    $sql = "SELECT id, title, description, location, date, time_from, time_to FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $event_data = $result->fetch_assoc();

        if ($event_data) {
            // Retorna la información del evento como un array JSON
            echo json_encode($event_data);
        } else {
            // El evento con el ID especificado no existe
            echo 'not_found';
        }
    } else {
        // Hubo un error al ejecutar la consulta
        echo 'error';
    }

    $stmt->close();
}

$conn->close();
?>
