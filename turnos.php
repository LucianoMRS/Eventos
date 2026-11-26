<?php
// Cadastra e lista os turnos de UM evento (turnos.php?evento_id=...)
require "includes/iniciar.php";

$evento_id = $_GET["evento_id"] ?? "";
$evento    = $objEvento->buscarPorId($evento_id);

// Se o evento não existe, volta para a lista
if (!$evento) {
    header("Location: eventos.php");
    exit;
}

$erro  = "";
$msg   = $_GET["msg"] ?? "";
$dados = ["turno" => "", "hora_inicio" => "", "hora_fim" => "", "vagas" => ""];

// Se o formulário de novo turno foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dados = $_POST; // mantém o que foi digitado, caso dê erro

    try {
        $objTurno->cadastrar($evento_id, $_POST["turno"], $_POST["hora_inicio"], $_POST["hora_fim"], $_POST["vagas"]);
        header("Location: turnos.php?evento_id=$evento_id&msg=" . urlencode("Turno cadastrado com sucesso!"));
        exit;
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

$lista = $objTurno->listarPorEvento($evento_id);

require "includes/cabecalho.php";
?>

<h2>Turnos do evento</h2>
<p>
    <strong><?= htmlspecialchars($evento["titulo"]) ?></strong>
    - <?= date("d/m/Y", strtotime($evento["data_evento"])) ?>
</p>

<?php if ($msg != "") { ?>
    <p class="msg"><?= htmlspecialchars($msg) ?></p>
<?php } ?>
<?php if ($erro != "") { ?>
    <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php } ?>

<h3>Novo turno</h3>
<form method="post">
    <label>Turno</label>
    <select name="turno">
        <option value="">Selecione...</option>
        <?php foreach ($objTurno->turnos_validos as $nome) { ?>
            <option value="<?= $nome ?>" <?= $dados["turno"] == $nome ? "selected" : "" ?>><?= $nome ?></option>
        <?php } ?>
    </select>

    <label>Hora de início</label>
    <input type="time" name="hora_inicio" value="<?= htmlspecialchars($dados["hora_inicio"]) ?>">

    <label>Hora de término</label>
    <input type="time" name="hora_fim" value="<?= htmlspecialchars($dados["hora_fim"]) ?>">

    <label>Vagas</label>
    <input type="number" name="vagas" min="1" value="<?= htmlspecialchars($dados["vagas"]) ?>">

    <p><button type="submit">Cadastrar turno</button></p>
</form>

<h3>Turnos cadastrados</h3>
<table>
    <tr>
        <th>Turno</th>
        <th>Horário</th>
        <th>Vagas</th>
        <th>Inscritos</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($lista as $t) { ?>
    <tr>
        <td><?= $t["turno"] ?></td>
        <td><?= date("H:i", strtotime($t["hora_inicio"])) ?> às <?= date("H:i", strtotime($t["hora_fim"])) ?></td>
        <td><?= $t["vagas"] ?></td>
        <td><?= $t["inscritos"] ?></td>
        <td>
            <form method="post" action="turno_excluir.php" class="inline"
                  onsubmit="return confirm('Excluir este turno?')">
                <input type="hidden" name="id" value="<?= $t["id"] ?>">
                <input type="hidden" name="evento_id" value="<?= $evento_id ?>">
                <button type="submit" class="perigo">Excluir</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

<?php if (count($lista) == 0) { ?>
    <p>Este evento ainda não tem turnos.</p>
<?php } ?>

<p><a href="eventos.php">Voltar para os eventos</a></p>

<?php require "includes/rodape.php"; ?>
