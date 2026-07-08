<?php
// Volta 2 níveis: de views/chefs/ para a raiz do projeto
require_once dirname(__DIR__, 2) . '/base.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

$conn = database::getConexao();
// ... o resto do PHP continua exatamente igual

$busca = $_GET['busca'] ?? '';
$ordem = $_GET['ordem'] ?? 'recentes';

// 1. QUERY: Faz o JOIN com as avaliações reais do banco de dados
$sql = "SELECT 
            c.id, 
            c.name, 
            c.photo, 
            c.region_operation,
            COALESCE(AVG(cr.rating), 0) as avg_rating,
            COUNT(cr.id) as total_reviews
        FROM chef c
        LEFT JOIN chef_reviews cr ON c.id = cr.chef_id
        WHERE 1=1";
$params = [];

if (!empty($busca)) {
    $sql .= " AND c.name LIKE ?";
    $params[] = "%$busca%";
}

$sql .= " GROUP BY c.id, c.name, c.photo, c.region_operation";

// Ordenação dinâmica
if ($ordem === 'avaliacao') {
    $sql .= " ORDER BY avg_rating DESC, c.id DESC"; 
} elseif ($ordem === 'alfabetica') {
    $sql .= " ORDER BY c.name ASC";
} else {
    $sql .= " ORDER BY c.id DESC";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$chefs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-container">
    <div class="page-header">
        <h1><i class="fa-solid fa-utensils"></i> Nossos Chefs</h1>
        
        <form method="GET" class="filter-bar">
            <input type="text" name="busca" class="filter-input" placeholder="Buscar por nome..." value="<?php echo htmlspecialchars($busca); ?>">
            
            <select name="ordem" class="filter-select">
                <option value="recentes" <?php echo $ordem == 'recentes' ? 'selected' : ''; ?>>Mais Recentes</option>
                <option value="alfabetica" <?php echo $ordem == 'alfabetica' ? 'selected' : ''; ?>>Ordem Alfabética</option>
                <option value="avaliacao" <?php echo $ordem == 'avaliacao' ? 'selected' : ''; ?>>Melhores Avaliados</option>
            </select>
            
            <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Filtrar</button>
        </form>
    </div>

    <?php if(empty($chefs)): ?>
        <div style="text-align: center; padding: 50px; color: #888; background: #fff; border-radius: 12px;">
            <i class="fa-solid fa-face-frown" style="font-size: 3rem; margin-bottom: 15px; color: #ddd;"></i>
            <h3>Nenhum chef encontrado.</h3>
        </div>
    <?php else: ?>
        <div class="chefs-grid">
            <?php foreach($chefs as $c): 
                $foto = !empty($c['photo']) ? $c['photo'] : 'default_chef.png';
                $regiao = !empty($c['region_operation']) ? $c['region_operation'] : 'Região não informada';
                
                // Lógica matemática para pintura das estrelas do FontAwesome
                $notaMedia = round($c['avg_rating'] * 2) / 2; 
                $estrelasCheias = floor($notaMedia);
                $meiaEstrela = ($notaMedia - $estrelasCheias) > 0 ? 1 : 0;
                $estrelasVazias = 5 - $estrelasCheias - $meiaEstrela;
            ?>
               <a href="../user_profile.php?id=<?php echo $c['id']; ?>&type=chef" class="chef-card">
               <img src="<?php echo $base_path; ?>static/assets/uploads/<?php echo htmlspecialchars($foto); ?>" alt="Foto do Chef" class="chef-avatar">
                    <h3 class="chef-name"><?php echo htmlspecialchars($c['name']); ?></h3>
                    <div class="chef-region"><i class="fa-solid fa-map-location-dot"></i> <?php echo htmlspecialchars($regiao); ?></div>
                    
                    <div class="chef-rating">
                        <?php for($i = 0; $i < $estrelasCheias; $i++): ?>
                            <i class="fa-solid fa-star"></i>
                        <?php endfor; ?>
                        
                        <?php if($meiaEstrela): ?>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        <?php endif; ?>
                        
                        <?php for($i = 0; $i < $estrelasVazias; $i++): ?>
                            <i class="fa-regular fa-star"></i>
                        <?php endfor; ?>
                        
                        <span style="color: #666; margin-left: 5px; font-weight: bold;">
                            <?php echo number_format($c['avg_rating'], 1, ',', '.'); ?> 
                            <span style="font-size: 0.8em; font-weight: normal;">(<?php echo $c['total_reviews']; ?>)</span>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>