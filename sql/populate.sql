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