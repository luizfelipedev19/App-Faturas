<?php
require_once __DIR__ . '/../utils/jwt.php';

class AuthMiddleware
{

    public static function autenticar(): ?object
    {

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = $headers["Authorization"] ?? $headers["authorization"] ?? null;

        if (!$authHeader || !str_starts_with($authHeader, "Bearer ")) {
            http_response_code(401);
            echo json_encode(["menagem" => "Token não enviado"]);
            exit;
        }
        $token = str_replace("Bearer ", "", $authHeader);

        try {
            $jwt = new JwtHandler();
            $decoded = $jwt->validarToken($token);

            if(($decoded->type ?? null) !== "access"){
                http_response_code(401);
                echo json_encode(["mensagem" => "Token inválido para acesso"]);
                exit;
            }

            return $decoded;

        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["mensagem" => "Token inválido ou expirado"]);
            exit;
        }
    }
}
