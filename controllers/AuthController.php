<?php
require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../models/Livro.php';
require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../DTO/RegisterUserDTO.php';
require_once __DIR__ . '/../DTO/LoginDTO.php';

class AuthController
{

    private Usuarios $usuarioModel;
    private Livro $livroModel;
    private JwtHandler $jwtHandler;

    public function __construct(PDO $db)
    {
        $this->usuarioModel = new Usuarios($db);
        $this->livroModel = new Livro($db);
        $this->jwtHandler = new JwtHandler();
    }

    public function register(): void
    {
        try { 
        $data = json_decode(file_get_contents("php://input"), true);

        $dto = new RegisterUserDTO($data);

        $usuarioExistente = $this->usuarioModel->buscarPorEmail($dto->email);

        if ($usuarioExistente) {
            http_response_code(409);
            echo json_encode(["Sucess" => "false",
                "mensagem" => "Email já cadastrado"]);
            return;
        }

        $criado = $this->usuarioModel->criar(
            $dto->nome,
            $dto->email,
            $dto->senha_hash);

        if ($criado) {
            http_response_code(201);
            echo json_encode(["sucess" => true,
            "mensagem" => "Usuário criado com sucesso"]);
            return;
        }

        http_response_code(500);
        echo json_encode(["sucess" => "false",
            "mensagem" => "Erro ao cadastrar usuário"]);

    } catch(Exception $e){
        http_response_code(400);
        echo json_encode(["sucess" => false,
        "mensagem" => $e->getMessage()]);
    }
} 



    public function login(): void
    {

        try{
        $data = json_decode(file_get_contents("php://input"), true);

        $dto = new LoginDTO($data);

        $usuario = $this->usuarioModel->buscarPorEmail($dto->email);

        if (!$usuario || !password_verify($dto->senha, $usuario["senha_hash"])) {
            http_response_code(401);
            echo json_encode(["sucess" => false,
             "mensagem" => "Email ou senha inválidos"]);
            return;
        }

        $accessToken = $this->jwtHandler->gerarToken($usuario);
        $refreshToken = $this->jwtHandler->gerarRefreshToken([
            "id_usuario" => $usuario["id_usuario"]
        ]);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "mensagem" => "Login realizado com sucesso",
            "access_token" => $accessToken,
            "refresh_token" => $refreshToken
        ]);
    } catch(Exception $e){
        http_response_code(400);
        echo json_encode([
            "sucess" => false,
            "mensagem" => $e->getMessage()
        ]);
    }
}

public function refresh(): void {
    try{
        $data = json_decode(file_get_contents("php://input"), true);


        $refreshToken = $data["refresh_token"] ?? null;

        if(!$refreshToken){
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "mensagem" => "Refresh token não enviado"
            ]);
            return;
        }

        $decoded = $this->jwtHandler->validarToken($refreshToken);

        if (($decoded->type ?? null) !== "refresh"){
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "mensagem" => "Token invalido para renovação"
            ]);
            return;
            }

            $idUsuario = $decoded->data->id_usuario ?? null;

            if (!$idUsuario){
                http_response_code(401);
                echo json_encode([
                    "success" => false, 
                    "mensagem" => "Refresh token inválido"
                ]);
                return;
            }

            $usuario = $this->usuarioModel->buscarPorId($usuario);

            if(!$usuario){
                http_response_code(404);
                echo json_encode(["success" => false,
                "mensagem" => "Usuario não encontrado"]);
                return;
            }

            $novoAcessToken = $this->jwtHandler->gerarToken($usuario);

            http_response_code(200);
            echo json_encode([
                "success" => true,
                "mensagem" => "Access token renovado com sucesso",
                "access_token" => $novoAcessToken
            ]);

    } catch(Exception $e){
        http_response_code(401);
        echo json_encode([
            "success" => false, 
            "mensagem" => "Refresh token inválido ou expirado"
        ]);
    }
}


    public function perfil()
    {
        $usuario = AuthMiddleware::autenticar();

        if(($usuario->type ?? null) !== "access"){
            http_response_code(401);
            echo json_encode([
                "success" => false, 
                "mensagem" => "Token inválido para acesso"
            ]);
            return;
        }

        $idUsuario = $usuario->data->id_usuario;

        echo json_encode([
            "mensagem" => "Perfil acessado com sucesso",
            "id_usuario" => $idUsuario,
            "usuario" => $usuario->data
        ]);
    }
}
