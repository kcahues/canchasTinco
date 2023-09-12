<?php 
// Database configuration 
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'u340286682_adminTinco');
define('DB_PASSWORD', '=Uj03A?*');
define('DB_NAME', 'u340286682_canchas_tinco');
 
// Create database connection 
$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME); 

// Check connection 
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error); 
}
