<?php

use Message_Business_Billing as Billing;
use Message_Business_Shipping as Shipping;

class Message_Business_Customer {

    private $firstName;
    private $lastName;
    private $email;
    private $billing;
    private $shipping;
    private $turnover;

    public function __construct($firstName = '', $lastName = '', $email ='', Billing $billing, Shipping $shipping, $turnover = 0) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->billing = $billing;
        $this->shipping = $shipping;
        $this->turnover = $turnover;
    }

    public function getFirstName() {
        if( !empty($this->firstName) ) {
            return $this->firstName;
        } elseif( $this->billing->getFirstName() ) {
            return $this->billing->getFirstName();
        } elseif( $this->shipping->getFirstName() ) {
            return $this->shipping->getFirstName();
        }
        return '';
    }

    public function getLastName() {
        if( !empty($this->lastName) ) {
            return $this->lastName;
        } elseif( $this->billing->getLastName() ) {
            return $this->billing->getLastName();
        } elseif( $this->shipping->getLastName() ) {
            return $this->shipping->getLastName();
        }
        return '';
    }

    public function getEmail() {
        if( !empty($this->email) ) {
            return $this->email;
        } elseif( $this->billing->getEmail() ) {
            return $this->billing->getEmail();
        }
        return '';
    }

    public function getCompanyName() {
        if( $this->billing->getCompanyName() ) {
            return $this->billing->getCompanyName();
        } elseif( $this->shipping->getCompanyName() ) {
            return $this->shipping->getCompanyName();
        }
        return '';
    }

    public function getAddress1() {
        if( $this->billing->getAddress1() ) {
            return $this->billing->getAddress1();
        } elseif( $this->shipping->getAddress1() ) {
            return $this->shipping->getAddress1();
        }
        return '';
    }

    public function getAddress2() {
        if( $this->billing->getAddress2() ) {
            return $this->billing->getAddress2();
        } elseif( $this->shipping->getAddress2() ) {
            return $this->shipping->getAddress2();
        }
        return '';
    }

    public function getZipCode() {
        if( $this->billing->getZipCode() ) {
            return $this->billing->getZipCode();
        } elseif( $this->shipping->getZipCode() ) {
            return $this->shipping->getZipCode();
        }
        return '';
    }

    public function getCity() {
        if( $this->billing->getCity() ) {
            return $this->billing->getCity();
        } elseif( $this->shipping->getCity() ) {
            return $this->shipping->getCity();
        }
        return '';
    }

    public function getCountry() {
        if( $this->billing->getCountry() ) {
            return $this->billing->getCountry();
        } elseif( $this->shipping->getCountry() ) {
            return $this->shipping->getCountry();
        }
        return '';
    }

    public function getTurnover() {
        return $this->turnover;
    }
}