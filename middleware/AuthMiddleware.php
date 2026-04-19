<?php
require_once __DIR__ . '/../utils/jwt.php';

class AuthMiddleware
{

    public static function authenticate(): ?object
    {

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = $headers["Authorization"] 
        ?? $headers["authorization"] 
        ?? $_SERVER["HTTP_AUTHORIZATION"]
        ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"]
        ?? null;

        $uuidHeader = $headers["X-User-UUID"]
        ?? $headers["x-user-uuid"]
        ?? $_SERVER["HTTP_X_USER_UUID"]
        ?? null;

        if (!$authHeader || !str_starts_with($authHeader, "Bearer ")) {
            http_response_code(401);
            echo json_encode(["mensagem" => "Token não enviado"]);
            exit;
        }
                //  API KEY
        if (!isset($headers['X-API-KEY']) || $headers['X-API-KEY'] !== $_ENV['API_KEY']) {
            http_response_code(401);
            echo json_encode(["erro" => "API Key inválida"]);
            exit;
        }

        if(!$uuidHeader){
            http_response_code(401);
            echo json_encode(["mensagem" => "UUID do usuário não enviado"]);
            exit;
        }

        $token = str_replace("Bearer ", "", $authHeader);

        try {
            $jwt = new JwtHandler();
            $decoded = $jwt->validateToken($token);

            if(($decoded->type ?? null) !== "access"){
                http_response_code(401);
                echo json_encode(["mensagem" => "Token inválido para acesso"]);
                exit;
            }


            $uuidToken = $decoded->data->UUID ?? null;
            if(!$uuidToken){
                http_response_code(401);
                echo json_encode(["mensagem" => "UUID não encontrado no token"]);
                exit;
            }

            if($uuidToken !== $uuidHeader){
                http_response_code(403);
                echo json_encode(["mensagem" => "UUID não corresponde ao usuário autenticado"]);
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
