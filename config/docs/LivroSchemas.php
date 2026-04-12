<?php
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "LivroResource",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 10),
        new OA\Property(property: "titulo", type: "string", example: "Dom Casmurro"),
        new OA\Property(property: "autor", type: "string", example: "Machado de Assis"),
        new OA\Property(property: "ano", type: "integer", nullable: true, example: 1899),
        new OA\Property(property: "genero", type: "string", nullable: true, example: "Romance"),
        new OA\Property(property: "status", type: "string", example: "quero_ler"),
        new OA\Property(property: "avaliacao", type: "integer", nullable: true, example: 5),
        new OA\Property(property: "anotacoes", type: "string", nullable: true, example: "Clássico da literatura brasileira")
    ]
)]
class LivroResourceSchema {}

#[OA\Schema(
    schema: "LivroCreateRequest",
    required: ["titulo", "autor", "ano", "status"],
    properties: [
        new OA\Property(property: "titulo", type: "string", example: "Dom Casmurro"),
        new OA\Property(property: "autor", type: "string", example: "Machado de Assis"),
        new OA\Property(property: "ano", type: "integer", example: 1899),
        new OA\Property(property: "genero", type: "string", nullable: true, example: "Romance"),
        new OA\Property(property: "status", type: "string", example: "quero_ler"),
        new OA\Property(property: "avaliacao", type: "integer", nullable: true, example: 5),
        new OA\Property(property: "anotacoes", type: "string", nullable: true, example: "Clássico da literatura brasileira")
    ],
    type: "object"
)]
class LivroCreateRequestSchema {}

#[OA\Schema(
    schema: "LivroUpdateRequest",
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
    ],
    type: "object"
)]
class LivroUpdateRequestSchema {}

#[OA\Schema(
    schema: "LivroDeleteRequest",
    required: ["id_livro"],
    properties: [
        new OA\Property(property: "id_livro", type: "integer", example: 10)
    ],
    type: "object"
)]
class LivroDeleteRequestSchema {}

#[OA\Schema (
    schema: "LivroListRequest",
    required: ["page", "limit"],
    properties: [
        new OA\Property(property: "page", type: "integer", example: 1),
        new OA\Property(property: "limit", type: "integer", example: 5),
    ],
    type: "object"
)]
class LivroListRequestSchema {}

#[OA\Schema(
    schema: "LivroListResponse",
    type: "object",
    properties: [
        new OA\Property(
            property: "detail",
            type: "object",
            properties: [
                new OA\Property(
                    property: "livros",
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/LivroResource")
                ),
                new OA\Property(
                    property: "paginacao",
                    type: "object",
                    properties: [
                        new OA\Property(property: "page", type: "integer", example: 1),
                        new OA\Property(property: "limit", type: "integer", example: 10),
                        new OA\Property(property: "total", type: "integer", example: 25),
                        new OA\Property(property: "total_pages", type: "integer", example: 3)
                    ]
                )
            ]
        )
    ]
)]
class LivroListResponseSchema {}


#[OA\Schema(
    schema: "LivroCreateResponse",
    type: "object",
    properties: [
        new OA\Property(property: "mensagem", type: "string", example: "Livro criado com sucesso"),
        new OA\Property(property: "id", type: "integer", example: 10)
    ]
)]
class LivroCreateResponseSchema {}

#[OA\Schema(
    schema: "LivroUpdateResponse",
    type: "object",
    properties: [
        new OA\Property(property: "mensagem", type: "string", example: "Livro atualizado com sucesso"),
        new OA\Property(
            property: "detail",
            type: "object",
            properties: [
                new OA\Property(
                    property: "livro",
                    ref: "#/components/schemas/LivroResource"
                )
            ]
        )
    ]
)]
class LivroUpdateResponseSchema {}

