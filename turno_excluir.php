<?php
require "includes/iniciar.php";

$msg       = "";
$evento_id = $_POST["evento_id"] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $objTurno->excluir($_POST["id"]);
        $msg = "Turno excluído com sucesso!";
    } catch (Exception $e) {
        $msg = "Erro ao excluir: " . $e->getMessage();
    }
}

header("Location: turnos.php?evento_id=" . urlencode($evento_id) . "&msg=" . urlencode($msg));
exit;
