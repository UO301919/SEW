<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración Test</title>
    <link rel="stylesheet" href="../estilo/estilo.css">
    <link rel="stylesheet" href="../estilo/layout.css">
</head>
<body>
    <h2>Configuración de la Prueba de Usabilidad</h2>
    <?php
        require_once("configuracion.php");
        $config = new Configuracion();
        $mensaje = "";

        if (isset($_POST['reiniciar'])) {
            $mensaje = $config->reiniciarDB();
        }
        if (isset($_POST['eliminar'])) {
            $mensaje = $config->eliminarDB();
        }
        if (isset($_POST['exportar'])) {
            $mensaje = $config->exportarCSV();
        }
    ?>
    <form method="post">
        <button type="submit" name="reiniciar">Reiniciar BD</button>
        <button type="submit" name="eliminar">Eliminar BD</button>
        <button type="submit" name="exportar">Exportar datos</button>
    </form>
    <p><?= $mensaje ?></p>
</body>
</html>
