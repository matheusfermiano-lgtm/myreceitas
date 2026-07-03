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

<style>
    .page-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-header h1 {
        margin: 0;
        color: #8b2538;
        font-size: 2rem;
    }

    .filter-bar {
        background: #fff;
        padding: 15px 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .filter-input, .filter-select {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        outline: none;
        font-size: 0.95rem;
    }

    .filter-input:focus, .filter-select:focus {
        border-color: #8b2538;
    }

    .btn-filter {
        background: #8b2538;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.2s;
    }

    .btn-filter:hover {
        background: #6a1b2a;
    }

    .chefs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }

    .chef-card {
        background: #fff;
        border-radius: 16px;
        padding: 25px 20px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.04);
        transition: transform 0.3s, box-shadow 0.3s;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .chef-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(139, 37, 56, 0.15);
    }

    .chef-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 3px solid #fdf5f6;
    }

    .chef-name {
        font-size: 1.2rem;
        color: #333;
        margin: 0 0 10px 0;
        font-weight: bold;
    }

    .chef-region {
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 15px;
    }

    .chef-rating {
        color: #f39c12;
        font-size: 0.9rem;
    }
</style>

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
                $foto = !empty($c['photo']) ? $c['photo'] : 'default.png';
                $regiao = !empty($c['region_operation']) ? $c['region_operation'] : 'Região não informada';
                
                // Lógica matemática para pintura das estrelas do FontAwesome
                $notaMedia = round($c['avg_rating'] * 2) / 2; 
                $estrelasCheias = floor($notaMedia);
                $meiaEstrela = ($notaMedia - $estrelasCheias) > 0 ? 1 : 0;
                $estrelasVazias = 5 - $estrelasCheias - $meiaEstrela;
            ?>
               <a href="../user_profile.php?id=<?php echo $c['id']; ?>&type=chef" class="chef-card">
                    <img src="../assets/uploads/<?php echo htmlspecialchars($foto); ?>" alt="Foto do Chef" class="chef-avatar">
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