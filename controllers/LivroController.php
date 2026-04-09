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
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["titulo", "autor", "ano", "status"],
                properties: [
                    new OA\Property(property: "titulo", type: "string", example: "Dom Casmurro"),
                    new OA\Property(property: "autor", type: "string", example: "Machado de Assis"),
                    new OA\Property(property: "ano", type: "integer", example: 1899),
                    new OA\Property(property: "genero", type: "string", nullable: true, example: "Romance"),
                    new OA\Property(property: "status", type: "string", example: "quero_ler"),
                    new OA\Property(property: "avaliacao", type: "integer", nullable: true, example: 5),
                    new OA\Property(property: "anotacoes", type: "string", nullable: true, example: "Clássico da literatura brasileira")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Livro criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Livro criado com sucesso"),
                        new OA\Property(property: "id", type: "integer", example: 10)
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Dados inválidos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Campo título é obrigatório")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 500,
                description: "Erro ao criar livro",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Erro ao criar livro")
                    ]
                )
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
        path: "/livros",
        summary: "Atualiza um livro existente do usuário autenticado",
        tags: ["Livros"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["id_livro"],
                properties: [
                    new OA\Property(property: "id_livro", type: "integer", example: 10),
                    new OA\Property(property: "titulo", type: "string", nullable: true, example: "Dom Casmurro - edição revisada"),
                    new OA\Property(property: "autor", type: "string", nullable: true, example: "Machado de Assis"),
                    new OA\Property(property: "ano", type: "integer", nullable: true, example: 1900),
                    new OA\Property(property: "genero", type: "string", nullable: true, example: "Romance"),
                    new OA\Property(property: "status", type: "string", nullable: true, example: "lendo"),
                    new OA\Property(property: "avaliacao", type: "integer", nullable: true, example: 4),
                    new OA\Property(property: "anotacoes", type: "string", nullable: true, example: "Leitura em andamento")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Livro atualizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Livro atualizado com sucesso"),
                        new OA\Property(
                            property: "detail",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "livro",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer", example: 10),
                                        new OA\Property(property: "titulo", type: "string", example: "Dom Casmurro - edição revisada"),
                                        new OA\Property(property: "autor", type: "string", example: "Machado de Assis"),
                                        new OA\Property(property: "ano", type: "integer", example: 1900),
                                        new OA\Property(property: "genero", type: "string", nullable: true, example: "Romance"),
                                        new OA\Property(property: "status", type: "string", example: "lendo"),
                                        new OA\Property(property: "avaliacao", type: "integer", nullable: true, example: 4),
                                        new OA\Property(property: "anotacoes", type: "string", nullable: true, example: "Leitura em andamento")
                                    ]
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Id do livro ausente ou dados inválidos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Id do livro é obrigatório")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 404,
                description: "Livro não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Livro não encontrado")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Erro ao atualizar livro",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Erro ao atualizar livro")
                    ]
                )
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
            $this->error($e->getMessage());
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
        path: "/livros",
        summary: "Deleta um livro do usuário autenticado",
        tags: ["Livros"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["id_livro"],
                properties: [
                    new OA\Property(property: "id_livro", type: "integer", example: 10)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Livro deletado com sucesso"
            ),
            new OA\Response(
                response: 400,
                description: "Id do livro não informado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Id do livro é obrigatório")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Não autenticado"
            ),
            new OA\Response(
                response: 404,
                description: "Livro não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Livro não encontrado")
                    ]
                )
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
        path: "/livros/listar",
        summary: "Lista livros do usuário autenticado",
        tags: ["Livros"],
        security: [["bearerAuth" => []]],
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
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "detail",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "livros",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "titulo", type: "string", example: "Dom Casmurro"),
                                            new OA\Property(property: "autor", type: "string", example: "Machado de Assis"),
                                            new OA\Property(property: "ano", type: "integer", nullable: true, example: 1899),
                                            new OA\Property(property: "genero", type: "string", example: "Romance"),
                                            new OA\Property(property: "status", type: "string", example: "lendo"),
                                            new OA\Property(property: "avaliacao", type: "integer", nullable: true, example: 5),
                                            new OA\Property(property: "anotacoes", type: "string", nullable: true, example: "Muito bom")
                                        ],
                                        type: "object"
                                    )
                                ),
                                new OA\Property(
                                    property: "paginacao",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "page", type: "integer", example: 1),
                                        new OA\Property(property: "limit", type: "integer", example: 10),
                                        new OA\Property(property: "total", type: "integer", example: 1),
                                        new OA\Property(property: "total_pages", type: "integer", example: 1)
                                    ]
                                )
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
                response: 404,
                description: "Nenhum livro encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "mensagem", type: "string", example: "Nenhum livro encontrado")
                    ]
                )
            )
        ]
    )]
    public function listarLivros(): void
{
    $this->requireAuth();

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

    if ($page < 1) $page = 1;
    if ($limit < 1) $limit = 10;
    if ($limit > 100) $limit = 100;

    $livrosEncontrados = $this->livroModel->encontrarLivro($this->data, $this->uuid);
    $livroCount = count($livrosEncontrados);

    if ($livroCount === 0) {
        $this->error("Nenhum livro encontrado", 404);
        return;
    }

    $this->success([
        "livros" => $livrosEncontrados,
        "paginacao" => [
            "page" => $page,
            "limit" => $limit,
            "total" => $livroCount,
            "total_pages" => (int) ceil($livroCount / $limit)
        ]
    ]);
}
}