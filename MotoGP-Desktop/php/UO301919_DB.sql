CREATE DATABASE UO301919_DB;
USE UO301919_DB;

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    profesion VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    pericia_informatica INT NOT NULL,
    CHECK(pericia_informatica BETWEEN 0 and 10)
);

CRE