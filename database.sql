DROP DATABASE IF EXISTS techstore;
CREATE DATABASE techstore;
USE techstore;

CREATE TABLE Categoria (
	id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE Usuario (
	id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, 
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Producto (
	id INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(250),
    stock INT, 
    precio FLOAT(10,2) NOT NULL,
    FOREIGN KEY (id_categoria) REFERENCES Categoria(id)
);

CREATE TABLE Cesta (
	id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    precio_total FLOAT(10,2),
    cantidad_total INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id)
);

CREATE TABLE CestaProducto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cesta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario FLOAT(10,2) NOT NULL,
	FOREIGN KEY (id_cesta) REFERENCES Cesta(id),
    FOREIGN KEY (id_producto) REFERENCES Producto(id)
);

CREATE TABLE ListaDeseo (
	id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id),
    FOREIGN KEY (id_producto) REFERENCES Producto(id)
);

CREATE TABLE Fotos (
	id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES Producto(id)
);

-- Categorías
INSERT INTO Categoria (nombre) VALUES ('Smartphones');
INSERT INTO Categoria (nombre) VALUES ('Laptops');

-- Usuarios
INSERT INTO Usuario (nombre, contrasena, rol, correo) VALUES 
('Admin', 'admin123', 'admin', 'admin@techstore.com'),
('Juan Pérez', 'juan123', 'usuario', 'juanperez@gmail.com');

-- Productos
INSERT INTO Producto (id_categoria, nombre, descripcion, stock, precio) VALUES 
(1, 'iPhone 14', 'Smartphone de última generación', 15, 999.99),
(1, 'Samsung Galaxy S22', 'Pantalla AMOLED 120Hz', 20, 849.99),
(2, 'MacBook Air M2', 'Ultraliviana con chip M2', 10, 1199.99),
(2, 'Dell XPS 13', 'Laptop compacta con pantalla InfinityEdge', 8, 1099.99);
