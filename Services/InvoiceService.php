<?php

class InvoiceService {

private invoice $invoiceModel;

public function __construct(PDO $db)
{
    $this->invoiceModel = new Invoice($db);
}

public function searchByCodes(array $codes){
    return $this->invoiceModel->searchByCodes($codes);
}

public function searchByPeriod(int $tipo) {
    return $this->invoiceModel->searchByPeriod($tipo);
}

//faturas que vão vencer daqui 7 dias
public function searchWinning7Days(){
    
    $url = $_ENV["ADMIN_URL"];    

    $response = ApiClient::request("POST", $url, [
        "tpfat" => 6,
        "ckv" => "false"
    ]);

    //faturas tratadas
    $invoiceTreated = [];

    foreach ($response as $invoice){
        $invoiceClear = Formatter::prepareInvoice($invoice);

        if(!Formatter::clearPhone  ($invoiceClear["telefone"])) {
            continue; // vai ignorar os inválidos
        }

        $invoiceTreated[] = $invoiceClear;
         
    }

    return $invoiceTreated;
}

//faturas que venceram há 2 dias
public function searchExpired2Days(){

    $url = $_ENV["ADMIN_URL"];

    $response = ApiClient::request("POST", $url, [
        "tpfat" => 2,
        "ckv" => "true"
    ]);
    
}

//faturas que vão vencer amanhã
public function searchWinning1Day(){

    $url = $_ENV["ADMIN_URL"];

    $response = ApiClient::request("POST", $url, [
        "tpfat" => 1,
        "ckv" => "false"
    ]);

}

}

?>