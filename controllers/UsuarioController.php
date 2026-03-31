<?php
require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';


class UsuarioController
{
    private Usuarios $usuarioModel;

    public function __construct(PDO $db)
    {
        $this->usuarioModel = new Usuarios($db);
    }

    public function atualizarFoto(): void {
        $usuario = AuthMiddleware::autenticar();
        $idUsuario = $usuario->data->id_usuario;

        $rawInput = file_get_contents("php://input");

        file_put_contents(__DIR__ . '/../debug_back.log', 
        "RAW: $rawInput" . PHP_EOL, 
        FILE_APPEND
    );

        $data = json_decode($rawInput, true);
        $urlFoto = $data["url_foto"] ?? null;

        if(!$urlFoto){
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "mensagem" => "A URL da foto não pode ser vazia"
            ]);
            return;
        }

        if(!filter_var($urlFoto, FILTER_VALIDATE_URL)){
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "mensagem" => "A URL da foto é inválida"
            ]);
            return;
        }
        $atualizado = $this->usuarioModel->atualizarFoto($idUsuario, $urlFoto);


        if(!$atualizado){
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensagem" => "Erro ao atualziar a foto de perfil"
            ]);
            return;
        }
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "mensagem" => "Foto de perfil atualizada com sucesso",
            "foto_perfil" => $urlFoto
        ]);
    }

    
}