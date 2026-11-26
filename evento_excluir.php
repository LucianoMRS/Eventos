<?php
require "includes/iniciar.php";

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $objEvento->excluir($_POST["id"]);
        $msg = "Evento excluído com sucesso!";
    } catch (Exception $e) {
        $msg = "Erro ao excluir: " . $e->getMessage();
    }
}

header("Location: eventos.php?msg=" . urlencode($msg));
exit;
