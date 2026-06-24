<?php
class Recipe {
    private $id;
    private $name;
    private $ingredients;
    private $description;
    private $preparation_time;
    private $category;
    private $price;
    private $is_public;
    private $user_id;
    private $chef_id;
    private $restaurant_id;

    public function __construct($name, $ingredients, $description = null, $preparation_time = null, $category = null, $price = 0.00, $is_public = 1, $user_id = null, $chef_id = null, $restaurant_id = null, $id = null) {
        $this->setName($name);
        $this->setIngredients($ingredients);
        $this->setDescription($description);
        $this->setPreparationTime($preparation_time);
        $this->setCategory($category);
        $this->setPrice($price);
        $this->setIsPublic($is_public);
        $this->setUserId($user_id);
        $this->setChefId($chef_id);
        $this->setRestaurantId($restaurant_id);
        $this->setId($id);
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getIngredients() { return $this->ingredients; }
    public function getDescription() { return $this->description; }
    public function getPreparationTime() { return $this->preparation_time; }
    public function getCategory() { return $this->category; }
    public function getPrice() { return $this->price; }
    public function getIsPublic() { return $this->is_public; }
    public function getUserId() { return $this->user_id; }
    public function getChefId() { return $this->chef_id; }
    public function getRestaurantId() { return $this->restaurant_id; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($n) { $this->name = trim($n); }
    public function setIngredients($i) { $this->ingredients = trim($i); }
    public function setDescription($d) { $this->description = trim($d); }
    public function setPreparationTime($pt) { $this->preparation_time = $pt; }
    public function setCategory($c) { $this->category = trim($c); }
    public function setPrice($p) { $this->price = (float)$p; }
    public function setIsPublic($ip) { $this->is_public = (int)$ip; }
    public function setUserId($uid) { $this->user_id = $uid; }
    public function setChefId($cid) { $this->chef_id = $cid; }
    public function setRestaurantId($rid) { $this->restaurant_id = $rid; }

    public function __toString() {
        return "{$this->name} - Categoria: {$this->category} - Preço: R$ " . number_format($this->price, 2, ',', '.');
    }
}
?>