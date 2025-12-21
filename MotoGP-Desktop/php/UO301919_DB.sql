CREATE DATABASE IF NOT EXISTS UO301919_DB;
USE UO301919_DB;

CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    profesion VARCHAR(100) NOT NULL,
    edad TINYINT(3) NOT NULL CHECK(edad > 0),
    genero VARCHAR(20) NOT NULL CHECK (genero IN ('masculino','femenino','otro')),
    pericia_informatica INT NOT NULL CHECK(pericia_informatica BETWEEN 0 AND 10)
);

CREATE TABLE PruebaUsabilidad (
    id_prueba INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    dispositivo VARCHAR(20) NOT NULL CHECK (dispositivo IN ('ordenador','tableta','telefono')),
    tiempo_segundos INT UNSIGNED NOT NULL,
    completado BOOLEAN NOT NULL,
    comentarios_usuario TEXT,
    mejoras TEXT,
    valoracion TINYINT UNSIGNED NOT NULL CHECK(valoracion BETWEEN 0 AND 10),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

CREATE TABLE Observacion (
    id_observacion INT AUTO_INCREMENT PRIMARY KEY,
    id_prueba INT NOT NULL,
    comentarios_facilitador TEXT,
    FOREIGN KEY (id_prueba) REFERENCES PruebaUsabilidad(id_prueba)
);
