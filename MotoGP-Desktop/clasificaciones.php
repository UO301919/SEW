<!DOCTYPE HTML>

<html lang="es">
<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <meta name="author" content="Ángela Nistal Guerrero"/>
    <meta name="description" content="clasificaciones de los pilotos en el campeonato de moto gp"/>
    <meta name="primero,clasificaciones" content="palabras clave del documento separadas por comas"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>MotoGP-Clasificaciones</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css">
    <link rel="icon" href="multimedia/favicon.ico" />
</head>

<body>
    <!-- Datos con el contenidos que aparece en el navegador -->
    <header>
        <h1><a href="index.html">MotoGP Desktop</a></h1>
        <nav>
            <a href="index.html" title="Inicio">Inicio</a>
            <a href="piloto.html" title="Información del piloto">Piloto</a>
            <a href="circuito.html" title="Información del circuito">Circuito</a>
            <a href="meteorologia.html" title="Información de la meteorologia">Meteorologia</a>
            <a href="clasificaciones.php" title="Información de las clasificaciones">Clasificaciones</a>
            <a href="juegos.html" title="Información de los juegos">Juegos</a>
            <a href="ayuda.html" title="Información de ayuda">Ayuda</a>
        </nav>
     </header>

    <nav>
        <p>Estás en: <a href="index.html">Inicio</a> >> <strong>Clasificaciones</strong></p>
    </nav>
    
    <main>
        <h2>Clasificaciones de MotoGP-Desktop</h2>

        <?php
            class Clasificacion {
                private $documento;
                private $xml;

                public function __construct() {
                    $this->documento = "xml/circuitoEsquema.xml";
                }

                public function consultar() {
                    $datos = file_get_contents($this->documento);
                    if($datos == null) {
                        echo "<h3>Error en el archivo XML recibido</h3>";
                    } else {
                        $this->xml = new SimpleXMLElement($datos);
                    }
                }

                public function mostrarGanador() {
                    if ($this->xml) {
                        $ganador = (string)$this->xml->vencedor->nombre;
                        $tiempoISO = (string)$this->xml->vencedor->tiempo;
                
                        if (preg_match('/PT(\d+)M([\d\.]+)S/', $tiempoISO, $matches)) {
                            $minutos = $matches[1];
                            $segundos = $matches[2];
                            $tiempoFormateado = sprintf("%02d:%06.3f", $minutos, $segundos);
                        } else {
                            $tiempoFormateado = $tiempoISO;
                        }
                
                        echo "<h3>Ganador de la carrera</h3>";
                        echo "<p>Nombre: $ganador</p>";
                        echo "<p>Tiempo: $tiempoFormateado</p>";
                    }
                }
                
                public function mostrarClasificacionMundial() {
                    if ($this->xml) {
                        echo "<h3>Clasificación del mundial</h3>";
                        echo "<ul>";
                        foreach ($this->xml->clasificacionMundial->piloto as $piloto) {
                            echo "<li>$piloto</li>";
                        }
                        echo "</ul>";
                    }
                }
            }
        ?>

        <?php
        $clasificacion = new Clasificacion();
        $clasificacion->consultar();
        $clasificacion->mostrarGanador();
        $clasificacion->mostrarClasificacionMundial();
        ?>
    </main>
    
</body>
</html>