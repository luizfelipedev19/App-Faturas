<?php

require_once __DIR__ . '/../models/Livro.php';
require_once __DIR__ . '/../DTO/CreateLivroDTO.php';
require_once __DIR__ . '/../DTO/UpdateLivroDTO.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../base/BaseController.php';

use OpenApi\Attributes as OA;

class LivroController extends BaseController
{
    private Livro $livroModel;

    public function __construct(PDO $db)
    {
        parent::__construct();
        $this->livroModel = new Livro($db);
    }

    #[OA\Post(
        path: "/livros",
        summary: "Cria um novo livro para o usuário autenticado",
        tags: ["Livros"],
        security: [["bearerAuth" => [], "userUuid" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LivroCreateRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Livro criado com sucesso",
                content: new OA\JsonContent(ref: "#/components/schemas/LivroCreateResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Dados inválidos",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 500,
                description: "Erro ao criar livro",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function criarLivro(): void
    {
        $this->requireAuth();

        try {
            $dto = new CreateLivroDTO($this->data);
        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
            return;
        }

        $idLivro = $this->livroModel->criarLivro(
            $dto->titulo,
            $dto->autor,
            $dto->ano,
            $this->uuid,
            $dto->genero,
            $dto->status,
            $dto->avaliacao,
            $dto->anotacoes
        );

        if (!$idLivro) {
            $this->error("Erro ao criar livro", 500);
            return;
        }

        $this->success([
            "mensagem" => "Livro criado com sucesso",
            "id" => $idLivro
        ], 201);
    }

    #[OA\Put(
        path: "/livro/editar",
        summary: "Atualiza um livro existente do usuário autenticado",
        tags: ["Livros"],
        security: [["bearerAuth" => [], "userUuid" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LivroUpdateRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Livro atualizado com sucesso",
                content: new OA\JsonContent(ref: "#/components/schemas/LivroUpdateResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Id do livro ausente ou dados inválidos",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 404,
                description: "Livro não encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Erro ao atualizar livro",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function atualizarLivro(): void
    {
        $this->requireAuth();

        $idLivro = $this->data['id_livro'] ?? null;

        if (!$idLivro) {
            $this->error("Id do livro é obrigatório", 400);
            return;
        }

        $livroAtual = $this->livroModel->buscarPorId((int) $idLivro, $this->uuid);

        if (!$livroAtual) {
            $this->error("Livro não encontrado", 404);
            return;
        }

        try {
            $dto = new UpdateLivroDTO($this->data);
        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
            return;
        }

        $atualizado = $this->livroModel->atualizarLivro(
            (int) $idLivro,
            $dto->titulo ?? $livroAtual['titulo'],
            $dto->autor ?? $livroAtual['autor'],
            $dto->ano ?? ((int) ($livroAtual['ano'] ?? 0)),
            $this->uuid,
            $dto->genero ?? ($livroAtual['genero'] ?? null),
            $dto->status ?? ($livroAtual['status'] ?? 'quero_ler'),
            $dto->avaliacao ?? ($livroAtual['avaliacao'] ?? null),
            $dto->anotacoes ?? ($livroAtual['anotacoes'] ?? null)
        );

        if (!$atualizado) {
            $this->error("Erro ao atualizar livro", 500);
            return;
        }

        $this->success([
            "mensagem" => "Livro atualizado com sucesso",
            "detail" => [
                "livro" => [
                    "id"        => (int) $idLivro,
                    "titulo"    => $dto->titulo ?? $livroAtual['titulo'],
                    "autor"     => $dto->autor ?? $livroAtual['autor'],
                    "ano"       => $dto->ano ?? $livroAtual['ano'],
                    "genero"    => $dto->genero ?? $livroAtual['genero'],
                    "status"    => $dto->status ?? $livroAtual['status'],
                    "avaliacao" => $dto->avaliacao ?? $livroAtual['avaliacao'],
                    "anotacoes" => $dto->anotacoes ?? $livroAtual['anotacoes']
                ]
            ]
        ]);
    }

    #[OA\Delete(
        path: "/livro/deletar",
        summary: "Deleta um livro do usuário autenticado",
        tags: ["Livros"],
        security: [["bearerAuth" => [], "userUuid" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LivroDeleteRequest")
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Livro deletado com sucesso"
            ),
            new OA\Response(
                response: 400,
                description: "Id do livro não informado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 404,
                description: "Livro não encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function deletarLivro(): void
    {
        $this->requireAuth();

        $idLivro = $this->data['id_livro'] ?? null;

        if (!$idLivro) {
            $this->error("Id do livro é obrigatório", 400);
            return;
        }

        $deletado = $this->livroModel->deletarLivro((int) $idLivro, $this->uuid);

        if (!$deletado) {
            $this->error("Livro não encontrado", 404);
            return;
        }

        http_response_code(204);
    }

    #[OA\Get(
        path: "/livros",
        summary: "Lista livros do usuário autenticado",
        tags: ["Livros"],
        security: [["bearerAuth" => [], "userUuid" => []]],
        parameters: [
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                description: "Página atual",
                schema: new OA\Schema(type: "integer", default: 1, example: 1)
            ),
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                description: "Quantidade de itens por página",
                schema: new OA\Schema(type: "integer", default: 10, example: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Livros retornados com sucesso",
                content: new OA\JsonContent(ref: "#/components/schemas/LivroListResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 404,
                description: "Nenhum livro encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]

    public function listarLivros(): void
    {
        $this->requireAuth();
        
        $pageDefault = 1;
        $limitDefault = 10;
        $_GET = $this->data;
        $page = $_GET['page'] ?? $this->data['page'] ?? $pageDefault;
        $limit = $_GET['limit'] ?? $this->data['limit'] ?? $limitDefault;

        if ($page < 1) $page = $pageDefault;
        if ($limit < 1) $limit = $limitDefault;
        if ($limit > 100) $limit = 100;

        $resultado = $this->livroModel->encontrarLivro(
            !empty($_GET) ? $_GET : $this->data,
            $this->uuid,
            $page,
            $limit
        );



        if (empty($resultado['livros'])) {
            $this->error("Nenhum livro encontrado", 404);
            return;
        }

        $this->success($resultado);
    }
}