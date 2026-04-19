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

    public function generateToken(array $user): string
    {

    //removi o exp do token
        $payload = [
            "iss" => $this->iss,
            "type" => "access",
            "iat" => time(),
            "data" => [
                "user_id" => $user["user_id"],
                "name" => $user["name"],
                "email" => $user["email"],
                "uuid" => $user["uuid"]
            ]
        ];

        return JWT::encode($payload, $this->secret, $this->alg);
    }

    public function validateToken(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, $this->alg));
    }

}
