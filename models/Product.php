<?php

class Product
{
    const TABLE_PRODUCT = 'products';
    const ID_PRODUCT = 'id_product';
    const NAME = 'name';
    const PRICE = 'price';
    const DESCRIPTION = 'description';

    private $conn;

    public int $id_product;
    public string $name;
    public float $price;
    public ?array $pictures;
    public ?string $description;

    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }

    function getAllProducts(): array|false
    {
        $query = 
            'SELECT r.id_pro AS ' . $this::ID_PRODUCT . ', r.nm AS ' . $this::NAME .
            ', r.pr AS ' . $this::PRICE . ', p.' . Picture::PICTURE_FILE . ' AS ' . Picture::PICTURE_FILE .
            ', r.id_pic AS ' . Picture::ID_PICTURE . ' ' .
            'FROM ('.
                'SELECT pro.' . $this::ID_PRODUCT . ' AS id_pro, pro.' . $this::NAME . ' AS nm, '.
                'pro.' . $this::PRICE . ' AS pr, MIN(pic.' . Picture::ID_PICTURE . ') AS id_pic '.
                'FROM ' . $this::TABLE_PRODUCT . ' AS pro '.
                'LEFT JOIN ' . Picture::TABLE_PICTURE . ' AS pic ON pro.' . $this::ID_PRODUCT . '=pic.' . $this::ID_PRODUCT . ' '.
                'GROUP BY pro.' . $this::ID_PRODUCT . ', pro.' . $this::NAME . ', pro.' . $this::PRICE .') AS r ' .
            'LEFT JOIN ' . Picture::TABLE_PICTURE . ' AS p ON r.id_pic=p.' . Picture::ID_PICTURE;

        $stmt = $this->conn->query($query);

        if ($stmt === false) {
            return false;
        }

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id_product = htmlspecialchars($row[$this::ID_PRODUCT]);
            $this->name = htmlspecialchars($row[$this::NAME]);
            $this->price = htmlspecialchars($row[$this::PRICE]);

            $pictures_arr = [];

            $picture = new Picture($this->conn);
            $picture->id_picture = htmlspecialchars($row[Picture::ID_PICTURE]);
            $picture->file_path = htmlspecialchars($row[Picture::PICTURE_FILE]);

            $pictures_arr[]=$picture;
            $this->pictures = $pictures_arr;

            $data[] = clone ($this);
        }

        return $data;
    }

    function getProductById(int $id): Product|false
    {
        $query =
            'SELECT ' . $this::ID_PRODUCT . ', ' . $this::NAME . ', ' . $this::PRICE . ', ' . $this::DESCRIPTION . ' ' .
            'FROM ' . $this::TABLE_PRODUCT . ' ' .
            'WHERE ' . $this::ID_PRODUCT . ' = :id';

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bindParam(':id', $id);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return false;
        }

        $this->id_product = htmlspecialchars($data[$this::ID_PRODUCT]);
        $this->name = htmlspecialchars($data[$this::NAME]);
        $this->price = htmlspecialchars($data[$this::PRICE]);
        $this->description = htmlspecialchars($data[$this::DESCRIPTION]);

        $stmt->closeCursor();

        $picture = new Picture($this->conn);

        $pictures_arr = $picture->getPicturesByProductId($id);

        if ($pictures_arr !== false) {
            $this->pictures = $pictures_arr;
        }

        return $this;
    }
}
