<?php

use OpenApi\Attributes as OA;

/*
|--------------------------------------------------------------------------
| REQUESTS
|--------------------------------------------------------------------------
| Schemas usados para documentar os corpos de requisição
| dos endpoints de autenticação.
*/

#[OA\Schema(
    schema: "RegisterRequest",
    type: "object",
    required: ["nome", "email", "senha"],
    properties: [
        new OA\Property(
            property: "nome",
            type: "string",
            maxLength: 255,
            example: "Luiz Felipe"
        ),
        new OA\Property(
            property: "email",
            type: "string",
            format: "email",
            example: "teste@email.com"
        ),
        new OA\Property(
            property: "senha",
            type: "string",
            format: "password",
            example: "12345678Ab@"
        )
    ]
)]
class RegisterRequestSchema {}

#[OA\Schema(
    schema: "LoginRequest",
    type: "object",
    required: ["email", "senha"],
    properties: [
        new OA\Property(
            property: "email",
            type: "string",
            format: "email",
            example: "teste@email.com"
        ),
        new OA\Property(
            property: "senha",
            type: "string",
            format: "password",
            example: "12345678Ab@"
        )
    ]
)]
class LoginRequestSchema {}


/*
|--------------------------------------------------------------------------
| RESOURCES
|--------------------------------------------------------------------------
| Schemas reutilizáveis de objetos retornados nas respostas.
*/

#[OA\Schema(
    schema: "LoginDetailResource",
    type: "object",
    properties: [
        new OA\Property(property: "mensagem", type: "string", example: "Login realizado com sucesso"),
        new OA\Property(property: "access_token", type: "string", example: "jwt_aqui"),
        new OA\Property(property: "UUID", type: "string", example: "e04230085f34fcdc518137ac826725"),
        new OA\Property(property: "nome", type: "string", example: "Luiz Felipe"),
        new OA\Property(property: "email", type: "string", format: "email", example: "teste@email.com"),
        new OA\Property(property: "foto_perfil", type: "string", nullable: true, example: null)
    ]
)]
class LoginDetailResourceSchema {}


/*
|--------------------------------------------------------------------------
| RESPONSES
|--------------------------------------------------------------------------
| Schemas padronizados das respostas dos endpoints de Auth.
*/

#[OA\Schema(
    schema: "RegisterResponse",
    type: "object",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: true),
        new OA\Property(
            property: "detail",
            type: "object",
            properties: [
                new OA\Property(property: "mensagem", type: "string", example: "Usuário registrado com sucesso")
            ]
        )
    ]
)]
class RegisterResponseSchema {}

#[OA\Schema(
    schema: "LoginResponse",
    type: "object",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: true),
        new OA\Property(
            property: "detail",
            ref: "#/components/schemas/LoginDetailResource"
        )
    ]
)]
class LoginResponseSchema {}
