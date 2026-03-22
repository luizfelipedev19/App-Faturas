<?php
class Usuarios
{
    private PDO $conn;
    private string $table = "usuarios";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function buscarPorEmail(string $email): ?array
    {
        $query = "select id_usuario, nome, email, senha_hash, UUID, foto_perfil from {$this->table} where email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function buscarPorId(int $id_usuario): ?array {
        $query = "select id_usuario, nome, email, senha_hash, UUID from {$this->table}
        where id_usuario = :id_usuario limit 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();


        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function criar(string $nome, string $email, string $senhaHash, string $uuid): bool
    {
        $query = "insert into {$this->table} (nome, email, senha_hash, UUID) values (:nome, :email, :senha_hash, :uuid)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":nome", $nome);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":senha_hash", $senhaHash);
        $stmt->bindValue(":uuid", $uuid);

        return $stmt->execute();
    }

    public function atualizarFoto(int $idUsuario, string $caminhoFoto): bool {
        $query = "update {$this->table} SET foto_perfil = :foto_perfil where id_usuario = :id_usuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":foto_perfil", $caminhoFoto);
        $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
