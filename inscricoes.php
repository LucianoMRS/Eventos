<?php
require "includes/iniciar.php";

$erro  = "";
$msg   = $_GET["msg"] ?? "";
$busca = $_GET["busca"] ?? "";

// Se o formulário de nova inscrição foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $objInscricao->registrar($_POST["turno_id"], $_POST["participante_id"]);
        header("Location: inscricoes.php?msg=" . urlencode("Inscrição registrada com sucesso!"));
        exit;
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

$turnos        = $objTurno->listarTodos();
$participantes = $objParticipante->listar();
$lista         = $objInscricao->listar($busca);

require "includes/cabecalho.php";
?>

<h2>Inscrições</h2>

<?php if ($msg != "") { ?>
    <p class="msg"><?= htmlspecialchars($msg) ?></p>
<?php } ?>
<?php if ($erro != "") { ?>
    <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php } ?>

<h3>Nova inscrição</h3>
<form method="post">
    <label>Evento e turno</label>
    <select name="turno_id">
        <option value="">Selecione...</option>
        <?php foreach ($turnos as $t) { ?>
            <option value="<?= $t["id"] ?>">
                <?= htmlspecialchars($t["titulo"]) ?> - <?= date("d/m/Y", strtotime($t["data_evento"])) ?> -
                <?= $t["turno"] ?> (<?= date("H:i", strtotime($t["hora_inicio"])) ?> às <?= date("H:i", strtotime($t["hora_fim"])) ?>) -
                <?= $t["vagas"] - $t["inscritos"] ?> vagas livres
            </option>
        <?php } ?>
    </select>

    <label>Participante</label>
    <select name="participante_id">
        <option value="">Selecione...</option>
        <?php foreach ($participantes as $p) { ?>
            <option value="<?= $p["id"] ?>"><?= htmlspecialchars($p["nome"]) ?></option>
        <?php } ?>
    </select>

    <p><button type="submit">Registrar inscrição</button></p>
</form>

<h3>Inscrições registradas</h3>
<form method="get" class="busca">
    <input type="text" name="busca" placeholder="Buscar por evento ou participante" value="<?= htmlspecialchars($busca) ?>">
    <button type="submit">Buscar</button>
</form>

<table>
    <tr>
        <th>Evento</th>
        <th>Data do evento</th>
        <th>Turno</th>
        <th>Participante</th>
        <th>Inscrito em</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($lista as $i) { ?>
    <tr>
        <td><?= htmlspecialchars($i["titulo"]) ?></td>
        <td><?= date("d/m/Y", strtotime($i["data_evento"])) ?></td>
        <td><?= $i["turno"] ?> (<?= date("H:i", strtotime($i["hora_inicio"])) ?> às <?= date("H:i", strtotime($i["hora_fim"])) ?>)</td>
        <td><?= htmlspecialchars($i["nome"]) ?></td>
        <td><?= date("d/m/Y H:i", strtotime($i["data_inscricao"])) ?></td>
        <td>
            <form method="post" action="inscricao_cancelar.php" class="inline"
                  onsubmit="return confirm('Cancelar esta inscrição?')">
                <input type="hidden" name="id" value="<?= $i["id"] ?>">
                <button type="submit" class="perigo">Cancelar</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

<?php if (count($lista) == 0) { ?>
    <p>Nenhuma inscrição encontrada.</p>
<?php } ?>

<?php require "includes/rodape.php"; ?>
