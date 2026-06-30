public function create(Restaurant $r) {
    try {
        $query = "INSERT INTO restaurants (name, email, password, address, phone, location_map_link, description, photo, opening_hours, services_offered, menu_description) 
                  VALUES (:name, :email, :password, :address, :phone, :map, :desc, :photo, :hours, :serv, :menu)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindValue(':name', $r->getName());
        $stmt->bindValue(':email', $r->getEmail());
        $stmt->bindValue(':password', $r->getPassword());
        $stmt->bindValue(':address', $r->getAddress());
        $stmt->bindValue(':phone', $r->getPhone());
        $stmt->bindValue(':map', $r->getLocationMapLink());
        $stmt->bindValue(':desc', $r->getDescription());
        $stmt->bindValue(':photo', $r->getPhoto() ?? 'default_restaurant.png');
        $stmt->bindValue(':hours', $r->getOpeningHours());
        $stmt->bindValue(':serv', $r->getServicesOffered());
        $stmt->bindValue(':menu', $r->getMenuDescription());
        
        $result = $stmt->execute();
        
        if($result) {
            $r->setId($this->conn->lastInsertId());
            return true;
        }
        return false;

    } catch (PDOException $e) {
        // Isso vai parar o site e mostrar EXATAMENTE o que o MySQL não gostou
        echo "<h3>Erro no Banco de Dados:</h3>";
        echo "Mensagem: " . $e->getMessage() . "<br>";
        echo "Código do erro: " . $e->getCode();
        die(); 
    }
}