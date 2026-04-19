<?php


class checkEmail{
    private PDO $conn;

    public function __construct(PDO $db){
        $this->conn = $db;
    }

    public function verifyEmailInUsed(string $email, string $uuid): bool {

    //seleciona o uuid do usuário que tem o email passado, se não encontrar nenhum usuário com esse email, retorna false, se encontrar, compara o uuid encontrado com o uuid passado, se for diferente, significa que o email já está em uso por outro usuário, então retorna true, caso contrário, retorna false
        $sql = "select uuid from users where email = :email limit 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        //retorna o uuid do usuário encontrado ou null se não encontrar nenhum usuário com esse email, no modelo array associativo, chave e valor. 
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$user){
            //aqui retorna false porque o e-mail não existe
            return false;
        
        }

        return $user['uuid'] !== $uuid; // aqui ele existe e ja esta em uso
    }

}
?>