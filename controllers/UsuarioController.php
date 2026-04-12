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
        security: [["bearerAuth" => [], "userUuid" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UsuarioFotoRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Foto atualizada",
                content: new OA\JsonContent(ref: "#/components/schemas/UsuarioFotoResponse")
            ),
            new OA\Response(
                response: 400,
                description: "URL inválida",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(
                response: 500,
                description: "Erro interno",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
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
        path: "/usuario/editar",
        summary: "Edita os dados do usuário autenticado",
        tags: ["Usuário"],
        security: [["bearerAuth" => [], "userUuid" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UsuarioUpdateRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Usuário atualizado",
                content: new OA\JsonContent(ref: "#/components/schemas/UsuarioUpdateResponse")
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(
                response: 409,
                description: "Email já em uso",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Erro interno",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
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
        path: "/usuario/deletar",
        summary: "Deleta o usuário autenticado",
        tags: ["Usuário"],
        security: [["bearerAuth" => [], "userUuid" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Usuário deletado",
                content: new OA\JsonContent(ref: "#/components/schemas/UsuarioDeleteResponse")
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(
                response: 500,
                description: "Erro interno",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
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
        security: [["bearerAuth" => [], "userUuid" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Usuário encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/UsuarioListResponse")
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(
                response: 404,
                description: "Usuário não encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
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

        $this->success([
            "mensagem" => "Usuário encontrado",
            "usuario" => $dadosUsuario
        ]);
    }
}