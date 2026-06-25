<?php
// models/model/restaurant.php

class Restaurant {
    public $id;
    public $name;
    public $email;      // ADICIONADO
    public $password;   // ADICIONADO
    public $address;
    public $phone;
    public $description;
    public $photo;
    public $menu_description;

    public function __construct($id = null, $name = null, $email = null, $password = null, $address = null, $phone = null, $description = null, $photo = null, $menu_description = null) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->address = $address;
        $this->phone = $phone;
        $this->description = $description;
        $this->photo = $photo;
        $this->menu_description = $menu_description;
    }
}