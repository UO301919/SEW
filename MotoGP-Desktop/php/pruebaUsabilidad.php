<?php
require_once("../cronometro.php");
session_start();

$errorFormulario = false;
$errores = [];
$formularioPOST = "";

function mostrarError($errores, $campo) {
    if (isset($errores[$campo])) {
        echo '<p class="error">Esta pregunta es obligatoria</p>';
    }
}

if (isset($_POST['iniciar'])) {
    $_SESSION['cronometro'] = new Cronometro();
    $_SESSION['cronometro']->arrancar();
}

if (isset($_POST['terminar'])) {
    $formularioPOST = $_POST;

    for ($i = 1; $i <= 10; $i++) {
        if (empty($_POST["p$i"])) {
            $errores["p$i"] = true;
            $errorFormulario = true;
        }
    }

    if (!$errorFormulario) {

        $tiempoTotal = $_SESSION['cronometro']->parar();

        $db = new mysqli("localhost", "DBUSER2025", "DBPSWD2025", "UO301919_DB");
        if ($db->connect_error) exit("Error de conexión: " . $db->connect_error);

        $stmt = $db->prepare("
            INSERT INTO Usuario (profesion, edad, genero, pericia_informatica)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sisi", $_POST['profesion'], $_POST['edad'], $_POST['genero'], $_POST['pericia']);
        $stmt->execute();
        $id_usuario = $stmt->insert_id;

        $stmt = $db->prepare("
            INSERT INTO PruebaUsabilidad 
            (id_usuario, dispositivo, tiempo_segundos, completado, comentarios_usuario, mejoras, valoracion)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $completado = 1;
        $stmt->bind_param(
            "isisssi",
            $id_usuario,
            $_POST['dispositivo'],
            $tiempoTotal,
            $completado,
            $_POST['comentarios_usuario'],
            $_POST['mejoras'],
            $_POST['valoracion']
        );
        $stmt->execute();
        $id_prueba = $stmt->insert_id;

        $stmt = $db->prepare("
            INSERT INTO Observacion (id_prueba, comentarios_facilitador)
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $id_prueba, $_POST['comentarios_facilitador']);
        $stmt->execute();

        echo "<p>Prueba completada y datos guardados en la BD.</p>";
        unset($_SESSION['cronometro']);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Usabilidad MotoGP-Desktop</title>
    <meta name="author" content="Ángela Nistal Guerrero" />
    <meta name="description" content="Prueba de usabilidad del proyecto MotoGP-Desktop" />
    <meta name="keywords" content="prueba,usabilidad,usuario" />
    <link rel="stylesheet" href="../estilo/estilo.css">
    <link rel="stylesheet" href="../estilo/layout.css">
    <link rel="icon" href="multimedia/favicon.ico" />
</head>
<body>
    <h1>Prueba de Usabilidad</h1>

    <?php if (!isset($_SESSION['cronometro'])): ?>
        <form method="post">
            <button type="submit" name="iniciar">Iniciar Prueba</button>
        </form>
    <?php endif; ?>

    <?php if (isset($_SESSION['cronometro'])): ?>
        <form method="post">
            <h2>Preguntas del proyecto</h2>
            <p>
                <label>1. ¿Cuál es el nombre del piloto destacado?
                    <input type="text" name="p1">
                </label>
                <?php mostrarError($errores, "p1"); ?>
            </p>

            <p>
                <label>2. ¿Qué día fue la carrera destacada?
                    <input type="text" name="p2">
                </label>
                <?php mostrarError($errores, "p2"); ?>
            </p>

            <p>
                <label>3. ¿Cuál es el nombre del circuito destacado?
                    <input type="text" name="p3">
                </label>
                <?php mostrarError($errores, "p3"); ?>
            </p>

            <p>
                <label>4. ¿Quién fue el ganador de la carrera?
                    <input type="text" name="p4">
                </label>
                <?php mostrarError($errores, "p4"); ?>
            </p>

            <p>
                <label>5. ¿En qué país se llevó a cabo la carrera?
                    <input type="text" name="p5">
                </label>
                <?php mostrarError($errores, "p5"); ?>
            </p>

            <p>
                <label>6. ¿Qué moto conduce el piloto destacado?
                    <input type="text" name="p6">
                </label>
                <?php mostrarError($errores, "p6"); ?>
            </p>

            <p>
                <label>7. ¿Cuál fue la temperatura el día de la carrera?
                    <input type="text" name="p7">
                </label>
                <?php mostrarError($errores, "p7"); ?>
            </p>

            <p>
                <label>8. ¿Cuál fue el tiempo total del ganador?
                    <input type="text" name="p8">
                </label>
                <?php mostrarError($errores, "p8"); ?>
            </p>

            <p>
                <label>9. ¿Cuántas cartas hay en el juego de memoria?
                    <input type="text" name="p9">
                </label>
                <?php mostrarError($errores, "p9"); ?>
            </p>

            <p>
                <label>10. ¿Cuál fue la temperatura en los entrenamientos del 21 de agosto?
                    <input type="text" name="p10">
                </label>
                <?php mostrarError($errores, "p10"); ?>
            </p>

            <h2>Datos del usuario</h2>
            <p>
                <label>Edad:
                    <input type="number" name="edad" min="1" max="130">
                </label>
            </p>

            <p>
                <label>Profesión:
                    <input type="text" name="profesion">
                </label>
            </p>

            <p>
                <label>Género:
                    <select name="genero">
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="otro">Otro</option>
                    </select>
                </label>
            </p>

            <p>
                <label>Pericia informática (0-10):
                    <input type="number" name="pericia">
                </label>
            </p>

            <p>
                <label>Dispositivo:
                    <select name="dispositivo">
                        <option value="ordenador">Ordenador</option>
                        <option value="tableta">Tableta</option>
                        <option value="telefono">Teléfono</option>
                    </select>
                </label>
            </p>

            <p>
                <label>Comentarios del usuario:
                    <textarea name="comentarios_usuario"></textarea>
                </label>
            </p>

            <p>
                <label>Mejoras propuestas:
                    <textarea name="mejoras"></textarea>
                </label>
            </p>

            <p>
                <label>Valoración (0-10):
                    <input type="number" name="valoracion" min="0" max="10">
                </label>
            </p>

            <h2>Datos del observador</h2>
            <p>
                <label>Comentarios del observador:
                    <textarea name="comentarios_facilitador" rows="5"></textarea>
                </label>
            </p>

            <button type="submit" name="terminar">Terminar Prueba</button>
        </form>
    <?php endif; ?>
</body>
</html>


