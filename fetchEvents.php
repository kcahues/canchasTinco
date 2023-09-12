<?php     
// Include database configuration file 
require_once 'dbConfig.php';

// Filter events by calendar date
$where_sql = '';
if(!empty($_GET['date'])){
    $where_sql .= " WHERE date BETWEEN '".$_GET['date']."' AND '".$_GET['date']."' ";
}

// Fetch events from database
$sql = "SELECT * FROM events $where_sql";
$result = $db->query($sql); 

$eventsArr = array();
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        array_push($eventsArr, $row);
    }
}

// Render event data in JSON format
echo json_encode($eventsArr);