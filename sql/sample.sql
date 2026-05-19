-- PostgreSQL initialization script for Product Registration System

\c product_db;

CREATE TABLE IF NOT EXISTS monedas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    codigo VARCHAR(10) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS bodegas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS sucursales (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    bodega_id INTEGER NOT NULL REFERENCES bodegas(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS materiales (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(15) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(1000) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL CHECK (precio > 0),
    moneda_id INTEGER NOT NULL REFERENCES monedas(id) ON DELETE CASCADE,
    bodega_id INTEGER NOT NULL REFERENCES bodegas(id) ON DELETE CASCADE,
    sucursal_id INTEGER NOT NULL REFERENCES sucursales(id) ON DELETE CASCADE,
    fecha_creacion TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS productos_materiales (
    producto_id INTEGER NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    material_id INTEGER NOT NULL REFERENCES materiales(id) ON DELETE CASCADE,
    PRIMARY KEY (producto_id, material_id)
);


-- Functions --

CREATE OR REPLACE FUNCTION update_fecha_actualizacion()
RETURNS TRIGGER AS $$
BEGIN
    NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Triggers --

-- update fecha_actualizacion 
CREATE TRIGGER trigger_update_fecha_actualizacion
BEFORE UPDATE ON productos
FOR EACH ROW
EXECUTE FUNCTION update_fecha_actualizacion();

-- Populate DB --

INSERT INTO monedas (nombre, codigo) VALUES
('Dólar Estadounidense', 'USD'),
('Euro', 'EUR'),
('Peso Chileno', 'CLP');

INSERT INTO bodegas (nombre) VALUES
('Bodega Central'),
('Bodega Norte'),
('Bodega Sur');

INSERT INTO sucursales (nombre, bodega_id) VALUES
('Sucursal C1', 1),
('Sucursal C2', 1),
('Sucursal C3', 1),
('Sucursal N1', 2),
('Sucursal N2', 2),
('Sucursal N3', 2),
('Sucursal S1', 3),
('Sucursal S2', 3),
('Sucursal S3', 3);

INSERT INTO materiales (nombre) VALUES
('Plástico'),
('Metal'),
('Madera'),
('Vidrio'),
('Textil');