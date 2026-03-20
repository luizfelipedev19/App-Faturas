<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler
{
    private string $secret;
    private string $alg;
    private int $exp;
    private string $iss;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'];
        $this->alg = $_ENV['JWT_ALG'];
        $this->exp = (int) $_ENV['JWT_EXP'];
        $this->iss = $_ENV['JWT_ISS'];
    }

    public function gerarToken(array $usuario): string
    {
        $payload = [
            "iss" => $this->iss,
            "type" => "access",
            "iat" => time(),
            "exp" => time() + (60 * 15),
            "data" => [
                "id_usuario" => $usuario["id_usuario"],
                "nome" => $usuario["nome"],
                "email" => $usuario["email"]
            ]
        ];

        return JWT::encode($payload, $this->secret, $this->alg);
    }

    public function gerarRefreshToken(array $dados): string {

    $payload = [
        "iss" => $this->iss,
        "type" => "refresh",
        "iat" => time(),
        "exp" => time() + (60 * 60 * 24 * 7),
        "data" => $dados
    ];

    return JWT::encode($payload, $this->secret, $this->alg);
    }

    public function validarToken(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, $this->alg));
    }

}
