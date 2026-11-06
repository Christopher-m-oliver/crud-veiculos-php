USE sistema_veiculos;

ALTER TABLE veiculos
ADD COLUMN foto VARCHAR(255) NULL AFTER tipo;