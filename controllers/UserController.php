<?php

require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../utils/checkEmail.php';
require_once __DIR__ . '/../base/BaseController.php';

use OpenApi\Attributes as OA;

class UserController extends BaseController
{
    private Users $userModel;
    private PDO $conn;

    public function __construct(PDO $db)
    {
        parent::__construct();
        $this->userModel = new Users($db);
        $this->conn = $db;
    }

    public function EditLoggedInUser(): void
    {
        $this->requireAuth();

        $UserId = $this->user->data->user_id;
        $uuid = $this->user->data->UUID;

        $validate = new checkEmail($this->conn);

        if (isset($this->data['email'])) {
            if ($validate->verifyEmailInUsed($this->data['email'], $this->uuid)) {
                $this->error("O email ja esta em uso por outro usuario", 409);
                return;
            }
        }

        $updated = $this->userModel->editUser($UserId, $uuid, $this->data);

        if (!$updated) {
            $this->error("Erro ao atualizar usuário", 500);
            return;
        }

        $this->success([
            "mensagem" => "Usuario atualizado com sucesso",
            "usuario" => [
                "user_id" => $UserId
            ]
        ]);
    }

    public function deleteUser(): void
    {
        $this->requireAuth();

        $UserId = $this->user->data->user_id;
        $deleted = $this->userModel->deleteUser($UserId);

        if (!$deleted) {
            $this->error("Erro ao deletar usuário", 500);
            return;
        }

        $this->success([
            "mensagem" => "Usuário deletado com sucesso"
        ]);
    }
    public function listUser(): void
    {
        $this->requireAuth();

        $uuid = $this->user->data->UUID;
        $dataUsers = $this->userModel->listUsers($uuid);

        if (!$dataUsers) {
            $this->error("Usuário não encontrado", 404);
            return;
        }

        $this->success([
            "mensagem" => "Usuário encontrado",
            "usuario" => $dataUsers
        ]);
    }
}