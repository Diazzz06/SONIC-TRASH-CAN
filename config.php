<?php
// Definir variables con los detalles de conexión a la base de datos
$servername = "localhost"; // El servidor donde está alojada la base de datos, generalmente "localhost" cuando se trabaja localmente
$username = "root"; // El nombre de usuario para acceder a la base de datos, comúnmente "root" para instalaciones locales de MySQL
$password = ""; // La contraseña para el usuario de la base de datos, en este caso está vacía
$dbname = "login"; // El nombre de la base de datos a la que se quiere conectar

// Crear una nueva conexión a la base de datos utilizando la clase mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) { // Si hubo un error en la conexión
    // Terminar el script y mostrar un mensaje de error
    die("Connection failed: " . $conn->connect_error); // Mostrar el error de conexión
}

// Si la conexión es exitosa, el script continuará ejecutándose normalmente

// Nota: No hay código adicional después de la verificación de la conexión en este archivo,
// por lo que no hay más acciones específicas que tomar aquí. Esta configuración inicial
// se utiliza comúnmente para establecer una conexión que luego puede ser utilizada en otras partes del proyecto.
?>
