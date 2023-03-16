<?php

class Customer
{
    const TABLE_CUSTOMERS = 'customers';
    const ID_CUSTOMER = 'id_customer';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const PHONE = 'phone';
    const EMAIL = 'email';

    private $conn;

    public ?int $id_customer;
    public string $firstname;
    public string $lastname;
    public int $phone;
    public string $email;

    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }

    public function findCustomerIdByNameAndEmail(string $customer_firstname, string $customer_lastname, string $customer_email): int|false
    {
        $query =
            'SELECT ' . $this::ID_CUSTOMER . ' ' .
            'FROM ' . $this::TABLE_CUSTOMERS . ' ' .
            'WHERE (' . $this::FIRSTNAME . ' = ' . ':frstnm) AND (' . $this::LASTNAME . ' = ' . ':lstnm) AND (' . $this::EMAIL . ' = :email)';

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bindParam(':frstnm', $customer_firstname);
        $stmt->bindParam(':lstnm', $customer_lastname);
        $stmt->bindParam(':email', $customer_email);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return 0;
        }

        return $data[$this::ID_CUSTOMER];
    }

    public function findCustomerByEmail(string $customer_email): Customer|false
    {
        $query =
            'SELECT ' . $this::ID_CUSTOMER . ', ' . $this::FIRSTNAME . ', ' . $this::LASTNAME . ', ' . $this::PHONE . ', ' . $this::EMAIL . ' ' .
            'FROM ' . $this::TABLE_CUSTOMERS . ' ' .
            'WHERE ' . $this::EMAIL . ' = :email';

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bindParam(':email', $customer_email);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            $this->id_customer = 0;
            $this->firstname = '';
            $this->lastname = '';
            $this->phone = 0;
            $this->email = '';

            return $this;
        }

        $this->id_customer = $data[$this::ID_CUSTOMER];
        $this->firstname = $data[$this::FIRSTNAME];
        $this->lastname = $data[$this::LASTNAME];
        $this->phone = $data[$this::PHONE];
        $this->email = $data[$this::EMAIL];

        return $this;
    }

    public function createCustomer(): int|false
    {
        $query =
            'INSERT INTO ' . $this::TABLE_CUSTOMERS . ' (' .
            $this::FIRSTNAME . ', ' . $this::LASTNAME . ', ' . $this::PHONE . ', ' . $this::EMAIL .
            ') VALUES(:frstnm, :lstnm, :phone, :email)';

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bindParam(':frstnm', $this->firstname);
        $stmt->bindParam(':lstnm', $this->lastname);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $stmt->closeCursor();

        $query = 'SELECT MAX(' . $this::ID_CUSTOMER . ') AS ID FROM ' . $this::TABLE_CUSTOMERS;

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
