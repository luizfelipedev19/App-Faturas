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
    ]
];
