<?php
class Participante {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Confere os dados. O $id serve para ignorar o próprio participante ao editar.
    private function validar($nome, $email, $id = 0) {
        if (trim($nome) == "") {
            throw new Exception("Informe o nome do participante.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Informe um e-mail válido.");
        }

        // Não pode haver dois participantes com o mesmo e-mail
        $cmd = $this->pdo->prepare("SELECT id FROM participantes WHERE email = ? AND id <> ?");
        $cmd->execute([$email, $id]);
        if ($cmd->fetch()) {
            throw new Exception("Já existe um participante com este e-mail.");
        }
    }

    // CREATE
    public function cadastrar($nome, $email, $telefone, $curso) {
        $this->validar($nome, $email);

        $sql = "INSERT INTO participantes (nome, email, telefone, curso) VALUES (?, ?, ?, ?)";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$nome, $email, $telefone, $curso]);
    }

    // UPDATE
    public function editar($id, $nome, $email, $telefone, $curso) {
        $this->validar($nome, $email, $id);

        $sql = "UPDATE participantes SET nome = ?, email = ?, telefone = ?, curso = ? WHERE id = ?";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute([$nome, $email, $telefone, $curso, $id]);
    }

    // DELETE (as inscrições do participante são apagadas junto, pelo banco)
    public function excluir($id) {
        $cmd = $this->pdo->prepare("DELETE FROM participantes WHERE id = ?");
        $cmd->execute([$id]);
    }

    // READ: lista todos (ou pesquisa por nome, e-mail ou curso)
    public function listar($busca = "") {
        $sql = "SELECT * FROM participantes
                WHERE nome LIKE ? OR email LIKE ? OR curso LIKE ?
                ORDER BY nome";
        $cmd = $this->pdo->prepare($sql);
        $cmd->execute(["%$busca%", "%$busca%", "%$busca%"]);
        return $cmd->fetchAll();
    }

    // READ: busca um participante pelo id
    public function buscarPorId($id) {
        $cmd = $this->pdo->prepare("SELECT * FROM participantes WHERE id = ?");
        $cmd->execute([$id]);
        return $cmd->fetch();
    }
}
