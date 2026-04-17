<?php

require_once __DIR__ . '../../DTO/recuperarSenhaDTO.php';
require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../utils/enviarEmail.php'; 
require_once __DIR__ . '/../models/Senha.php';
require_once __DIR__ . '/../base/BaseController.php';

use OpenApi\Attributes as OA;

class SenhaController extends BaseController
{
    private Usuarios $usuarioModel;
    private Senha $senhaModel;
    private PDO $db;
    private enviarEmail $enviarEmail;

    public function __construct(PDO $db)
    {
        parent::__construct(false);
        $this->usuarioModel = new Usuarios($db);
        $this->senhaModel = new Senha($db);
        $this->db = $db;
        $this->enviarEmail = new enviarEmail();
    }

    #[OA\Post(
        path: "/recuperar-senha",
        summary: "Solicita recuperação de senha",
        tags: ["Senha"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(
                        property: "email",
                        type: "string",
                        format: "email",
                        example: "teste@email.com"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Solicitação processada com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "mensagem",
                            type: "string",
                            example: "Se o e-mail estiver cadastrado, você receberá instruções para redefinir sua senha."
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "E-mail não informado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "mensagem", type: "string", example: "E-mail é obrigatório")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Erro ao gerar token ou enviar e-mail",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Erro ao enviar e-mail")
                    ]
                )
            )
        ]
    )]
    public function solicitarRecuperacao(): void
    {
        $emailUsuario = trim($this->data['email'] ?? '');

        if (!$emailUsuario) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "mensagem" => "E-mail é obrigatório"
            ]);
            return;
        }

        $usuario = $this->usuarioModel->buscarPorEmail($emailUsuario);

        $enviado = true;

        if ($usuario) { 
            $token = $this->senhaModel->gerarTokenSenha($usuario['id_usuario']);

            if (!$token) {
                $this->error("Erro ao gerar token", 500);
                return;
            }

            $enviado = $this->enviarEmailRecuperacao(
                $emailUsuario,
                $usuario['nome'],
                $token
            );
        }

        if (!$enviado) {
            $this->error("Erro ao enviar e-mail", 500);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "mensagem" => "Se o e-mail estiver cadastrado, você receberá instruções para redefinir sua senha."
        ]);
    }

    private function enviarEmailRecuperacao(string $email, string $nome, string $token): bool
    {
        $enviarEmailUsuario = new enviarEmail();

        return $enviarEmailUsuario->enviarEmail($email, $nome, $token);
    }

    #[OA\Post(
        path: "/redefinir-senha",
        summary: "Redefine a senha do usuário",
        tags: ["Senha"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["token", "senha"],
                properties: [
                    new OA\Property(
                        property: "token",
                        type: "string",
                        example: "a1b2c3d4e5f6g7h8"
                    ),
                    new OA\Property(
                        property: "senha",
                        type: "string",
                        format: "password",
                        example: "NovaSenha@123"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Senha redefinida com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "mensagem", type: "string", example: "Senha redefinida com sucesso")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 500,
                description: "Não foi possível atualizar a senha",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Nao foi possível atualizar a senha")
                    ]
                )
            )
        ]
    )]
    public function redefinirSenha(): void
    {
        $this->data;
        $idUsuario = $this->user->data->id_usuario;
        $token = $this->data['token'] ?? '';
        $senhaNova = trim($this->data['senha'] ?? '');

        $novaSenha = $this->senhaModel->gerarSenhaHash($senhaNova, $idUsuario, $token);

        if (!$novaSenha) {
            $this->error("Nao foi possível atualizar a senha", 500);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "success" => true, 
            "mensagem" => "Senha redefinida com sucesso"
        ]);
    }
}
