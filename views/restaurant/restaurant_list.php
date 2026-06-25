<?php
// views/restaurant/restaurant_list.php
require_once dirname(dirname(__DIR__)) . '/base.php';
require_once dirname(dirname(__DIR__)) . '/models/dao/restaurantDAO.php';

$restaurantDAO = new RestaurantDAO();
$restaurants = $restaurantDAO->getAll();
?>

<div class="container">
    <style>
        .restaurant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0;
        }
        .restaurant-header h1 { color: #8b2538; margin: 0; }
        .btn-add {
            background-color: #8b2538;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-add:hover { background-color: #dcb382; color: #8b2538; }
        
        .restaurant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }
        .restaurant-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: 0.3s ease;
            border: 1px solid #eee;
        }
        .restaurant-card:hover { transform: translateY(-5px); }
        .restaurant-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .restaurant-body { padding: 20px; }
        .restaurant-title { font-size: 20px; color: #333; margin: 0 0 10px 0; font-weight: bold; }
        .restaurant-info { font-size: 14px; color: #666; margin-bottom: 8px; }
        .restaurant-info i { color: #dcb382; margin-right: 5px; }
        .stars { color: #ffc107; margin-bottom: 15px; }
        .btn-view {
            display: block;
            text-align: center;
            background-color: #f8f9fa;
            color: #8b2538;
            text-decoration: none;
            padding: 10px;
            border-radius: 6px;
            font-weight: 600;
            border: 1px solid #8b2538;
            transition: 0.3s;
        }
        .btn-view:hover { background-color: #8b2538; color: white; }
    </style>

    <div class="restaurant-header">
        <h1><i class="fa-solid fa-utensils"></i> Restaurantes Parceiros</h1>
        <a href="restaurant_form.php" class="btn-add"><i class="fa-solid fa-plus"></i> Novo Restaurante</a>
    </div>

    <?php if (empty($restaurants)): ?>
        <p style="text-align: center; color: #666; margin-top: 5px;">Nenhum restaurante cadastrado até o momento.</p>
    <?php else: ?>
        <div class="restaurant-grid">
            <?php foreach ($restaurants as $rest): ?>
                <div class="restaurant-card">
                    <img src="<?php echo $base_path; ?>uploads/<?php echo $rest->photo; ?>" class="restaurant-img" alt="<?php echo $rest->name; ?>">
                    <div class="restaurant-body">
                        <div class="restaurant-title"><?php echo $rest->name; ?></div>
                        
                        <div class="stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-regular fa-star"></i>
                            <span style="color: #666; font-size: 14px;">(4.0)</span>
                        </div>
                        
                        <div class="restaurant-info">
                            <i class="fa-solid fa-location-dot"></i> <?php echo $rest->address; ?>
                        </div>
                        <?php if(!empty($rest->phone)): ?>
                            <div class="restaurant-info">
                                <i class="fa-solid fa-phone"></i> <?php echo $rest->phone; ?>
                            </div>
                        <?php endif; ?>
                        
                        <p style="font-size: 14px; color: #555; line-height: 1.4;"><?php echo substr($rest->description, 0, 100) . '...'; ?></p>
                        
                        <a href="restaurant_edit.php?id=<?php echo $rest->id; ?>" class="btn-view">Ver Detalhes & Cardápio</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>