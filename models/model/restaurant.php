<?php
class Restaurant {
    private $id;
    private $name;
    private $email;
    private $password;
    private $phone;
    private $address;
    private $location_map_link;
    private $description;
    private $photo;
    private $opening_hours;
    private $services_offered;
    private $menu_description;
    private $created_at;

    public function __construct($name, $email, $password, $id = null) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->id = $id;
    }

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getName() { return $this->name; }
    public function setName($name) {$this->name = $name;}
    public function getEmail() { return $this->email; }
    public function setEmail($email) {$this->email = $email;}
    public function getPassword() { return $this->password; }
    public function getPhone() { return $this->phone; }
    public function setPhone($p) { $this->phone = $p; }
    public function getAddress() { return $this->address; }
    public function setAddress($a) { $this->address = $a; }
    public function getLocationMapLink() { return $this->location_map_link; }
    public function setLocationMapLink($l) { $this->location_map_link = $l; }
    public function getDescription() { return $this->description; }
    public function setDescription($d) { $this->description = $d; }
    public function getPhoto() { return $this->photo; }
    public function setPhoto($p) { $this->photo = $p; }
    public function getOpeningHours() { return $this->opening_hours; }
    public function setOpeningHours($o) { $this->opening_hours = $o; }
    public function getServicesOffered() { return $this->services_offered; }
    public function setServicesOffered($s) { $this->services_offered = $s; }
    public function getMenuDescription() { return $this->menu_description; }
    public function setMenuDescription($m) { $this->menu_description = $m; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($c) { $this->created_at = $c; }
}