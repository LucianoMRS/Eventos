<?php
class Inscricao {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Registra a inscrição em um turno, depois de conferir todas as regras
    public function registrar($turno_id, $participante_id) {
        if ($turno_id == "" || $participante_id == "") {
            throw new Exception("Selecione o turno do evento e o participante.");
        }

        // 1) O turno existe? (já busca junto o título e a data do evento)
        $sql = "SELECT t.*, e.titulo, e.data_evento
                FROM turnos t
                JOIN eventos e ON e.id = t.evento_id
                WHERE t.id = ?";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$turno_id]);
        $turno = $cmd->fetch();
        if (!$turno) {
            throw new Exception("Turno não encontrado.");
        }

        // 2) O participante existe?
        $cmd = $this->pdo->prepare("SELECT id FROM participantes WHERE id = ?");
        $cmd->execute([$participante_id]);
        if (!$cmd->fetch()) {
            throw new Exception("Participante não encontrado.");
        }

        // 3) Ele já está inscrito neste turno?
        $cmd = $this->pdo->prepare("SELECT id FROM inscricoes WHERE turno_id = ? AND participante_id = ?");
        $cmd->execute([$turno_id, $participante_id]);
        if ($cmd->fetch()) {
            throw new Exception("Este participante já está inscrito neste turno do evento.");
        }

        // 4) Ele já tem outro turno no mesmo dia e no mesmo horário?
        //    Dois horários batem quando um começa antes de o outro terminar.
        $sql = "SELECT e.titulo, t.turno, t.hora_inicio, t.hora_fim
                FROM inscricoes i
                JOIN turnos t ON t.id = i.turno_id
                JOIN eventos e ON e.id = t.evento_id
                WHERE i.participante_id = ?
                  AND e.data_evento = ?
                  AND t.hora_inicio < ?
                  AND t.hora_fim > ?";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$participante_id, $turno["data_evento"], $turno["hora_fim"], $turno["hora_inicio"]]);
        $outro = $cmd->fetch();
        if ($outro) {
            $inicio = date("H:i", strtotime($outro["hora_inicio"]));
            $fim    = date("H:i", strtotime($outro["hora_fim"]));
            throw new Exception("Este participante já está inscrito no turno da " . $outro["turno"]
                . " (" . $inicio . " às " . $fim . ") do evento \"" . $outro["titulo"] . "\", no mesmo horário.");
        }

        // 5) Ainda há vagas neste turno?
        $cmd = $this->pdo->prepare("SELECT COUNT(*) FROM inscricoes WHERE turno_id = ?");
        $cmd->execute([$turno_id]);
        if ($cmd->fetchColumn() >= $turno["vagas"]) {
            throw new Exception("Não há vagas disponíveis neste turno.");
        }

        // Tudo certo: salva
        $cmd = $this->pdo->prepare("INSERT INTO inscricoes (turno_id, participante_id) VALUES (?, ?)");
        $cmd->execute([$turno_id, $participante_id]);
    }

    // Cancela (apaga) a inscrição
    public function cancelar($id) {
        $cmd = $this->pdo->prepare("DELETE FROM inscricoes WHERE id = ?");
        $cmd->execute([$id]);
    }

    // Lista as inscrições (pesquisa por nome do evento ou do participante)
    public function listar($busca = "") {
        $sql = "SELECT i.id, i.data_inscricao, e.titulo, e.data_evento,
                       t.turno, t.hora_inicio, t.hora_fim, p.nome, p.email
                FROM inscricoes i
                JOIN turnos t ON t.id = i.turno_id
                JOIN eventos e ON e.id = t.evento_id
                JOIN participantes p ON p.id = i.participante_id
                WHERE e.titulo LIKE ? OR p.nome LIKE ?
                ORDER BY i.data_inscricao DESC";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute(["%$busca%", "%$busca%"]);
        return $cmd->fetchAll();
    }

    // Relatório: quantos inscritos cada turno de cada evento tem
    public function relatorioPorEvento() {
        $sql = "SELECT e.titulo, e.data_evento, t.turno, t.hora_inicio, t.hora_fim, t.vagas,
                       COUNT(i.id) AS inscritos
                FROM turnos t
                JOIN eventos e ON e.id = t.evento_id
                LEFT JOIN inscricoes i ON i.turno_id = t.id
                GROUP BY t.id
                ORDER BY inscritos DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    // Relatório: totais gerais do sistema
    public function totais() {
        $sql = "SELECT (SELECT COUNT(*) FROM eventos) AS eventos,
                       (SELECT COUNT(*) FROM participantes) AS participantes,
                       (SELECT COUNT(*) FROM inscricoes) AS inscricoes";
        return $this->pdo->query($sql)->fetch();
    }
}
