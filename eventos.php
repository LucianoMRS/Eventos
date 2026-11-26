<?php
require "includes/iniciar.php";

$msg   = $_GET["msg"] ?? "";
$busca = $_GET["busca"] ?? "";
$lista = $objEvento->listar($busca);

require "includes/cabecalho.php";
?>

<h2>Eventos</h2>

<?php if ($msg != "") { ?>
    <p class="msg"><?= htmlspecialchars($msg) ?></p>
<?php } ?>

<form method="get" class="busca">
    <input type="text" name="busca" placeholder="Buscar por título ou local" value="<?= htmlspecialchars($busca) ?>">
    <button type="submit">Buscar</button>
    <a class="botao" href="evento_form.php">Novo evento</a>
</form>

<table>
    <tr>
        <th>Título</th>
        <th>Data</th>
        <th>Local</th>
        <th>Turnos</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($lista as $e) { ?>
    <tr>
        <td><?= htmlspecialchars($e["titulo"]) ?></td>
        <td><?= date("d/m/Y", strtotime($e["data_evento"])) ?></td>
        <td><?= htmlspecialchars($e["local_evento"]) ?></td>
        <td><?= $e["qtd_turnos"] ?></td>
        <td>
            <a href="turnos.php?evento_id=<?= $e["id"] ?>">Turnos</a>
            <a href="evento_form.php?id=<?= $e["id"] ?>">Editar</a>
            <form method="post" action="evento_excluir.php" class="inline"
                  onsubmit="return confirm('Excluir este evento? As inscrições dele também serão apagadas.')">
                <input type="hidden" name="id" value="<?= $e["id"] ?>">
                <button type="submit" class="perigo">Excluir</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

<?php if (count($lista) == 0) { ?>
    <p>Nenhum evento encontrado.</p>
<?php } ?>

<?php require "includes/rodape.php"; ?>
