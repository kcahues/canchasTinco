<?php
session_start();
if (isset($_SESSION["idUsuario"])) {
  header("Location: /admin/indexAdmin.php"); // Redirigir a indexAdmin.php si ya ha iniciado sesión
  exit();
}

$error_message = ""; // Variable para almacenar mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevaContrasenia = $_POST["nueva_contrasenia"]; // Campo donde el usuario ingresa la nueva contraseña

    // Aquí debes agregar la lógica para cambiar la contraseña del usuario en la base de datos.

    // Después de cambiar la contraseña, puedes redirigir al usuario a la página de inicio de sesión.
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Agrega tus metadatos y enlaces a hojas de estilo aquí -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Agrega tus metadatos y enlaces a hojas de estilo aquí -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/custom.css">
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
    <!-- Aquí puedes agregar un formulario para que el usuario ingrese su nueva contraseña -->
    <h2 class="login-heading">Cambiar Contraseña</h2>
    <form action="cambio-contrasenia.php" method="post">
      <div class="form-group">
        <input type="password" class="form-control" placeholder="Nueva Contraseña" name="nueva_contrasenia">
      </div>
      <button type="submit" class="btn btn-primary btn-orange">Cambiar Contraseña</button>
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
</body>
</html>
