<?php
class Turno {
    private $pdo;

    // Turnos que um evento pode ter
    public $turnos_validos = ["Manhã", "Tarde", "Noite"];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Confere os dados. Se algo estiver errado, lança uma exceção com a mensagem.
    private function validar($turno, $inicio, $fim, $vagas) {
        if (!in_array($turno, $this->turnos_validos)) {
            throw new Exception("Escolha o turno: Manhã, Tarde ou Noite.");
        }
        if ($inicio == "" || $fim == "") {
            throw new Exception("Informe o horário de início e de término.");
        }
        if ($fim <= $inicio) {
            throw new Exception("O horário de término deve ser depois do início.");
        }
        if (!is_numeric($vagas) || $vagas < 1) {
            throw new Exception("O número de vagas deve ser maior que zero.");
        }
    }

    // CREATE
    public function cadastrar($evento_id, $turno, $inicio, $fim, $vagas) {
        $this->validar($turno, $inicio, $fim, $vagas);

        // O evento existe?
        $cmd = $this->pdo->prepare("SELECT id FROM eventos WHERE id = ?");
        $cmd->execute([$evento_id]);
        if (!$cmd->fetch()) {
            throw new Exception("Evento não encontrado.");
        }

        // O evento já tem este turno?
        $cmd = $this->pdo->prepare("SELECT id FROM turnos WHERE evento_id = ? AND turno = ?");
        $cmd->execute([$evento_id, $turno]);
        if ($cmd->fetch()) {
            throw new Exception("Este evento já tem o turno da $turno.");
        }

        $sql = "INSERT INTO turnos (evento_id, turno, hora_inicio, hora_fim, vagas)
                VALUES (?, ?, ?, ?, ?)";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$evento_id, $turno, $inicio, $fim, $vagas]);
    }

    // DELETE (só se ninguém estiver inscrito no turno)
    public function excluir($id) {
        $cmd = $this->pdo->prepare("SELECT COUNT(*) FROM inscricoes WHERE turno_id = ?");
        $cmd->execute([$id]);
        if ($cmd->fetchColumn() > 0) {
            throw new Exception("Este turno já tem inscritos. Cancele as inscrições antes de excluir.");
        }

        $cmd = $this->pdo->prepare("DELETE FROM turnos WHERE id = ?");
        $cmd->execute([$id]);
    }

    // READ: turnos de um evento, com a quantidade de inscritos em cada um
    public function listarPorEvento($evento_id) {
        $sql = "SELECT t.*,
                       (SELECT COUNT(*) FROM inscricoes i WHERE i.turno_id = t.id) AS inscritos
                FROM turnos t
                WHERE t.evento_id = ?
                ORDER BY t.hora_inicio";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$evento_id]);
        return $cmd->fetchAll();
    }

    // READ: todos os turnos de todos os eventos (usado na tela de inscrição)
    public function listarTodos() {
        $sql = "SELECT t.*, e.titulo, e.data_evento,
                       (SELECT COUNT(*) FROM inscricoes i WHERE i.turno_id = t.id) AS inscritos
                FROM turnos t
                JOIN eventos e ON e.id = t.evento_id
                ORDER BY e.data_evento, e.titulo, t.hora_inicio";
        return $this->pdo->query($sql)->fetchAll();
    }
}
