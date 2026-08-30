CREATE DATABASE IF NOT EXISTS sistema_veiculos;
USE sistema_veiculos;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE marcas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL,
    marca_id INT NOT NULL,
    potencia INT NOT NULL,
    ano_fabricacao INT NOT NULL,
    tipo ENUM('Carro', 'Moto', 'Caminhao') NOT NULL,

    FOREIGN KEY (marca_id)
        REFERENCES marcas(id)
);

INSERT INTO marcas (marca) VALUES
('Toyota'),
('Honda'),
('Volkswagen'),
('Chevrolet'),
('Ford');