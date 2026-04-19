<?php

require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../DTO/User/RegisterUserDTO.php';
require_once __DIR__ . '/../DTO/User/LoginUserDTO.php';
require_once __DIR__ . '/../base/BaseController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';


class AuthController extends BaseController
{
    private Users $userModel;
    private JwtHandler $jwtHandler;

    public function __construct(PDO $db)
    {
        parent::__construct(false);
        $this->userModel = new Users($db);
        $this->jwtHandler = new JwtHandler();
    }

    public function registerUser(): void
    {
        try {
            $dto = new RegisterUserDTO($this->data);

            $userExisting = $this->userModel->searchByEmail($dto->email);

            if ($userExisting) {
                $this->error("Email já cadastrado", 400);
                return;
            }

            //gerar alfanumérico
            $uuid = $this->genereteUUIDUser();

            $created = $this->userModel->createUser(
                $dto->name,
                $dto->email,
                $dto->password_hash,
                $uuid
            );

            if (!$created) {
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

    public function loginUser(): void
    {
        try {
            $dto = new LoginUserDTO($this->data);

            $user = $this->userModel->searchByEmail($dto->email);

            if (!$user || !password_verify($dto->password, $user["password_hash"])) {
                $this->error("Email ou senha inválidos", 401);
                return;
            }

            $accessToken = $this->jwtHandler->generateToken($user);

            $this->success([
                "mensagem" => "Login realizado com sucesso",
                "access_token" => $accessToken,
                "UUID" => $user["uuid"],
                "nome" => $user["name"],
                "email" => $user["email"]
            ], 200);

        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
        }
    }

    private function genereteUUIDUser(int $tamanho = 30): string
    {
        return substr(bin2hex(random_bytes(20)), 0, $tamanho);
    }
}