<?php

class ApiClient {

public static function request($method, $url, $query = []) {
    
//montar query string

if(!empty($query)){
    $url .= '?' . http_build_query($query);
}

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url, 
    CURLOPT_RETURNTRANSFER => true, 
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ], 
]);

$response = curl_exec($ch);

if($response === false) {
    return [
        "erro" => curl_error($ch)
    ];
}

curl_close($ch);

return json_decode($response, true);

}
}

?>