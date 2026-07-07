<?php
// views/restaurant/restaurant_edit.php
require_once dirname(dirname(__DIR__)) . '/base.php';
require_once dirname(dirname(__DIR__)) . '/models/dao/restaurantDAO.php';

$restaurantDAO = new RestaurantDAO();
$restaurant = null;

if (isset($_GET['id'])) {
    $restaurant = $restaurantDAO->getById($_GET['id']);
}

if (!$restaurant) {
    echo "<div class='container'><p>Restaurante não encontrado.</p></div>";
    exit;
}

$chefs = $restaurantDAO->getChefsByRestaurant($restaurant->id);
?>

<div class="container">

    <div class="profile-container">
        <div class="side-info">
            <img src="<?php echo $base_path; ?>uploads/<?php echo $restaurant->photo; ?>" class="profile-img">
            <h1><?php echo $restaurant->name; ?></h1>
            <p><i class="fa-solid fa-location-dot" style="color:#dcb382"></i> <?php echo $restaurant->address; ?></p>
            <p><i class="fa-solid fa-phone" style="color:#dcb382"></i> <?php echo $restaurant->phone; ?></p>
        </div>

        <div class="main-content">
            <h2>Sobre o Restaurante</h2>
            <p style="line-height: 1.6; color: #444;"><?php echo nl2br($restaurant->description); ?></p>
            
            <div class="section-box">
                <h3><i class="fa-solid fa-book-open"></i> Cardápio da Casa</h3>
                <p style="line-height: 1.6; color: #444;"><?php echo nl2br($restaurant->menu_description); ?></p> </div>

            <div class="section-box">
                <h3><i class="fa-solid fa-user-tie"></i> Nossos Chefs Cadastrados</h3>
                <?php if (empty($chefs)): ?>
                    <p style="color: #777;">Nenhum chefe associado a este estabelecimento ainda.</p>
                <?php else: ?>
                    <div>
                        <?php foreach ($chefs as $chef): ?>
                            <div class="chef-badge">
                                <i class="fa-solid fa-kitchen-set"></i> 
                                <?php echo $chef['name']; ?> </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>