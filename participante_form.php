<?php
// Esta página serve para CADASTRAR (sem id) e para EDITAR (com ?id=...)
require "includes/iniciar.php";

$id    = $_GET["id"] ?? "";
$erro  = "";
$dados = ["nome" => "", "email" => "", "telefone" => "", "curso" => ""];

// Se veio um id na URL, estamos editando: busca os dados atuais
if ($id != "") {
    $dados = $objParticipante->buscarPorId($id);
    if (!$dados) {
        header("Location: participantes.php");
        exit;
    }
}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dados = $_POST; // mantém o que foi digitado, caso dê erro

    try {
        if ($id == "") {
            $objParticipante->cadastrar($_POST["nome"], $_POST["email"], $_POST["telefone"], $_POST["curso"]);
            $msg = "Participante cadastrado com sucesso!";
        } else {
            $objParticipante->editar($id, $_POST["nome"], $_POST["email"], $_POST["telefone"], $_POST["curso"]);
            $msg = "Participante atualizado com sucesso!";
        }
        header("Location: participantes.php?msg=" . urlencode($msg));
        exit;
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

require "includes/cabecalho.php";
?>

<h2><?= $id == "" ? "Novo participante" : "Editar participante" ?></h2>

<?php if ($erro != "") { ?>
    <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php } ?>

<form method="post">
    <label>Nome</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($dados["nome"]) ?>">

    <label>E-mail</label>
    <input type="text" name="email" value="<?= htmlspecialchars($dados["email"]) ?>">

    <label>Telefone</label>
    <input type="text" name="telefone" value="<?= htmlspecialchars($dados["telefone"]) ?>">

    <label>Curso</label>
    <input type="text" name="curso" value="<?= htmlspecialchars($dados["curso"]) ?>">

    <p>
        <button type="submit">Salvar participante</button>
        <a href="participantes.php">Cancelar</a>
    </p>
</form>

<?php require "includes/rodape.php"; ?>
