<?php

class RegisterUserDTO {
    public string $name;
    public string $email;
    public string $password;
    public string $password_hash;

public function __construct(array $data){

    $this->name = trim($data['name'] ?? '');
    $this->email = trim($data['email'] ?? '');
    $this->password = trim($data['password'] ?? '');

    $this->validar();

    //criptografando a senha
    $this->password_hash = password_hash($this->password, PASSWORD_DEFAULT);

}
     private function validar(): void
    {
        if ($this->name === '') {
            throw new Exception('Nome é obrigatório');
        }

        if ($this->email === '') {
            throw new Exception('Email é obrigatório');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }

        if ($this->password === '') {
            throw new Exception('Senha é obrigatória');
        }

        if (strlen($this->password) <= 8) {
            throw new Exception('A senha deve ter no mínimo 8 caracteres');
        }

        if (!preg_match('/[A-Z]/', $this->password)) {
            throw new Exception('A senha deve conter pelo menos uma letra maiúscula');
        }

        if (!preg_match('/[a-z]/', $this->password)) {
            throw new Exception('A senha deve conter pelo menos uma letra minúscula');
        }

        if (!preg_match('/[0-9]/', $this->password)) {
            throw new Exception('A senha deve conter pelo menos um número');
        }

        if (!preg_match('/[\W_]/', $this->password)) {
            throw new Exception('A senha deve conter pelo menos um caractere especial');
        }
    }
}
