<?php

class Order
{
    const TABLE_ORDER = 'orders';
    const ID_ORDER = 'id_order';
    const DATETIME = 'order_date';

    private $conn;

    public ?int $id_order;
    public DateTime $order_date;
    public int $id_customer;
    public ?array $ordered_products;

    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }

    public function createOrder(): int|false
    {
        $query =
            'INSERT INTO ' . $this::TABLE_ORDER . ' (' .
            $this::DATETIME . ', ' . Customer::ID_CUSTOMER .
            ') VALUES(:dt, :id)';

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $date= date_format($this->order_date,'Y-m-d');

        $stmt->bindParam(':dt', $date);
        $stmt->bindParam(':id', $this->id_customer);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $stmt->closeCursor();

        $query = 'SELECT MAX(' . $this::ID_ORDER . ') AS ID FROM ' . $this::TABLE_ORDER;

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return false;
        }

        return $data['ID'];
    }
}
