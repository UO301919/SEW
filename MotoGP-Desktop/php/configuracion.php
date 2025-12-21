<?php
    class Configuracion {
        private $db;
        private $dbname = "UO301919_DB";
        private $servername = "localhost";
        private $username = "DBUSER2025";
        private $password = "DBPSWD2025";

        public function __construct() {
            $this->db = new mysqli(
                $this->servername, 
                $this->username, 
                $this->password);
            if($this->db->connect_error) {
                exit("Error de conexión: " . $this->db->connect_error);
            }
        }

        public function reiniciarDB() {
            $this->db->select_db($this->dbname);
            $tablas = ["Observacion", "PruebaUsabilidad", "Usuario"];
            foreach($tablas as $tabla) {
                $this->db->query("DELETE FROM $tabla");
            }
            return "Base de datos reiniciada correctamente.";
        }

        public function eliminarDB() {
            $this->db->query("DROP DATABASE IF EXISTS $this->dbname");
            return "Base de datos eliminada correctamente.";
        }

        public function exportarCSV() {
            $this->db->select_db($this->dbname);

            $sql = "
            SELECT 
                u.id_usuario,
                u.profesion,
                u.edad,
                u.genero,
                u.pericia_informatica,
                p.id_prueba,
                p.dispositivo,
                p.tiempo_segundos,
                p.completado,
                p.comentarios_usuario,
                p.mejoras,
                p.valoracion,
                o.id_observacion,
                o.comentarios_facilitador
            FROM Usuario u
            INNER JOIN PruebaUsabilidad p ON u.id_usuario = p.id_usuario
            LEFT JOIN Observacion o ON p.id_prueba = o.id_prueba
            ";
            $resultado = $this->db->query($sql);

            if(!$resultado) return;

            if($resultado && $resultado->num_rows > 0) {
                $archivo = fopen("pruebasUsabilidad.csv", 'w');

                $columnas = array_keys($resultado->fetch_assoc());
                fputcsv($archivo, $columnas, ';');
                $resultado->data_seek(0);

                while($fila = $resultado->fetch_assoc()) {
                    fputcsv($archivo, $fila, ';');
                }

                fclose($archivo);
            }
            return "Datos exportados correctamente a pruebasUsabilidad.csv.";
        }
    }

?>