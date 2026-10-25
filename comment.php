<?php
// Iniciar una sesión o reanudar la sesión existente. Esto permite almacenar y acceder a datos de sesión para cada usuario.
session_start();

// Incluir el archivo de configuración, que contiene los detalles de la base de datos y otras configuraciones necesarias.
require 'config.php';

// Verificar si el usuario está logueado comprobando una variable de sesión. Si no está logueado, redirigir a la página de inicio de sesión.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirige al usuario a login.php si no está logueado.
    exit; // Termina el script para asegurarse de que no se ejecuten más instrucciones.
}

// Procesar el formulario de comentario si se ha enviado un formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el ID del usuario de la sesión actual
    $user_id = $_SESSION['id'];
    // Obtener el comentario enviado por el usuario desde el formulario
    $comment = $_POST['comment'];

    // Validar que el comentario no esté vacío
    if (!empty($comment)) {
        // Preparar una consulta SQL para insertar el comentario en la base de datos
        $stmt = $conn->prepare('INSERT INTO comentarios (user_id, comentario) VALUES (?, ?)');
        // Vincular los parámetros de la consulta (ID del usuario y el comentario) a la declaración preparada
        $stmt->bind_param('is', $user_id, $comment);

        // Ejecutar la consulta y comprobar si se insertó correctamente
        if ($stmt->execute()) {
            // Cerrar la declaración y la conexión a la base de datos para liberar recursos
            $stmt->close();
            $conn->close();
            // Redirigir al usuario a la misma página para evitar resubmisión del formulario
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            // Mostrar un mensaje de error si hubo un problema al guardar el comentario
            echo 'Error al guardar el comentario: ' . $stmt->error;
        }
    } else {
        // Guardar un mensaje de error si el comentario está vacío
        $error = 'El comentario no puede estar vacío.';
    }
}

// Preparar una consulta SQL para obtener todos los comentarios de la base de datos, junto con el nombre de usuario y la fecha
$stmt = $conn->prepare('SELECT c.comentario, c.fecha, u.username FROM comentarios c JOIN usuarios u ON c.user_id = u.id ORDER BY c.fecha DESC');
// Ejecutar la consulta
$stmt->execute();
// Almacenar los resultados de la consulta
$stmt->store_result();
// Vincular los resultados de la consulta a variables (comentario, fecha, nombre de usuario)
$stmt->bind_result($comentario, $fecha, $username);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Comentarios</title>
    <style>
        /* Estilos CSS para dar estilo a la página */
        body {
            background-color: #000611;
            color: #FFFFFF;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 800px;
            margin: 50px auto;
            background-color: #333;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
        }

        .comment {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #555;
        }

        .comment p {
            margin-bottom: 5px;
        }

        .comment .meta {
            font-size: 12px;
            color: #999;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            background-color: #555;
            color: #FFFFFF;
            resize: none;
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

        .boton {
            text-align: center;
            color: #000611;
            text-decoration: none;
            padding: 10px;
            background-color: #FFFFFF;
            border-radius: 5px;
        }

        .boton:hover {
            background-color: #ddd;
        }

        nav {
            margin-top: 20px;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Navegación con un enlace de vuelta a la página de inicio -->
    <nav>
        <a href="index.html" class="boton">Inicio</a>
    </nav>
    <div class="container">
        <h2>Comentarios</h2>
        <!-- Formulario para agregar un comentario -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="comment">Escribe tu comentario:</label><br>
            <!-- Campo de texto para el comentario -->
            <textarea id="comment" name="comment" required></textarea><br>
            <!-- Botón para enviar el formulario -->
            <button type="submit">Publicar Comentario</button>
        </form>
        <!-- Mostrar un mensaje de error si hay uno -->
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <hr>

        <!-- Lista de comentarios -->
        <?php while ($stmt->fetch()) : ?>
            <div class="comment">
                <!-- Mostrar el comentario -->
                <p><?php echo htmlspecialchars($comentario); ?></p>
                <div class="meta">
                    <!-- Mostrar el nombre de usuario y la fecha del comentario -->
                    <span>Usuario: <?php echo htmlspecialchars($username); ?></span>
                    <span>Fecha: <?php echo htmlspecialchars($fecha); ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>

<?php
// Cerrar la declaración y la conexión a la base de datos para liberar recursos
$stmt->close();
$conn->close();
?>
