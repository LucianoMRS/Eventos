<?php
require "includes/iniciar.php";

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $objParticipante->excluir($_POST["id"]);
        $msg = "Participante excluído com sucesso!";
    } catch (Exception $e) {
        $msg = "Erro ao excluir: " . $e->getMessage();
    }
}

header("Location: participantes.php?msg=" . urlencode($msg));
exit;
