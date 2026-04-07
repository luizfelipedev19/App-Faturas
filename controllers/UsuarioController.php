<?php
require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../utils/verificarEmail.php';
require_once __DIR__ . '/../base/BaseController.php';

use OpenApi\Attributes as OA;

class UsuarioController extends BaseController
{
    private Usuarios $usuarioModel;
    private PDO $conn;

    public function __construct(PDO $db)
    {
        parent::__construct();
        $this->usuarioModel = new Usuarios($db);
        $this->conn = $db;
    }

    #[OA\Put(
        path: "/usuario/foto",
        summary: "Atualiza a foto de perfil do usuário autenticado",
        tags: ["Usuário"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["url_foto"],
                properties: [
                    new OA\Property(
                        property: "url_foto",
                        type: "string",
                        format: "uri",
                        example: "https://meusite.com/imagens/foto.jpg"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Foto de perfil atualizada com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Foto de perfil atualizada com sucesso"),
                        new OA\Property(property: "foto_perfil", type: "string", format: "uri", example: "https://meusite.com/imagens/foto.jpg")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "URL ausente ou inválida",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "A URL da foto é inválida")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 500,
                description: "Erro ao atualizar a foto de perfil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Erro ao atualizar a foto de perfil")
                    ]
                )
            )
        ]
    )]
    public function atualizarFoto(): void
    {
        $this->requireAuth();

        $idUsuario = $this->user->data->id_usuario;
        $urlFoto = $this->data["url_foto"] ?? null;

        if (!$urlFoto) {
            $this->error("A URL da foto não pode ser vazia", 400);
            return;
        }

        if (!filter_var($urlFoto, FILTER_VALIDATE_URL)) {
            $this->error("A URL da foto é inválida", 400);
            return;
        }

        $atualizado = $this->usuarioModel->atualizarFoto($idUsuario, $urlFoto);

        if (!$atualizado) {
            $this->error("Erro ao atualizar a foto de perfil", 500);
            return;
        }

        $this->success([
            "mensagem" => "Foto de perfil atualizada com sucesso",
            "foto_perfil" => $urlFoto
        ]);
    }

    #[OA\Put(
        path: "/usuario",
        summary: "Edita os dados do usuário autenticado",
        tags: ["Usuário"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string", example: "Luiz Felipe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "luiz@email.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Usuário atualizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Usuario atualizado com sucesso"),
                        new OA\Property(
                            property: "usuario",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id_usuario", type: "integer", example: 1)
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 409,
                description: "E-mail já está em uso por outro usuário",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "O email ja esta em uso por outro usuario")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Erro ao atualizar usuário",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Erro ao atualizar usuário")
                    ]
                )
            )
        ]
    )]
    public function editarUsuarioLogado(): void
    {
        $this->requireAuth();

        $idUsuario = $this->user->data->id_usuario;
        $uuid = $this->user->data->UUID;

        $validar = new verificarEmail($this->conn);

        if (isset($this->data['email'])) {
            if ($validar->verificarEmailEmUso($this->data['email'], $this->uuid)) {
                $this->error("O email ja esta em uso por outro usuario", 409);
                return;
            }
        }

        $atualizado = $this->usuarioModel->editarUsuario($idUsuario, $uuid, $this->data);

        if (!$atualizado) {
            $this->error("Erro ao atualizar usuário", 500);
            return;
        }

        $this->success([
            "mensagem" => "Usuario atualizado com sucesso",
            "usuario" => [
                "id_usuario" => $idUsuario
            ]
        ]);
    }

    #[OA\Delete(
        path: "/usuario",
        summary: "Deleta o usuário autenticado",
        tags: ["Usuário"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Usuário deletado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Usuário deletado com sucesso")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 500,
                description: "Erro ao deletar usuário",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Erro ao deletar usuário")
                    ]
                )
            )
        ]
    )]
    public function deletarUsuario(): void
    {
        $this->requireAuth();

        $idUsuario = $this->user->data->id_usuario;
        $deletado = $this->usuarioModel->deletarUsuario($idUsuario);

        if (!$deletado) {
            $this->error("Erro ao deletar usuário", 500);
            return;
        }

        $this->success([
            "mensagem" => "Usuário deletado com sucesso"
        ]);
    }

    #[OA\Get(
        path: "/usuario",
        summary: "Lista os dados do usuário autenticado",
        tags: ["Usuário"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Usuário encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Usuário encontrado"),
                        new OA\Property(
                            property: "usuario",
                            type: "object",
                            additionalProperties: true
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 404,
                description: "Usuário não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Usuário não encontrado")
                    ]
                )
            )
        ]
    )]
    public function listarUsuario(): void
    {
        $this->requireAuth();

        $uuid = $this->user->data->UUID;
        $dadosUsuario = $this->usuarioModel->listarUsuario($uuid);

        if (!$dadosUsuario) {
            $this->error("Usuário não encontrado", 404);
            return;
        }

        http_response_code(200);
        $this->success([
            "mensagem" => "Usuário encontrado",
            "usuario" => $dadosUsuario
        ]);
    }
}