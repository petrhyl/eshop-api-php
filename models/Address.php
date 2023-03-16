<?php

class Address
{
    const TABLE_ADDRESS = 'addresses';
    const ID_ADDRESS = 'id_address';
    const STREET = 'street';
    const HOUSE = 'house_number';
    const TOWN = 'town';
    const POSTAL = 'postal_code';
    const COUNTRY = 'country';

    private $conn;

    public ?int $id_address;
    public string $street;
    public string $house;
    public string $town;
    public string $postal_code;
    public string $country;
    public ?int $id_customer;

    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }

    public function findAddressesByCustomerId(int $customer_id): array|false
    {
        $query =
            'SELECT ' . $this::ID_ADDRESS . ', ' . $this::STREET . ', ' . $this::HOUSE . ', ' . $this::TOWN . ', ' . $this::POSTAL . ', ' . $this::COUNTRY . ' ' .
            'FROM ' . $this::TABLE_ADDRESS . ' ' .
            'WHERE ' . Customer::ID_CUSTOMER . ' = :id';

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bindParam(':id', $customer_id);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id_address = htmlspecialchars($row[$this::ID_ADDRESS]);
            $this->street = htmlspecialchars($row[$this::STREET]);
            $this->house = htmlspecialchars($row[$this::HOUSE]);
            $this->town = htmlspecialchars($row[$this::TOWN]);
            $this->postal_code = htmlspecialchars($row[$this::POSTAL]);
            $this->country = htmlspecialchars($row[$this::COUNTRY]);

            $data[] = clone $this;
        }

        return $data;
    }

    public function createAddressOfCustomer(): int|false
    {
        $query =
            'INSERT INTO ' . $this::TABLE_ADDRESS . ' (' .
            $this::STREET . ', ' . $this::HOUSE . ', ' . $this::TOWN . ', ' .$this::POSTAL.', '. $this::COUNTRY . ', ' . Customer::ID_CUSTOMER
            . ') ' .
            'VALUES(:street, :house, :town, :postal, :country, :customer)';

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bindParam(':street', $this->street);
        $stmt->bindParam(':house', $this->house);
        $stmt->bindParam(':town', $this->town);
        $stmt->bindParam(':postal', $this->postal_code);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':customer', $this->id_customer);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $stmt->closeCursor();

        $query = 'SELECT MAX(' . $this::ID_ADDRESS . ') AS ID FROM ' . $this::TABLE_ADDRESS;

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
