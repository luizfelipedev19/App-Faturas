<?php
class Users
{
    private PDO $conn;
    private string $table = "users";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function searchByEmail(string $email): ?array
    {
        $query = "select user_id, name, email, password_hash, UUID from {$this->table} where email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function searchById(int $user_id): ?array {
        $query = "select user_id, name, email, password_hash, UUID from {$this->table}
        where user_id = :user_id limit 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_usuario", $user_id, PDO::PARAM_INT);
        $stmt->execute();


        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function createUser(string $name, string $email, string $passwordHash, string $uuid): bool
    {
        $created_at = date('Y-m-d H:i:s');


        $query = "insert into {$this->table} (name, email, password_hash, UUID, created_at) values (:name, :email, :password_hash, :uuid, :created_at)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":password_hash", $passwordHash);
        $stmt->bindValue(":uuid", $uuid);
        $stmt->bindValue(":created_at", $created_at);


        return $stmt->execute();
    }


    public function editUser(
        int $user_id,
        string $uuid,
        array $data
    ): bool {

        $fields = [];
        $params = [];

        if(isset($data['name'])){
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }

        if(isset($data['email'])){
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }

        if(empty($fields)){
            return false;
        }

        $fields[] = "updated_at = :updated_at";
        $params[':updated_at'] = date('Y-m-d H:i:s');
       
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . "
        WHERE user_id = :user_id AND UUID = :uuid"; 
        $stmt = $this->conn->prepare($query);

        foreach($params as $key => $value){
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindValue(":uuid", $uuid, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;


    }

    function deleteUser($userId): bool
    {
        $query = "delete from {$this->table} where user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
        return $stmt->execute();
        return $stmt->rowCount() > 0;

    }

    function listUsers(string $uuid): array {
        $query = "select user_id, name, email from {$this->table} where uuid = :uuid limit 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":uuid", $uuid, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: [];
    }

}