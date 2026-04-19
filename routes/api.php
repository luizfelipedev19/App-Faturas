<?php
return [
    [
        "method" => "POST",
        "path" => "/register",
        "controller" => "AuthController",
        "action" => "registerUser",
        "auth" => false
    ],
    [
        "method" => "POST",
        "path" => "/login",
        "controller" => "AuthController",
        "action" => "loginUser",
        "auth" => false
    ],
    [
        "method" => "GET",
        "path" => "/perfil",
        "controller" => "AuthController",
        "action" => "perfil",
        "auth" => true
    ],
    [
        "method" => "POST",
        "path" => "/livros",
        "controller" => "LivroController",
        "action" => "criarLivro",
        "auth" => true,
    ],
    [
        "method" => "GET",
        "path" => "/livros",
        "controller" => "LivroController",
        "action" => "listarLivros",
        "auth" => true
    ],
    [
    "method" => "PUT",
    "path" => "/livro/editar",
    "controller" => "LivroController",
    "action" => "atualizarLivro",
    "auth" => true
    ],
    [
    "method" => "DELETE",
    "path" => "/livro/deletar",
    "controller" => "LivroController",
    "action" => "deletarLivro",
    "auth" => true
    ],
    [
    "method" => "PATCH",
    "path" => "/usuario/foto",
    "controller" => "UsuarioController",
    "action" => "atualizarFoto",
    "auth" => true
    ],
    [
    "method" => "POST",
    "path" => "/recuperar-senha",
    "controller" => "SenhaController",
    "action" => "solicitarRecuperacao",
    "auth" => false
    ],
    [
    "method" => "POST",
    "path" => "/redefinir-senha",
    "controller" => "SenhaController",
    "action" => "redefinirSenha",
    "auth" => false
    ],
    [
    "method" => "PUT",
    "path" => "/usuario/editar",
    "controller" => "UsuarioController",
    "action" => "editarUsuarioLogado",
    "auth" => true
    ],
    [
    "method" => "DELETE",
    "path" => "/usuario/deletar",
    "controller" => "UsuarioController",
    "action" => "deletarUsuario",
    "auth" => true
    ],
    [
    "method" => "GET",
    "path" => "/usuario",
    "controller" => "UsuarioController",
    "action" => "listarUsuario",
    "auth" => true
    ],
];
