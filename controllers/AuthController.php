<?php

require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../models/Livro.php';
require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../DTO/RegisterUserDTO.php';
require_once __DIR__ . '/../DTO/LoginDTO.php';
require_once __DIR__ . '/../base/BaseController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

use OpenApi\Attributes as OA;

class AuthController extends BaseController
{
    private Usuarios $usuarioModel;
    private JwtHandler $jwtHandler;

    public function __construct(PDO $db)
    {
        parent::__construct(false);
        $this->usuarioModel = new Usuarios($db);
        $this->jwtHandler = new JwtHandler();
    }

    #[OA\Post(
        path: "/register",
        summary: "Realiza o cadastro de um novo usuário",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/RegisterRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Usuário registrado com sucesso",
                content: new OA\JsonContent(ref: "#/components/schemas/RegisterResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Dados inválidos ou email já cadastrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Erro interno ao cadastrar usuário",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function register(): void
    {
        try {
            $dto = new RegisterUserDTO($this->data);

            $usuarioExistente = $this->usuarioModel->buscarPorEmail($dto->email);

            if ($usuarioExistente) {
                $this->error("Email já cadastrado", 400);
                return;
            }

            $uuid = $this->gerarUUIDUsuario();

            $criado = $this->usuarioModel->criar(
                $dto->nome,
                $dto->email,
                $dto->senha_hash,
                $uuid
            );

            if (!$criado) {
                $this->error("Erro ao cadastrar usuário", 500);
                return;
            }

            $this->success([
                "mensagem" => "Usuário registrado com sucesso",
            ], 201);

        } catch (Exception $e) {
            $this->error("Erro ao cadastrar usuário", 400);
            echo json_encode([
                "success" => false,
                "mensagem" => $e->getMessage()
            ]);
        }
    }

    #[OA\Post(
        path: "/login",
        summary: "Realiza login do usuário",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LoginRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login realizado com sucesso",
                content: new OA\JsonContent(ref: "#/components/schemas/LoginResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Erro de validação na requisição",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Credenciais inválidas",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function login(): void
    {
        try {
            $dto = new LoginDTO($this->data);

            $usuario = $this->usuarioModel->buscarPorEmail($dto->email);

            if (!$usuario || !password_verify($dto->senha, $usuario["senha_hash"])) {
                $this->error("Email ou senha inválidos", 401);
                return;
            }

            $accessToken = $this->jwtHandler->gerarToken($usuario);

            $this->success([
                "mensagem" => "Login realizado com sucesso",
                "access_token" => $accessToken,
                "UUID" => $usuario["UUID"],
                "nome" => $usuario["nome"],
                "email" => $usuario["email"],
                "foto_perfil" => $usuario["foto_perfil"] ?? null
            ], 200);

        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
        }
    }

    private function gerarUUIDUsuario(int $tamanho = 30): string
    {
        return substr(bin2hex(random_bytes(20)), 0, $tamanho);
    }
}