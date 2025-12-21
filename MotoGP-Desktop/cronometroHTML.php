<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Ángela Nistal Guerrero"/>
    <meta name="description" content="Cronómetro para MotoGP-Desktop"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>MotoGP-Cronómetro</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
    <link rel="icon" href="multimedia/favicon.ico" />
</head>

<body>
    <header>
        <h1><a href="index.html">MotoGP Desktop</a></h1>
        <nav>
            <a href="index.html" title="Inicio">Inicio</a>
            <a href="piloto.html" title="Información del piloto">Piloto</a>
            <a href="circuito.html" title="Información del circuito">Circuito</a>
            <a href="meteorologia.html" title="Información de la meteorologia">Meteorologia</a>
            <a href="clasificaciones.html" title="Información de las clasificaciones">Clasificaciones</a>
            <a href="juegos.html" title="Información de los juegos">Juegos</a>
            <a href="ayuda.html" title="Información de ayuda">Ayuda</a>
        </nav>
     </header>

    <nav>
        <p>Estás en: <a href="index.html">Inicio</a> >> <a href="juegos.html">Juegos</a> >> <strong>Cronómetro</strong></p>
    </nav>

    <main>
        <h2>Cronómetro de MotoGP-Desktop</h2>
        <?php
            require_once("cronometro.php");
            session_start();
    
            if (!isset($_SESSION['cronometro'])) {
                $_SESSION['cronometro'] = new Cronometro();
            }
            $cronometro = $_SESSION['cronometro'];
    
            $mensaje = "";
            if (isset($_POST['arrancar'])) {
                $cronometro->arrancar();
                $mensaje = "Cronómetro arrancado.";
            }
            if (isset($_POST['parar'])) {
                $cronometro->parar();
                $mensaje = "Cronómetro parado.";
            }
            if (isset($_POST['mostrar'])) {
                $mensaje = "Tiempo transcurrido: " . $cronometro->mostrar();
            }
        ?>
        <form action="#" method="post">
            <input type="submit" name="arrancar" value="Arrancar"/>
            <input type="submit" name="parar" value="Parar"/>
            <input type="submit" name="mostrar" value="Mostrar"/>
        </form>
        <p><?php echo $mensaje; ?></p>
    </main>
</body>
</html>