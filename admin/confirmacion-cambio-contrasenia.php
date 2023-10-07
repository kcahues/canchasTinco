<?php
session_start();
if (isset($_SESSION["idUsuario"])) {
  header("Location: /admin/indexAdmin.php"); // Redirigir a indexAdmin.php si ya ha iniciado sesión
  exit();
}

$error_message = ""; // Variable para almacenar mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigoConfirmacion = $_POST["codigo_confirmacion"]; // Campo donde el usuario ingresa el código de confirmación

    // Aquí debes agregar la lógica para verificar el código de confirmación ingresado por el usuario.

    // Si el código de confirmación es válido, redirige al usuario a la página de cambio de contraseña.
    header("Location: cambio-contrasenia.php");
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
    <!-- Aquí puedes agregar un formulario para que el usuario confirme el cambio de contraseña con el código -->
    <h2 class="login-heading">Confirmar Cambio de Contraseña</h2>
    <form action="confirmacion-cambio-contrasenia.php" method="post">
      <div class="form-group">
        <input type="text" class="form-control" placeholder="Código de Confirmación" name="codigo_confirmacion">
      </div>
      <button type="submit" class="btn btn-primary btn-orange">Confirmar Cambio de Contraseña</button>
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
