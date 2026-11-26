<?php
require "includes/iniciar.php";

$totais    = $objInscricao->totais();
$porEvento = $objInscricao->relatorioPorEvento();

require "includes/cabecalho.php";
?>

<h2>Relatórios</h2>

<div class="cards">
    <div class="card"><strong><?= $totais["eventos"] ?></strong> eventos</div>
    <div class="card"><strong><?= $totais["participantes"] ?></strong> participantes</div>
    <div class="card"><strong><?= $totais["inscricoes"] ?></strong> inscrições</div>
</div>

<h3>Inscrições por evento e turno</h3>
<table>
    <tr>
        <th>Evento</th>
        <th>Data</th>
        <th>Turno</th>
        <th>Vagas</th>
        <th>Inscritos</th>
        <th>Vagas livres</th>
        <th>Ocupação</th>
    </tr>
    <?php foreach ($porEvento as $r) { ?>
    <tr>
        <td><?= htmlspecialchars($r["titulo"]) ?></td>
        <td><?= date("d/m/Y", strtotime($r["data_evento"])) ?></td>
        <td><?= $r["turno"] ?> (<?= date("H:i", strtotime($r["hora_inicio"])) ?> às <?= date("H:i", strtotime($r["hora_fim"])) ?>)</td>
        <td><?= $r["vagas"] ?></td>
        <td><?= $r["inscritos"] ?></td>
        <td><?= $r["vagas"] - $r["inscritos"] ?></td>
        <td><?= round($r["inscritos"] / $r["vagas"] * 100) ?>%</td>
    </tr>
    <?php } ?>
</table>

<?php require "includes/rodape.php"; ?>
