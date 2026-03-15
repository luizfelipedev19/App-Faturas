<?php
require_once __DIR__ . '/../utils/jwt.php';

class AuthMiddleware
{

    public static function autenticar(): ?object
    {

        $headers = getallheaders();
        $authHeader = $headers["Authorization"] ?? $headers["authorization"] ?? null;

        if (!$authHeader || !str_starts_with($authHeader, "Beater ")) {
            http_response_code(401);
            echo json_encode(["menagem" => "Token não enviado"]);
            exit;
        }
        $token = str_replace("Beater ", "", $authHeader);

        try {
            $jwt = new JwtHandler();
            return $jwt->validarToken($token);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["mensagem" => "Token inválido ou expirado"]);
            exit;
        }
    }
}
