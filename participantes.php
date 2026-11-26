<?php
require "includes/iniciar.php";

$msg   = $_GET["msg"] ?? "";
$busca = $_GET["busca"] ?? "";
$lista = $objParticipante->listar($busca);

require "includes/cabecalho.php";
?>

<h2>Participantes</h2>

<?php if ($msg != "") { ?>
    <p class="msg"><?= htmlspecialchars($msg) ?></p>
<?php } ?>

<form method="get" class="busca">
    <input type="text" name="busca" placeholder="Buscar por nome, e-mail ou curso" value="<?= htmlspecialchars($busca) ?>">
    <button type="submit">Buscar</button>
    <a class="botao" href="participante_form.php">Novo participante</a>
</form>

<table>
    <tr>
        <th>Nome</th>
        <th>E-mail</th>
        <th>Telefone</th>
        <th>Curso</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($lista as $p) { ?>
    <tr>
        <td><?= htmlspecialchars($p["nome"]) ?></td>
        <td><?= htmlspecialchars($p["email"]) ?></td>
        <td><?= htmlspecialchars($p["telefone"]) ?></td>
        <td><?= htmlspecialchars($p["curso"]) ?></td>
        <td>
            <a href="participante_form.php?id=<?= $p["id"] ?>">Editar</a>
            <form method="post" action="participante_excluir.php" class="inline"
                  onsubmit="return confirm('Excluir este participante? As inscrições dele também serão apagadas.')">
                <input type="hidden" name="id" value="<?= $p["id"] ?>">
                <button type="submit" class="perigo">Excluir</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

<?php if (count($lista) == 0) { ?>
    <p>Nenhum participante encontrado.</p>
<?php } ?>

<?php require "includes/rodape.php"; ?>
