<?php
    class Configuracion {
        private $db;
        private $dbname = "UO301919_DB";
        private $servername = "localhost";
        private $username = "DBUSER2025";
        private $password = "DBPSWD2025";

        public function __construct() {
            $this->db = new mysqli($this->servername, $this->username, $this->password);
            if($this->db->connect_error) {
                exit("Error de conexión: " . $this->db->connect_error);
            }
        }

        public function reiniciarBD() {
            $this->db->select_db($this->dbname);
            $tablas = ["Profesion", "Genero", "Dispositivo", "Usuario", "ResultadoUsabilidad", "Observacion"];
            foreach($tablas as $tabla) {
                $this->db->query("DELETE FROM $tabla");
            }
        }

        public function eliminarDB() {
            $this->db->query("DROP DATABASE IF EXISTS $this->dbname");
        }

        public function exportarCSV() {
            $this->db->select_db($this->dbname);

            $sql = "SELECT 
                        u.id_usuario,
                        u.profesion,
                        u.edad,
                        u.genero,
                        u.pericia_informatica,
                        r.dispositivo,
                        r.tiempo,
                        r.completado,
                        r.comentarios_usuario,
                        r.mejoras,
                        r.valoracion,
                        o.comentarios_facilitador
                    FROM Usuario u
                    LEFT JOIN ResultadoUsabilidad r ON u.id_usuario = r.id_usuario
                    LEFT JOIN Observacion o ON u.id_usuario = o.id_usuario";
            $resultado = $this->db->query($sql);

            if($resultado && $resultado->num_rows > 0) {
                $archivo = fopen("pruebasUsabilidad.csv", 'w');

                $columnas = array_keys($resultado->fetch_assoc());
                fputcsv($archivo, $columnas);
                $resultado->data_seek(0);

                while($fila = $resultado->fetch_assoc()) {
                    fputcsv($archivo, $fila);
                }

                fclose($archivo);
            }
        }
    }

?>