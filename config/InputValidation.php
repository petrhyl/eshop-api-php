<?php

class InputValidation
{
    private const NAME_REGEX =
    "/^(([A-ZŠŽČŘĎŤŇŸĹĽŔŚŹĆŃÀ-ÖÙ-Ý]{1}[a-zšžřčťďňěľĺŕůśźćńà-ïñ-öù-ÿ]*[ \-']*[A-ZŠŽČŘĎŤŇŸĹĽŔŚŹĆŃÀ-ÖÙ-Ý]*[a-zšžřčťďňěľĺŕůśźćńà-ïñ-öù-ÿ]+)+|([A-ZŠŽČŘĎŤŇŸĹĽŔŚŹĆŃÀ-ÖÙ-Ý]{1}[a-zšžřčťďňěľĺŕůśźćńà-ïñ-öù-ÿ]+))$/";
    private const HOUSE_REGEX = '/^[\w \-\.\/]+$/';
    private const EXIT_CODE = 422;

    private array $invalids;

    public function __construct()
    {
        $this->invalids = [];
    }

    private function namesValidation(array $names): bool
    {
        $valid = true;

        foreach ($names as $key => $value) {
            if (preg_match($this::NAME_REGEX, $value) !== 1) {
                $this->invalids[] = $key;
                $valid = false;
            }
        }

        return $valid;
    }

    private function emailValidation($email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->invalids[] = 'e-mail';
            return false;
        }

        return true;
    }

    private function houseNumberValidation($house): bool
    {
        if (preg_match($this::HOUSE_REGEX, $house) !== 1) {
            $this->invalids[] = 'house number';
            return false;
        }

        return true;
    }

    private function postalCodeValidation($postal): bool
    {
        if (preg_match('/[0-9]{5}/', $postal) !== 1) {
            $this->invalids[] = 'postal code';
            return false;
        }

        return true;
    }

    private function integerValidation(array $int_numbers): bool
    {
        $valid = true;

        foreach ($int_numbers as $key => $value) {
            if (!filter_var($value, FILTER_VALIDATE_INT)) {
                $this->invalids[] = $key;
                $valid = false;
            }elseif($value <= 0){
                $this->invalids[] = $key;
                $valid = false;
            }
        }

        return $valid;
    }

    private function priceValidation($price): bool
    {
        if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
            $this->invalids[] = 'price';
            return false;
        }

        if ($price <= 0) {
            $this->invalids[] = 'price';
            return false;
        }
        return true;
    }

    private function createErrorMessage(): string
    {
        $message_str = 'Invalid format of ';

        for ($i = 0; $i < count($this->invalids); $i++) {
            $message_str .= $this->invalids[$i] . ', ';
        }

        return substr($message_str, 0, strlen($message_str) - 2) . '.';
    }

    public function ValidateCustomerWithAddress(
        $firstname,
        $lastname,
        $phone_number,
        $email,
        $street,
        $house_number,
        $town,
        $postal_code,
        $country
    ) {
        $names_array['first name'] = $firstname;
        $names_array['last name'] = $lastname;
        $names_array['street'] = $street;
        $names_array['town'] = $town;
        $names_array['country'] = $country;

        $valid = true;

        if (!$this->namesValidation($names_array)) {
            $valid = false;
        }
        if (!$this->emailValidation($email)) {
            $valid = false;
        }
        if (!$this->integerValidation(['phone number' => $phone_number])) {
            $valid = false;
        }
        if (!$this->houseNumberValidation($house_number)) {
            $valid = false;
        }
        if (!$this->postalCodeValidation($postal_code)) {
            $valid = false;
        }
        if (!$valid) {
            throw new DomainException($this->createErrorMessage(), $this::EXIT_CODE);
        }
    }

    public function ValidateCustomer($firstname, $lastname, $email)
    {
        $valid = true;

        if (!$this->namesValidation(['first name' => $firstname, 'last name' => $lastname])) {
            $valid = false;
        }
        if (!$this->emailValidation($email)) {
            $valid = false;
        }

        if (!$valid) {
            throw new DomainException($this->createErrorMessage(), $this::EXIT_CODE);
        }
    }

    public function ValidateOrder($cutomer_id, $product_id, $price, $quantity)
    {
        $ints_array['customer id'] = $cutomer_id;
        $ints_array['product id'] = $product_id;
        $ints_array['product amount'] = $quantity;

        $valid = true;

        if (!$this->integerValidation($ints_array)) {
            $valid = false;
        }
        if (!$this->priceValidation($price)) {
            $valid = false;
        }

        if (!$valid) {
            throw new DomainException($this->createErrorMessage(), $this::EXIT_CODE);
        }
    }

    public function ValidateAddress($customer_id, $street, $house_number, $town, $postal_code, $country)
    {
        $names['street'] = $street;
        $names['town'] = $town;
        $names['country'] = $country;

        $valid = true;

        if (!$this->namesValidation($names)) {
            $valid = false;
        }
        if (!$this->houseNumberValidation($house_number)) {
            $valid = false;
        }
        if (!$this->postalCodeValidation($postal_code)) {
            $valid = false;
        }
        if (!$this->integerValidation(['customer id' => $customer_id])) {
            $valid = false;
        }

        if (!$valid) {
            throw new DomainException($this->createErrorMessage(), $this::EXIT_CODE);
        }
    }
}
