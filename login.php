<?php
session_start();
if (isset($_SESSION["idUsuario"])) {
  header("Location: /admin/indexAdmin.php"); // Redirigir a indexAdmin.php si ya ha iniciado sesión
  exit();
}
$error_message = ""; // Variable para almacenar mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $contrasenia = $_POST["contrasenia"];

    $host = "localhost";
    $usuario_db = "u340286682_adminTinco";
    $contrasenia_db = "=Uj03A?*";
    $nombre_db = "u340286682_canchas_tinco";

    $conexion = new mysqli($host, $usuario_db, $contrasenia_db, $nombre_db);

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Preparar la consulta utilizando una sentencia preparada
    $consulta = "SELECT idUsuario, nombre, contrasenia, idRol FROM usuario WHERE correoElectronico = ?";
    $stmt = $conexion->prepare($consulta);
    
    $stmt->bind_param("s", $usuario);

    // Ejecutar la consulta
    $stmt->execute();

    // Almacenar el resultado
    $stmt->store_result();

    // Verificar si se encontró un usuario con el correo dado
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($idUsuario, $nombre, $contraseniaHash, $rol);
        $stmt->fetch();

        // Verificar la contraseña usando password_verify
        if (password_verify($contrasenia, $contraseniaHash)) {
            // Contraseña válida, inicia sesión
            $_SESSION["usuario"] = $usuario;
            $_SESSION["idUsuario"] = $idUsuario;
            $_SESSION["nombre"] = $nombre;
            $_SESSION["idRol"]  = $rol;

            // Redirige al usuario a la página de inicio después del inicio de sesión exitoso
            
            header("Location: /admin/indexAdmin.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Usuario no encontrado.";
    }

    // Cerrar la conexión y liberar recursos
    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <style>
    body {
      background: linear-gradient(to bottom, #D34805, #333);
      color: #ffffff;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }
    .login-container {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .login-heading {
      font-size: 24px;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .login-button {
      background-color: #007bff;
      color: #ffffff;
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="login-container">
  <img src="img/logo.png" alt="Logo de la Empresa" class="mb-4" style="max-width: 70px;">
  <h2 class="login-heading">Iniciar Sesión</h2>
  <form action="login.php" method="post">
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Usuario" name="usuario">
    </div>
    <div class="form-group">
      <input type="password" class="form-control" placeholder="Contraseña" name="contrasenia">
    </div>
    <button type="submit" class="btn btn-primary btn-orange">Iniciar Sesión</button>
  </form>
  <p class="mt-3"> 
    <a href="index.php" class="back-link">Regresar a la página principal</a>
  </p>
  <?php if (!empty($error_message)) : ?>
    <div class="alert alert-danger mt-3">
      <?php echo $error_message; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
