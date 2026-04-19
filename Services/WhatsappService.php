<?php
class WhatsappService {

public function mountPayload($invoice, $template = null): array {
    return [
        "phone_number" => Formatter::clearPhone($invoice->telefone),
        "client_id" => $_ENV["WPP_CLIENTE_ID"],
        "channel_id" => $_ENV["WPP_CHANNEL_ID"],
        "template_id" => $template ?? $_ENV["WPP_TEMPLATE_ID"],
        "sector_id" => $_ENV["WPP_SECTOR_ID"],
        "api_flow" => $invoice->codigo . "_" . time(),
        "webhook_on" => true, 
        "params" => [
            $invoice["nome"],
            $invoice["vencimento"],
            number_format($invoice["valor"], 2, ',', '.'),
            $invoice["payment_url"]
        ]
    ];


}


public function send($invoice, $template = null) {
    $payload = $this->mountPayload($invoice, $template);

    $response = ApiClient::request(
        "POST",
        $_ENV["WPP_URL"],
        [],
        $payload
    );

    if(!$response || isset($response["error"])){
        return [
            "success" => false,
            "response" => $response
        ];
    }

    return [
        "success" => true,
        "response" => $response
    ];
}
}
?>