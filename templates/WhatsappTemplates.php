<?php
class WhatsappTemplates {

public static function getTemplate($tipo) {
    return [
        "template_padrao" => $_ENV["WPP_TEMPLATE_ID"]
    ];
    
}
}
?>