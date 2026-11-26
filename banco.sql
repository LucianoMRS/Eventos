CREATE DATABASE IF NOT EXISTS eventos_academicos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventos_academicos;

CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    data_evento DATE NOT NULL,
    local_evento VARCHAR(100) NOT NULL
);

-- Um evento pode ter vários turnos (Manhã, Tarde, Noite).
-- Cada turno tem o seu horário e as suas vagas.
CREATE TABLE IF NOT EXISTS turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    turno VARCHAR(10) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    vagas INT NOT NULL,
    UNIQUE (evento_id, turno),
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    curso VARCHAR(100)
);

-- A inscrição é feita em um turno do evento
CREATE TABLE IF NOT EXISTS inscricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    turno_id INT NOT NULL,
    participante_id INT NOT NULL,
    data_inscricao DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (turno_id, participante_id),
    FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE CASCADE,
    FOREIGN KEY (participante_id) REFERENCES participantes(id) ON DELETE CASCADE
);

-- Dados de exemplo (opcional)
INSERT INTO eventos (titulo, descricao, data_evento, local_evento) VALUES
('Semana de Tecnologia', 'Palestras e oficinas de TI', '2026-12-01', 'Auditório Principal'),
('Workshop de PHP', 'Introdução a PHP e MySQL', '2026-12-10', 'Laboratório 3');

-- (os números 1 e 2 são os ids dos dois eventos acima)
INSERT INTO turnos (evento_id, turno, hora_inicio, hora_fim, vagas) VALUES
(1, 'Manhã', '08:00', '12:00', 30),
(1, 'Tarde', '13:00', '17:00', 20),
(2, 'Tarde', '14:00', '17:00', 10),
(2, 'Noite', '18:00', '22:00', 10);

INSERT INTO participantes (nome, email, telefone, curso) VALUES
('Ana Souza', 'ana@email.com', '(11) 90000-0001', 'Desenvolvimento de Sistemas'),
('Carlos Lima', 'carlos@email.com', '(11) 90000-0002', 'Redes de Computadores');
