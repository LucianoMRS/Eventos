<?php
require "includes/iniciar.php";

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $objInscricao->cancelar($_POST["id"]);
        $msg = "Inscrição cancelada com sucesso!";
    } catch (Exception $e) {
        $msg = "Erro ao cancelar: " . $e->getMessage();
    }
}

header("Location: inscricoes.php?msg=" . urlencode($msg));
exit;
