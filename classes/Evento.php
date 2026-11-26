<?php
class Evento {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Confere os dados. 
    private function validar($titulo, $data, $local) {
        if (trim($titulo) == "") {
            throw new Exception("Informe o título do evento.");
        }
        if ($data == "") {
            throw new Exception("Informe a data do evento.");
        }
        if (trim($local) == "") {
            throw new Exception("Informe o local do evento.");
        }
    }

    // CREATE 
    public function cadastrar($titulo, $descricao, $data, $local) {
        $this->validar($titulo, $data, $local);

        $sql = "INSERT INTO eventos (titulo, descricao, data_evento, local_evento)
                VALUES (?, ?, ?, ?)";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$titulo, $descricao, $data, $local]);

        return $this->pdo->lastInsertId();
    }

    // UPDATE
    public function editar($id, $titulo, $descricao, $data, $local) {
        $this->validar($titulo, $data, $local);

        $atual = $this->buscarPorId($id);
        if ($atual["data_evento"] != $data) {
            $sql = "SELECT COUNT(*) FROM inscricoes i
                    JOIN turnos t ON t.id = i.turno_id
                    WHERE t.evento_id = ?";
            $cmd = $this->pdo->prepare($sql);
            $cmd->execute([$id]);
            if ($cmd->fetchColumn() > 0) {
                throw new Exception("Não é possível mudar a data de um evento que já tem inscritos.");
            }
        }

        $sql = "UPDATE eventos
                SET titulo = ?, descricao = ?, data_evento = ?, local_evento = ?
                WHERE id = ?";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$titulo, $descricao, $data, $local, $id]);
    }

    // DELETE 
    public function excluir($id) {
        $cmd = $this->pdo->prepare("DELETE FROM eventos WHERE id = ?");
        $cmd->execute([$id]);
    }

    // READ: 
    public function listar($busca = "") {
        $sql = "SELECT e.*,
                       (SELECT COUNT(*) FROM turnos t WHERE t.evento_id = e.id) AS qtd_turnos
                FROM eventos e
                WHERE e.titulo LIKE ? OR e.local_evento LIKE ?
                ORDER BY e.data_evento";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute(["%$busca%", "%$busca%"]);
        return $cmd->fetchAll();
    }

    // READ: 
    public function buscarPorId($id) {
        $cmd = $this->pdo->prepare("SELECT * FROM eventos WHERE id = ?");
        $cmd->execute([$id]);
        return $cmd->fetch();
    }
}
