<?php
class Invoice {

private PDO $db; 

public function __construct(PDO $db)
{
    $this->db = $db;
}


//sava na tabela de faturas relacionada a emails
public function saveInvoices(array $data): bool
{
    $stmt = $this->db->prepare("
        INSERT INTO invoices (
            invoice_id,
            customer_id,
            customer_name,
            due_date,
            amount,
            plans,
            email,
            payment_url,
            source
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT (invoice_id) DO NOTHING
    ");

    return $stmt->execute([
        $data["invoice_id"],
        $data["customer_id"],
        $data["customer_name"],
        $data["due_date"],
        $data["amount"],
        $data["plans"],
        $data["email"],
        $data["payment_url"],
        $data["source"]
    ]);
}


//sava na tabela de 7 dias que é relacionado ao envio no whatsapp
public function saveInvoices7Days(array $data): bool
{
    $stmt = $this->db->prepare("
        INSERT INTO invoices_7_days (
            invoice_id,
            customer_id,
            customer_name,
            due_date,
            amount,
            plans,
            phone,
            payment_url,
            source
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT (invoice_id) DO NOTHING
    ");

    return $stmt->execute([
        $data["invoice_id"],
        $data["customer_id"],
        $data["customer_name"],
        $data["due_date"],
        $data["amount"],
        $data["plans"],
        $data["phone"],
        $data["payment_url"],
        $data["source"]
    ]);
}

//sava na tabela de 1 dia que é relacionado ao envio no whatsapp
public function saveInvoices1Day(array $data): bool
{
    $stmt = $this->db->prepare("
        INSERT INTO invoices_1_day_before (
            invoice_id,
            customer_id,
            customer_name,
            due_date,
            amount,
            plans,
            phone,
            payment_url,
            source
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT (invoice_id) DO NOTHING
    ");

    return $stmt->execute([
        $data["invoice_id"],
        $data["customer_id"],
        $data["customer_name"],
        $data["due_date"],
        $data["amount"],
        $data["plans"],
        $data["phone"],
        $data["payment_url"],
        $data["source"]
    ]);
}

//sava na tabela de 2 dias que é relacionado ao envio no whatsapp
public function saveInvoices2DaysAfter(array $data): bool
{
    $stmt = $this->db->prepare("
        INSERT INTO invoices_2_days_after (
            invoice_id,
            customer_id,
            customer_name,
            due_date,
            amount,
            plans,
            phone,
            payment_url,
            source
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT (invoice_id) DO NOTHING
    ");

    return $stmt->execute([
        $data["invoice_id"],
        $data["customer_id"],
        $data["customer_name"],
        $data["due_date"],
        $data["amount"],
        $data["plans"],
        $data["phone"],
        $data["payment_url"],
        $data["source"]
    ]);
}


//procurar fatura por codigo
public function searchByCodes(array $codes): array {
    if(empty($codes)) return [];

    $placeholders = implode(",", array_fill(0, count($codes), "?"));


    $stmt = $this->db->prepare(
        "SELECT * FROM invoices
        WHERE invoice_id IN ($placeholders)");


    $stmt->execute($codes);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//procurar a fatura pelo periodo
public function searchByPeriod(int $tipo): array {

    if($tipo === 7){
        $where = "due_date = CURRENT_DATE + INTERVAL '6 days'";
    } elseif ($tipo === 1){
        $where = "due_date = CURRENT_DATE + INTERVAL '1 day'";
    } elseif ($tipo === 2){
        $where = "due_date = CURRENT_DATE - INTERVAL '2 days'";
    } else {
        return [];
    }

    $stmt = $this->db->query("
    SELECT * FROM invoices WHERE $where");


    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function list(): array {
    $stmt = $this->db->query("SELECT * FROM invoices ORDER BY due_date ASC");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function updatedLink(int $invoiceId, string $link): bool {
    $stmt = $this->db->prepare("
    UPDATE invoices
    SET payment_url = ?, updated_at = NOW()
    WHERE invoice_id = ?");

    return $stmt->execute([$link, $invoiceId]);
}
}
?>