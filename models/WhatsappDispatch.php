<?php
class WhatsappDispatch
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO whatsapp_dispatches (
                invoice_id,
                customer_id,
                phone,
                template_id,
                success,
                status_code,
                error_message,
                request_payload,
                response_body
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data["invoice_id"],
            $data["customer_id"],
            $data["phone"],
            $data["template_id"],
            $data["success"],
            $data["status_code"],
            $data["error_message"],
            json_encode($data["request_payload"]),
            json_encode($data["response_body"])
        ]);
    }

    public function list()
    {
        return $this->db
            ->query("SELECT * FROM whatsapp_dispatches ORDER BY created_at DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>