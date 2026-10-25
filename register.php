<?php
// Iniciar una sesión. Esto permite guardar información del usuario entre diferentes páginas.
session_start();

// Incluir el archivo de configuración que contiene la conexión a la base de datos.
require 'config.php';

// Verificar si la solicitud se ha realizado mediante el método POST (es decir, si el formulario se ha enviado).
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el nombre de usuario y la contraseña del formulario de registro.
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar si el nombre de usuario ya existe en la base de datos.
    $stmt_check_username = $conn->prepare('SELECT id FROM usuarios WHERE username = ?');
    // Vincular el nombre de usuario ingresado a la declaración preparada.
    $stmt_check_username->bind_param('s', $username);
    // Ejecutar la declaración.
    $stmt_check_username->execute();
    // Almacenar el resultado de la consulta.
    $stmt_check_username->store_result();

    // Si el nombre de usuario ya existe, mostrar un mensaje de error.
    if ($stmt_check_username->num_rows > 0) {
        echo 'El nombre de usuario ya está en uso.';
    } else {
        // Si el nombre de usuario no existe, proceder con el registro del nuevo usuario.
        // Hashear la contraseña para almacenarla de forma segura en la base de datos.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Preparar una declaración SQL para insertar el nuevo usuario en la base de datos.
        $stmt_registro_usuario = $conn->prepare('INSERT INTO usuarios (username, password) VALUES (?, ?)');
        // Vincular el nombre de usuario y la contraseña hasheada a la declaración preparada.
        $stmt_registro_usuario->bind_param('ss', $username, $hashed_password);

        // Ejecutar la declaración y verificar si se insertó el usuario correctamente.
        if ($stmt_registro_usuario->execute()) {
            // Obtener el ID del nuevo usuario registrado.
            $user_id = $stmt_registro_usuario->insert_id;

            // Registrar el evento en la tabla de registro de usuarios.
            $stmt_registro_evento = $conn->prepare('INSERT INTO registro_usuarios (user_id) VALUES (?)');
            // Vincular el ID del usuario a la declaración preparada.
            $stmt_registro_evento->bind_param('i', $user_id);
            // Ejecutar la declaración.
            $stmt_registro_evento->execute();

            // Iniciar la sesión del usuario.
            $_SESSION['loggedin'] = true; // Guardar el estado de sesión iniciada.
            $_SESSION['username'] = $username; // Guardar el nombre de usuario en la sesión.
            $_SESSION['id'] = $user_id; // Guardar el ID del usuario en la sesión.

            // Redirigir al usuario a la página principal después del registro.
            header('Location: index.html');
            exit;
        } else {
            // Si hubo un error al registrar el usuario, mostrar un mensaje de error.
            echo 'Error al registrar el usuario: ' . $stmt_registro_usuario->error;
        }

        // Cerrar la declaración para liberar recursos.
        $stmt_registro_usuario->close();
    }

    // Cerrar la declaración de verificación del nombre de usuario para liberar recursos.
    $stmt_check_username->close();
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
    <title>Registro</title>
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

        .login-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
    <nav>
        <a href="index.html">
            <img src="Images/LOGOSONICTRASHCAN.png">
        </a>
    </nav>
<body>
    <div class="container">
        <h2>¡UNETE A LA COMUNIDAD!</h2>
        <!-- Formulario para registrar un nuevo usuario -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <!-- Campo para el nombre de usuario -->
            <label for="username">Crea tu nombre de usuario:</label>
            <input type="text" id="username" name="username" required>
            <!-- Campo para la contraseña -->
            <label for="password">Crea tu contraseña:</label>
            <input type="password" id="password" name="password" required>
            <!-- Botón para enviar el formulario -->
            <button type="submit">Registrarse</button>
        </form>
        <!-- Enlace para iniciar sesión si ya tiene una cuenta -->
        <div class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</body>

</html>
