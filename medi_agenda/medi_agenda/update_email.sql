USE medi_agenda_db;

ALTER TABLE patients 
ADD COLUMN email VARCHAR(100) DEFAULT NULL AFTER phone;
