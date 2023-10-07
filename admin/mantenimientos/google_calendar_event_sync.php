<?php
session_start();
// Include Google calendar api handler class
include_once 'GoogleCalendarApi.class.php';
    
// Include database configuration file
require_once 'dbConfig.php';

$statusMsg = '';
$status = 'danger';
$event_id = "";
$tipo = "";
$idgoogle = "";
if(isset($_GET['code'])){
    
    // Initialize Google Calendar API class
    $GoogleCalendarApi = new GoogleCalendarApi();
    
    // Get event ID from session
    //$event_id = $_SESSION['last_event_id'];
    //$tipo = $_SESSION['evento']; 
    //$idgoogle = $_SESSION['calendariog'];
    if (isset($_SESSION['last_event_id'])) {
        $event_id = $_SESSION['last_event_id'];
    }
    
    if (isset($_SESSION['evento'])) {
        $tipo = $_SESSION['evento'];
    }
    
    if (isset($_SESSION['calendariog'])) {
        $idgoogle = $_SESSION['calendariog'];
    }
        
    if(!empty($event_id)){
        
        // Fetch event details from database
        $sqlQ = "SELECT * FROM reserva WHERE idReserva = ?";
        $stmt = $db->prepare($sqlQ); 
        $stmt->bind_param("i", $db_event_id);
        $db_event_id = $event_id;
        $stmt->execute();
        $result = $stmt->get_result();
        $eventData = $result->fetch_assoc();
        
        if(!empty($eventData)){
            $calendar_event = array(
                'summary' => $eventData['title'],
                'location' => $eventData['location'],
                'description' => $eventData['description']
            );
            
            $event_datetime = array(
                'event_date' => $eventData['date'],
                'start_time' => $eventData['time_from'],
                'end_time' => $eventData['time_to']
            );
            
            // Get the access token
            $access_token_sess = $_SESSION['google_access_token'];
            //$access_token_sess = '';
            
            if(!empty($access_token_sess)){
                
                $access_token = $access_token_sess;
            }else{
                $data = $GoogleCalendarApi->GetAccessToken(GOOGLE_CLIENT_ID, REDIRECT_URI, GOOGLE_CLIENT_SECRET, $_GET['code']);
                $access_token = $data['access_token'];
                $_SESSION['google_access_token'] = $access_token;
            }
            
            if(!empty($access_token)){
                try {
                    // Get the user's calendar timezone
                    $user_timezone = $GoogleCalendarApi->GetUserCalendarTimezone($access_token);
                    
                    // Create an event on the primary calendar
                    if($tipo === "1"){ //Insertar
                        $google_event_id = $GoogleCalendarApi->CreateCalendarEvent($access_token, 'primary', $calendar_event, 0, $event_datetime, $user_timezone);
                    }elseif($tipo === "2"){
                        //Modificar evento
                        $cadena = implode(', ', $calendar_event);
                        
                        $google_event_id = $GoogleCalendarApi->UpdateCalendarEvent($access_token, 'primary', $idgoogle, $calendar_event, 0, $event_datetime,  $user_timezone); 
                        //die($valor);
                         //= $idgoogle;
                        
                    }elseif($tipo === "3"){
                        //Eliminar evento
                        $google_event_id = $GoogleCalendarApi->DeleteCalendarEvent($access_token, 'primary', $idgoogle);
                        
                    }
                    
                    //$flag = $GoogleCalendarApi->DeleteCalendarEvent($access_token, 'primary', 'eaa85hn15slfospovf5j939ljc');
                    //$valor = $GoogleCalendarApi->UpdateCalendarEvent($access_token, 'primary', '4n1jjd7n117u4gc1br5a2avncc', $calendar_event, 0, $event_datetime,  $event_timezone);
                    //echo json_encode([ 'event_id' => $event_id ]);
                    
                    if($google_event_id){
                        // Update google event reference in the database
                        $sqlQ = "UPDATE reserva SET google_calendar_event_id=? WHERE idReserva=?";
                        $stmt = $db->prepare($sqlQ);
                        $stmt->bind_param("si", $db_google_event_id, $db_event_id);
                        $db_google_event_id = $google_event_id;
                        $db_event_id = $event_id;
                        $update = $stmt->execute();
                        
                        unset($_SESSION['last_event_id']);
                        unset($_SESSION['google_access_token']);
                        
                        $status = 'success';
                        $statusMsg = '<p>Event #'.$event_id.' has been added to Google Calendar successfully!</p>';
                        $statusMsg .= '<p><a href="https://calendar.google.com/calendar/" target="_blank">Open Calendar</a>';
                    }
                } catch(Exception $e) {
                    //header('Bad Request', true, 400);
                    //echo json_encode(array( 'error' => 1, 'message' => $e->getMessage() ));
                    $statusMsg = $e->getMessage();
                }
            }else{
                $statusMsg = 'Failed to fetch access token!';
            }
        }else{
            $statusMsg = 'Event data not found!';
        }
    }else{
        $statusMsg = 'Event reference not found!';
    }
    
    $_SESSION['status_response'] = array('status' => $status, 'status_msg' => $statusMsg);
        if($tipo === "1"){
            header("Location: /admin/reservas_pendientes.php");
        }elseif($tipo === "2"){
            header("Location: /admin/reservas_aceptadas.php");
        }elseif($tipo === "3"){
            header("Location: /admin/reservas_aceptadas.php");
        }
        
    exit();
}
?>