<?php
require_once("../cronometro.php");
session_start();

if (isset($_POST['iniciar'])) {
    $_SESSION['cronometro'] = new Cronometro();
    $_SESSION['cronometro']->arrancar();
}

if (isset($_POST['terminar'])) {

    $p1 = trim($_POST['p1'] ?? '');
    $p2 = trim($_POST['p2'] ?? '');
    $p3 = trim($_POST['p3'] ?? '');
    $p4 = trim($_POST['p4'] ?? '');
    $p5 = trim($_POST['p5'] ?? '');
    $p6 = trim($_POST['p6'] ?? '');
    $p7 = trim($_POST['p7'] ?? '');
    $p8 = trim($_POST['p8'] ?? '');
    $p9 = trim($_POST['p9'] ?? '');
    $p10 = trim($_POST['p10'] ?? '');

    $edad = (int) ($_POST['edad'] ?? 0);
    $profesion = trim($_POST['profesion'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $pericia = trim($_POST['pericia'] ?? '');
    $dispositivo = trim($_POST['dispositivo'] ?? '');

    $comentarios_usuario = trim($_POST['comentarios_usuario'] ?? '');
    $mejoras = trim($_POST['mejoras'] ?? '');
    $valoracion = trim($_POST['valoracion'] ?? '');
    $comentarios_facilitador = trim($_POST['comentarios_facilitador'] ?? '');

    if (
        $p1 === '' || $p2 === '' || $p3 === '' || $p4 === '' || $p5 === '' ||
        $p6 === '' || $p7 === '' || $p8 === '' || $p9 === '' || $p10 === '' ||
        $edad <= 0 || $profesion === '' || $genero === '' || $pericia === '' ||
        $dispositivo === '' || $valoracion === '' ||
        $comentarios_usuario === '' || $mejoras === '' || $comentarios_facilitador === ''
    ) {
        echo "<p>Debes rellenar todos los campos antes de enviar la prueba.</p>";
        return;
    }
    
    
    $tiempoTotal = $_SESSION['cronometro']->parar();

    $db = new mysqli("localhost", "DBUSER2025", "DBPSWD2025", "UO301919_DB");
    if ($db->connect_error) exit("Error de conexión: " . $db->connect_error);

    $stmt = $db->prepare("
        INSERT INTO Usuario (profesion, edad, genero, pericia_informatica)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("sisi", $profesion, $edad, $genero, $pericia);
    $stmt->execute();
    $id_usuario = $stmt->insert_id;

    $stmt = $db->prepare("
        INSERT INTO PruebaUsabilidad 
        (id_usuario, dispositivo, tiempo_segundos, completado, comentarios_usuario, mejoras, valoracion)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $completado = 1;
    $stmt->bind_param( "isisssi", 
    $id_usuario, 
    $dispositivo, 
    $tiempoTotal, 
    $completado, 
    $comentarios_usuario, 
    $mejoras, 
    $valoracion );
    $stmt->execute();
    $id_prueba = $stmt->insert_id;

    $stmt = $db->prepare("
        INSERT INTO Observacion (id_prueba, comentarios_facilitador)
        VALUES (?, ?)
    ");
    $stmt->bind_param("is", $id_prueba, $comentarios_facilitador);
    $stmt->execute();

    echo "<p>Prueba completada y datos guardados en la BD.</p>";
    unset($_SESSION['cronometro']);
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
                <label for="p1">1. ¿Cuál es el nombre del piloto?</label>
                <input type="text" id="p1" name="p1" required>
            </p>

            <p>
                <label for="p2">2. ¿De qué son las imágenes de la página de inicio?</label>
                <input type="text" id="p2" name="p2" required>
            </p>

            <p>
                <label for="p3">3. ¿Cuál es el nombre del circuito?</label>
                <input type="text" id="p3" name="p3" required>
            </p>

            <p>
                <label for="p4">4. ¿Quién fue el ganador de la carrera?</label>
                <input type="text" id="p4" name="p4" required>
            </p>

            <p>
                <label for="p5">5. ¿En qué país se llevó a cabo la carrera?</label>
                <input type="text" id="p5" name="p5" required>
            </p>

            <p>
                <label for="p6">6. ¿Cuál es el dorsal del piloto?</label>
                <input type="text" id="p6" name="p6" required>
            </p>

            <p>
                <label for="p7">7. ¿Cuál fue la temperatura el día de la carrera?</label>
                <input type="text" id="p7" name="p7" required>
            </p>

            <p>
                <label for="p8">8. ¿Cuál fue el tiempo total del ganador?</label>
                <input type="text" id="p8" name="p8" required>
            </p>

            <p>
                <label for="p9">9. ¿Cuántas cartas hay en el juego de memoria?</label>
                <input type="text" id="p9" name="p9" required>
            </p>

            <p>
                <label for="p10">10. ¿Cuál fue la temperatura en los entrenamientos del 21 de agosto?</label>
                <input type="text" id="p10" name="p10" required>
            </p>

            <h2>Datos del usuario</h2>

            <p>
                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" min="1" max="130" required>
            </p>

            <p>
                <label for="profesion">Profesión:</label>
                <input type="text" id="profesion" name="profesion" required>
            </p>

            <p>
                <label for="genero">Género:</label>
                <select id="genero" name="genero" required>
                    <option value="">Selecciona una opción</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">Otro</option>
                </select>
            </p>

            <p>
                <label for="pericia">Pericia informática (0-10):</label>
                <input type="number" id="pericia" name="pericia" min="0" max="10" required>
            </p>

            <p>
                <label for="dispositivo">Dispositivo:</label>
                <select id="dispositivo" name="dispositivo" required>
                    <option value="">Selecciona una opción</option>
                    <option value="ordenador">Ordenador</option>
                    <option value="tableta">Tableta</option>
                    <option value="telefono">Teléfono</option>
                </select>
            </p>

            <p>
                <label for="comentarios_usuario">Comentarios del usuario:</label>
                <textarea id="comentarios_usuario" name="comentarios_usuario" required></textarea>
            </p>

            <p>
                <label for="mejoras">Mejoras propuestas:</label>
                <textarea id="mejoras" name="mejoras" required></textarea>
            </p>

            <p>
                <label for="valoracion">Valoración (0-10):</label>
                <input type="number" id="valoracion" name="valoracion" min="0" max="10" required>
            </p>

            <h2>Datos del observador</h2>

            <p>
                <label for="comentarios_facilitador">Comentarios del observador:</label>
                <textarea id="comentarios_facilitador" name="comentarios_facilitador" rows="5" required></textarea>
            </p>

            <button type="submit" name="terminar">Terminar Prueba</button>
        </form>

    <?php endif; ?>
</body>
</html>


