<?php

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "API Livros",
    description: "Documentação da API Livros"
)]
#[OA\Server(
    url: "http://localhost/Api-Livros",
    description: "Servidor local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class OpenApiSpec
{
    
}