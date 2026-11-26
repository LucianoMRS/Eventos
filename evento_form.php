<?php
// Esta página serve para CADASTRAR (sem id) e para EDITAR (com ?id=...)
require "includes/iniciar.php";

$id    = $_GET["id"] ?? "";
$erro  = "";
$dados = ["titulo" => "", "descricao" => "", "data_evento" => "", "local_evento" => ""];

// Se veio um id na URL, estamos editando: busca os dados atuais do evento
if ($id != "") {
    $dados = $objEvento->buscarPorId($id);
    if (!$dados) {
        header("Location: eventos.php");
        exit;
    }
}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dados = $_POST; // mantém o que foi digitado, caso dê erro

    try {
        if ($id == "") {
            $novo_id = $objEvento->cadastrar($_POST["titulo"], $_POST["descricao"], $_POST["data_evento"], $_POST["local_evento"]);

            // Evento novo: já vai para a tela de turnos para cadastrar os turnos dele
            $msg = "Evento cadastrado! Agora cadastre os turnos dele.";
            header("Location: turnos.php?evento_id=$novo_id&msg=" . urlencode($msg));
            exit;
        } else {
            $objEvento->editar($id, $_POST["titulo"], $_POST["descricao"], $_POST["data_evento"], $_POST["local_evento"]);
            $msg = "Evento atualizado com sucesso!";
        }
        header("Location: eventos.php?msg=" . urlencode($msg));
        exit;
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

require "includes/cabecalho.php";
?>

<h2><?= $id == "" ? "Novo evento" : "Editar evento" ?></h2>

<?php if ($erro != "") { ?>
    <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php } ?>

<form method="post">
    <label>Título</label>
    <input type="text" name="titulo" value="<?= htmlspecialchars($dados["titulo"]) ?>">

    <label>Descrição</label>
    <textarea name="descricao" rows="3"><?= htmlspecialchars($dados["descricao"]) ?></textarea>

    <label>Data</label>
    <input type="date" name="data_evento" value="<?= htmlspecialchars($dados["data_evento"]) ?>">

    <label>Local</label>
    <input type="text" name="local_evento" value="<?= htmlspecialchars($dados["local_evento"]) ?>">

    <p>
        <button type="submit">Salvar evento</button>
        <a href="eventos.php">Cancelar</a>
    </p>
</form>

<?php require "includes/rodape.php"; ?>
