<?php
session_start();
require_once("../Cronometro.php");

$errorFormulario = false;
$errores = [];
$formularioPOST = "";

// Arrancar cronómetro al pulsar "Iniciar prueba"
if (isset($_POST['iniciar'])) {
    $_SESSION['cronometro'] = new Cronometro();
    $_SESSION['cronometro']->arrancar();
}

// Procesar formulario al pulsar "Terminar prueba"
if (isset($_POST['terminar'])) {
    $formularioPOST = $_POST;

    // Validar que todas las preguntas tienen respuesta
    for ($i = 1; $i <= 10; $i++) {
        if (empty($_POST["p$i"])) {
            $errores["p$i"] = " * Esta pregunta es obligatoria";
            $errorFormulario = true;
        }
    }

    if (!$errorFormulario) {
        // Detener cronómetro
        $tiempoTotal = $_SESSION['cronometro']->parar();

        // Conexión BD
        $db = new mysqli("localhost", "DBUSER2025", "DBPSWD2025", "UO301919_DB");
        if ($db->connect_error) {
            exit("Error de conexión: " . $db->connect_error);
        }

        // Insertar usuario (ejemplo: datos básicos)
        $stmt = $db->prepare("INSERT INTO Usuario (id_profesion, edad, id_genero, pericia_informatica) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $_POST['profesion'], $_POST['edad'], $_POST['genero'], $_POST['pericia']);
        $stmt->execute();
        $id_usuario = $stmt->insert_id;

        // Insertar resultado de usabilidad
        $stmt = $db->prepare("INSERT INTO ResultadoUsabilidad (id_usuario, id_dispositivo, tiempo, completado, comentarios_usuario, mejoras, valoracion) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisssi", $id_usuario, $_POST['dispositivo'], $tiempoTotal, 1, $_POST['comentarios_usuario'], $_POST['mejoras'], $_POST['valoracion']);
        $stmt->execute();

        // Insertar observación del facilitador
        $stmt = $db->prepare("INSERT INTO Observacion (id_usuario, comentarios_facilitador) VALUES (?, ?)");
        $stmt->bind_param("is", $id_usuario, $_POST['comentarios_facilitador']);
        $stmt->execute();

        echo "<p>Prueba completada y datos guardados en la BD.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba de Usabilidad MotoGP-Desktop</title>
    <link rel="stylesheet" href="../estilo/estilo.css">
    <link rel="stylesheet" href="../estilo/layout.css">
</head>
<body>
    <h2>Prueba de Usabilidad</h2>

    <!-- Botón para iniciar prueba -->
    <form method="post">
        <button type="submit" name="iniciar">Iniciar Prueba</button>
    </form>

    <!-- Formulario de preguntas -->
    <form method="post">
        <p>1. ¿Cuál es el nombre del piloto destacado?</p>
        <input type="text" name="p1"> <span><?= $errores["p1"] ?? "" ?></span>

        <p>2. ¿Qué día fue la carrera destacada?</p>
        <input type="text" name="p2"> <span><?= $errores["p2"] ?? "" ?></span>

        <p>3. ¿Cuál es el nombre del circuito destacado?</p>
        <input type="text" name="p3"> <span><?= $errores["p3"] ?? "" ?></span>

        <p>4. ¿Quién fue el ganador de la carrera?</p>
        <input type="text" name="p4"> <span><?= $errores["p4"] ?? "" ?></span>

        <p>5. ¿En qué país se llevó a cabo la carrera?</p>
        <input type="text" name="p5"> <span><?= $errores["p5"] ?? "" ?></span>

        <p>6. ¿Qué moto conduce el piloto destacado?</p>
        <input type="text" name="p6"> <span><?= $errores["p6"] ?? "" ?></span>

        <p>7. ¿Cuál fue la temperatura el día de la carrera?</p>
        <input type="text" name="p7"> <span><?= $errores["p7"] ?? "" ?></span>

        <p>8. ¿Cuál fue el tiempo total del ganador?</p>
        <input type="text" name="p8"> <span><?= $errores["p8"] ?? "" ?></span>

        <p>9. ¿Cuántas cartas hay en el juego de memoria?</p>
        <input type="text" name="p9"> <span><?= $errores["p9"] ?? "" ?></span>

        <p>10. ¿Cuál fue la temperatura en los entrenamientos del 21 de agosto?</p>
        <input type="text" name="p10"> <span><?= $errores["p10"] ?? "" ?></span>

        <!-- Datos adicionales -->
        <p>Edad: <input type="number" name="edad"></p>
        <p>Profesión (id): <input type="number" name="profesion"></p>
        <p>Género (id): <input type="number" name="genero"></p>
        <p>Pericia informática (0-10): <input type="number" name="pericia"></p>
        <p>Dispositivo (id): <input type="number" name="dispositivo"></p>
        <p>Comentarios del usuario: <textarea name="comentarios_usuario"></textarea></p>
        <p>Mejoras propuestas: <textarea name="mejoras"></textarea></p>
        <p>Valoración (0-10): <input type="number" name="valoracion"></p>
        <p>Comentarios del observador: <textarea name="comentarios_facilitador"></textarea></p>

        <button type="submit" name="terminar">Terminar Prueba</button>
    </form>
</body>
</html>

