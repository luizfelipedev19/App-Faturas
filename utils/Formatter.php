<?php

class Formatter {

public static function clearPhone($phone){
    return preg_replace('/\D/', '', $phone);
}

public static function phoneValid($phone){

$phone = self::clearPhone($phone);

return strlen($phone) >= 10 && strlen($phone) <= 11;
}

public static function clearText($texto){
    return trim(strip_tags($texto));
}

public static function prepareInvoice($invoice) {
    return [
        "name" => self::clearText($invoice["nome"] ?? ""),
        "email" => self::clearText($invoice["email"] ?? ""),
        "phone" => self::clearPhone($invoice["telefone"] ?? ""),
        "value" => $invoice["valor"] ?? 0,
        "maturity" => $invoice["vencimento"] ?? null
    ];
}

}

?>