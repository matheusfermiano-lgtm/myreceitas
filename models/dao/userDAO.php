<?php

class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $phone;
    private $address;

    public function __construct($name, $email, $password, $phone = null, $address = null, $id = null) {
        $this->setName($name);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setPhone($phone);
        $this->setAddress($address);
        $this->setId($id);
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getPhone() { return $this->phone; }
    public function getAddress() { return $this->address; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($n) { $this->name = trim($n); }
    public function setEmail($e) { $this->email = trim($e); }
    public function setPassword($p) { $this->password = $p; }
    public function setPhone($p) { $this->phone = trim($p); }
    public function setAddress($a) { $this->address = trim($a); }

    public function __toString() {
        return "{$this->name} - {$this->email} - {$this->phone}";
    }
}
?>