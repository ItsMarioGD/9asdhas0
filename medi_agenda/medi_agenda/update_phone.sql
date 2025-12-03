USE medi_agenda_db;

-- Asegurar que la columna phone exista en la tabla patients
-- Si ya existe, esto modificará su tipo para asegurar que sea suficiente
ALTER TABLE patients 
MODIFY COLUMN phone VARCHAR(20) DEFAULT NULL;

-- Opcional: Si quieres ver los cambios
DESCRIBE patients;
