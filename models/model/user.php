<?php
class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $phone;
    private $address;
    private $created_at;

    public function __construct($name, $email, $password, $phone = null, $address = null, $id = null, $created_at = null) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->address = $address;
        $this->id = $id;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getPhone() { return $this->phone; }
    public function getAddress() { return $this->address; }
    public function getCreatedAt() { return $this->created_at; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setPassword($p) { $this->password = $p; }
}