CREATE DATABASE UO301919_DB;
USE UO301919_DB;

CREATE TABLE Profesion (
    id_profesion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE Genero (
    id_genero INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE Dispositivo (
    id_dispositivo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_profesion INT NOT NULL,
    edad INT NOT NULL CHECK(edad > 0),
    id_genero INT NOT NULL,
    pericia_informatica INT NOT NULL CHECK(pericia_informatica BETWEEN 0 AND 10),
    FOREIGN KEY (id_profesion) REFERENCES Profesion(id_profesion),
    FOREIGN KEY (id_genero) REFERENCES Genero(id_genero)
);

CREATE TABLE ResultadoUsabilidad (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_dispositivo INT NOT NULL,
    tiempo INT NOT NULL CHECK(tiempo > 0),
    completado BOOLEAN NOT NULL,
    comentarios_usuario TEXT,
    mejoras TEXT,
    valoracion INT NOT NULL CHECK(valoracion BETWEEN 0 AND 10),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_dispositivo) REFERENCES Dispositivo(id_dispositivo)
);

CREATE TABLE Observacion (
    id_observacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    comentarios_facilitador TEXT,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);
