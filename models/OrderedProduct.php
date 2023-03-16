<?php

class OrderedProduct
{
    const TABLE_ORD_PRODUCT = 'ordered_products';
    const ID_ORD_PRODUCT = 'id_ord_product';
    const ORDERED_PRICE = 'ordered_price';
    const ID_PRODUCT = 'id_product';
    const PRODUCT_QUANTITY = 'quantity';

    private $conn;

    public ?int $id_ord_product;
    public float $ordered_price;
    public int $quantity;
    public int $id_product;
    public int $id_order;

    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }

    public function createOrderingProduct(): int|false
    {
        $query = 'INSERT INTO ' . $this::TABLE_ORD_PRODUCT . ' (' .
            $this::ORDERED_PRICE . ', ' .
            $this::PRODUCT_QUANTITY . ', ' .
            $this::ID_PRODUCT . ', ' .
            Order::ID_ORDER . ') ' .
            'VALUES(:price, :qty, :product, :order)';

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bindParam(':price', $this->ordered_price);
        $stmt->bindParam(':qty', $this->quantity);
        $stmt->bindParam(':product', $this->id_product);
        $stmt->bindParam(':order', $this->id_order);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $stmt->closeCursor();

        $query = 'SELECT MAX(' . $this::ID_ORD_PRODUCT . ') AS ID FROM ' . $this::TABLE_ORD_PRODUCT;

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
