<?php
class Chef {
    private $id;
    private $name;
    private $email;
    private $password;
    private $phone;
    private $address;
    private $description;
    private $professional_experience;
    private $services_offered;
    private $region_operation;
    private $photo;
    private $created_at;

    public function __construct($name, $email, $password, $id = null) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->id = $id;
    }

    // Getters e Setters para os novos campos
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getPhone() { return $this->phone; }
    public function setPhone($p) { $this->phone = $p; }
    public function getAddress() { return $this->address; }
    public function setAddress($a) { $this->address = $a; }
    public function getDescription() { return $this->description; }
    public function setDescription($d) { $this->description = $d; }
    public function getProfessionalExperience() { return $this->professional_experience; }
    public function setProfessionalExperience($e) { $this->professional_experience = $e; }
    public function getServicesOffered() { return $this->services_offered; }
    public function setServicesOffered($s) { $this->services_offered = $s; }
    public function getRegionOperation() { return $this->region_operation; }
    public function setRegionOperation($r) { $this->region_operation = $r; }
    public function getPhoto() { return $this->photo; }
    public function setPhoto($p) { $this->photo = $p; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($c) { $this->created_at = $c; }
}