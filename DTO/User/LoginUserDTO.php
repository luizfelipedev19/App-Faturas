<?php

class LoginUserDTO {
    public string $email;
    public string $password;

    public function __construct(array $data)
    {
        $this->email = trim($data['email'] ?? '');
        $this->password = trim($data['password'] ?? '');

        $this->validar();
    }

    private function validar(): void {
        if($this->email === ''){
            throw new Exception("Email é obrigatório");
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            throw new Exception("Email inválido");
        }

        if($this->password === ''){
            throw new Exception("Senha é obrigatório");
        }
    }
}