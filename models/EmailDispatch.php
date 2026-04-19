<?php

class EmailDispatch {

private PDO $db;

public function __construct(PDO $db)
{
    $this->db = $db;
}

public function save(array $data){
    $stmt = $this->db->prepare(
        "INSERT INTO email_dispatches (
        invoice_id,
        customer_id,
        email_to,
        email_from,
        subject,
        provider, 
        message_id, 
        success, 
        status_code,
        error_message,
        request_payload,
        response_body
        ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

         return $stmt->execute([
            $data["invoice_id"],
            $data["customer_id"],
            $data["email_to"],
            $data["email_from"],
            $data["subject"],
            $data["provider"],
            $data["message_id"],
            $data["success"],
            $data["status_code"],
            $data["error_message"],
            json_encode($data["request_payload"]),
            json_encode($data["response_body"])
        ]);
    }

    public function list() {
        return $this->db->query("SELECT * FROM email_dispatches ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>