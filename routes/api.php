<?php
return [
    [
        "method" => "POST",
        "path" => "/register",
        "controller" => "AuthController",
        "action" => "register",
        "auth" => false
    ],
    [
        "method" => "POST",
        "path" => "/login",
        "controller" => "AuthController",
        "action" => "login",
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
        "path" => "/refresh",
        "controller" => "AuthController",
        "action" => "refresh", 
        "auth" => false
    ],

    [
        "method" => "POST",
        "path" => "/livros",
        "controller" => "LivroController",
        "action" => "create",
        "auth" => true,
    ],
    [
        "method" => "GET",
        "path" => "/livros",
        "controller" => "LivroController",
        "action" => "show",
        "auth" => true
    ],
    [
    "method" => "PUT",
    "path" => "/livro",
    "controller" => "LivroController",
    "action" => "update",
    "auth" => true
    ],
    [
    "method" => "DELETE",
    "path" => "/livro",
    "controller" => "LivroController",
    "action" => "delete",
    "auth" => true
    ],
];
