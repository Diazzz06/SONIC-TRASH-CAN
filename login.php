<?php
// Iniciar una sesión. Esto permite guardar información del usuario entre diferentes páginas.
session_start();

// Incluir el archivo de configuración que contiene la conexión a la base de datos.
require 'config.php';

// Verificar si la solicitud se ha realizado mediante el método POST (es decir, si el formulario se ha enviado).
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el nombre de usuario y la contraseña del formulario de inicio de sesión.
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Preparar una declaración SQL para verificar las credenciales del usuario.
    $stmt = $conn->prepare('SELECT id, password FROM usuarios WHERE username = ?');
    // Vincular el nombre de usuario ingresado a la declaración preparada.
    $stmt->bind_param('s', $username);
    // Ejecutar la declaración.
    $stmt->execute();
    // Almacenar el resultado de la consulta.
    $stmt->store_result();

    // Verificar si se encontró un usuario con el nombre de usuario proporcionado.
    if ($stmt->num_rows > 0) {
        // Vincular los resultados de la consulta a las variables.
        $stmt->bind_result($id, $hashed_password);
        // Obtener los valores de las variables.
        $stmt->fetch();

        // Verificar si la contraseña ingresada coincide con la contraseña almacenada (hasheada) en la base de datos.
        if (password_verify($password, $hashed_password)) {
            // Si la contraseña es correcta, iniciar la sesión del usuario.
            $_SESSION['loggedin'] = true; // Guardar el estado de sesión iniciada.
            $_SESSION['username'] = $username; // Guardar el nombre de usuario en la sesión.
            $_SESSION['id'] = $id; // Guardar el ID del usuario en la sesión.
            // Redirigir al usuario a la página principal después de un inicio de sesión exitoso.
            header('Location: index.html');
            exit;
        } else {
            // Si la contraseña es incorrecta, mostrar un mensaje de error.
            $error = 'La contraseña es incorrecta.';
        }
    } else {
        // Si no se encontró el usuario, mostrar un mensaje de error.
        $error = 'No se encontró el usuario.';
    }

    // Cerrar la declaración para liberar recursos.
    $stmt->close();
}

// Cerrar la conexión a la base de datos para liberar recursos.
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Iniciar sesión</title>
    <style>
        body {
            background-color: #000611;
            color: #FFFFFF;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 300px;
            margin: 100px auto;
            background-color: #333;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 2px;
            margin-bottom: 15px;
            border: none;
            background-color: #555;
            color: #FFFFFF;
        }

        button[type="submit"] {
            background-color: #000611;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #001D33;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }

        .register-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <nav>
        <a href="index.html">
            <img src="Images/LOGOSONICTRASHCAN.png">
        </a>
    </nav>

    <div class="container">
        <h2>INGRESA A TU CUENTA</h2>
        <!-- Formulario para iniciar sesión -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <!-- Campo para el nombre de usuario -->
            <label for="username">Nombre de usuario:</label>
            <input type="text" id="username" name="username" required>
            <!-- Campo para la contraseña -->
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <!-- Botón para enviar el formulario -->
            <button type="submit">Iniciar sesión</button>
        </form>
        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <!-- Enlace para registrar una nueva cuenta -->
        <div class="register-link">
            ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
        </div>
    </div>
</body>

</html>
