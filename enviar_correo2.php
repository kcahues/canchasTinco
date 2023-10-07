<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './Exception.php';
require './PHPMailer.php';
require './SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopila los datos del formulario
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $mensaje = $_POST["mensaje"];

    // Configura PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.titan.email'; // Reemplaza con el servidor SMTP de tu proveedor de correo
        $mail->SMTPAuth = true;
        $mail->Username = 'info@canchastinco.com'; // Reemplaza con tu dirección de correo
        $mail->Password = 'Cu!##nWY_j.3J!u'; // Reemplaza con tu contraseña
        $mail->SMTPSecure = 'ssl'; // Puedes cambiar a 'ssl' si es necesario
        $mail->Port = 465 ; // Puerto de SMTP, puede variar según tu proveedor de correo
        //$mail->SMTPDebug = 2;
        // Configuración del correo
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('info@canchastinco.com', 'Correo Canchas Tinco');
        //$mail->setFrom($email, $nombre);
        $mail->addAddress($email); // Reemplaza con la dirección de correo de destino
        $mail->isHTML(true);
        $mail->Subject = 'Correo información';
        $mail->Body = $mensaje;

        // Envía el correo
        $mail->send();
        echo 'El mensaje se ha enviado con éxito.';
    } catch (Exception $e) {
        echo 'Error al enviar el mensaje: ' . $mail->ErrorInfo;
    }
}
?>
