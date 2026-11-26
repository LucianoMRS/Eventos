<?php
// Este arquivo é incluído no começo de todas as páginas.
// Ele faz a conexão, carrega as classes e cria os objetos.
require_once __DIR__ . "/conexao.php";
require_once __DIR__ . "/../classes/Evento.php";
require_once __DIR__ . "/../classes/Turno.php";
require_once __DIR__ . "/../classes/Participante.php";
require_once __DIR__ . "/../classes/Inscricao.php";

$objEvento       = new Evento($pdo);
$objTurno        = new Turno($pdo);
$objParticipante = new Participante($pdo);
$objInscricao    = new Inscricao($pdo);
