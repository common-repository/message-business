<?php

class Message_Business_Billing {

    private $firstName;
    private $lastName;
    private $email;
    private $companyName;
    private $address1;
    private $address2;
    private $zipcode;
    private $city;
    private $country;

    public function __construct(
        $firstName = '', $lastName = '', $email = '', $companyName = '',
        $address1 = '', $address2 = '', $zipcode = '', $city = '', $country = ''
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->companyName = $companyName;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->zipcode = $zipcode;
        $this->city = $city;
        $this->country = $country;
    }

    public function getFirstName() {
        if( empty($this->firstName) ) {
            return false;
        }
        return $this->firstName;
    }

    public function getLastName() {
        if( empty($this->lastName) ) {
            return false;
        }
        return $this->lastName;
    }

    public function getEmail() {
        if( empty($this->email) ) {
            return false;
        }
        return $this->email;
    }

    public function getCompanyName() {
        if( empty($this->companyName) ) {
            return false;
        }
        return $this->companyName;
    }

    public function getAddress1() {
        if( empty($this->address1) ) {
            return false;
        }
        return $this->address1;
    }

    public function getAddress2() {
        if( empty($this->address2) ) {
            return false;
        }
        return $this->address2;
    }

    public function getZipCode() {
        if( empty($this->zipcode) ) {
            return false;
        }
        return $this->zipcode;
    }

    public function getCity() {
        if( empty($this->city) ) {
            return false;
        }
        return $this->city;
    }

    public function getCountry() {
        if( empty($this->country) ) {
            return false;
        }
        return $this->country;
    }
}