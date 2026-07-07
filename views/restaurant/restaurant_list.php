<?php
// views/restaurant/restaurant_list.php
require_once dirname(dirname(__DIR__)) . '/base.php';
require_once dirname(dirname(__DIR__)) . '/models/dao/restaurantDAO.php';

$restaurantDAO = new RestaurantDAO();
$restaurants = $restaurantDAO->getAll();
?>

<div class="container">

    <div class="restaurant-header">
        <h1><i class="fa-solid fa-utensils"></i> Restaurantes Parceiros</h1>
    </div>

    <?php if (empty($restaurants)): ?>
        <p style="text-align: center; color: #666; margin-top: 5px;">Nenhum restaurante cadastrado até o momento.</p>
    <?php else: ?>
        <div class="restaurant-grid">
            <?php foreach ($restaurants as $rest): ?>
                <div class="restaurant-card">
                <img src="../../assets/uploads/<?php echo htmlspecialchars($rest['photo']); ?>" class="restaurant-img" alt="<?php echo htmlspecialchars($rest['name']); ?>">
                    <div class="restaurant-body">
                        <div class="restaurant-title"><?php echo htmlspecialchars($rest['name']); ?></div>
                        
                        <div class="stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-regular fa-star"></i>
                            <span style="color: #666; font-size: 14px;">(4.0)</span>
                        </div>
                        
                        <div class="restaurant-info">
                            <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($rest['address']); ?>
                        </div>
                        <?php if(!empty($rest['phone'])): ?>
                            <div class="restaurant-info">
                                <i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($rest['phone']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <p style="font-size: 14px; color: #555; line-height: 1.4;"><?php echo htmlspecialchars(substr($rest['description'], 0, 100)) . '...'; ?></p>
                        
                        <a href="../restaurant_profile.php?id=<?php echo $rest['id']; ?>" class="btn-view">Ver Detalhes & Cardápio</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>