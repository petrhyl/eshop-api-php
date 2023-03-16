<?php

class Picture
{
    const TABLE_PICTURE = 'pictures';
    const ID_PICTURE = 'id_picture';
    const PICTURE_FILE = 'file_name';

    private $conn;

    public int $id_picture;
    public string $file_path;
    public ?int $id_product;

    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }

    function getPicturesByProductId(int $product_id): array|false
    {
        $query =
            'SELECT ' . $this::ID_PICTURE . ', ' . $this::PICTURE_FILE . ' ' .
            'FROM ' . $this::TABLE_PICTURE . ' ' .
            'WHERE ' . Product::ID_PRODUCT . ' = :id';

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bindParam(':id', $product_id);

        $result = $stmt->execute();

        if ($result === false) {
            return false;
        }

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id_picture = htmlspecialchars($row[$this::ID_PICTURE]);
            $this->file_path = htmlspecialchars($row[$this::PICTURE_FILE]);
            $this->id_product = htmlspecialchars($product_id);

            $data[] = clone $this;
        }

        return $data;
    }
}
