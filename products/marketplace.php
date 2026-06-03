<?php
include('../includes/header.php');
include('../config/db.php');

$selectedCategory = $_GET['category'] ?? 'All';
$searchTerm = trim($_GET['search'] ?? '');
$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;

$categoryResult = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$allCategories = [
    ['id' => 'All', 'name' => 'All'],
];
while ($category = $categoryResult->fetch_assoc()) {
    $allCategories[] = $category;
}

$query = "
    SELECT 
        p.*,
        COALESCE(u.username, u.name, 'Unknown Seller') AS seller_name,
        COALESCE(c.name, 'Uncategorized') AS category_name
    FROM products p
    LEFT JOIN users u ON p.seller_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
";

$result = $conn->query($query);

$products = [];
while ($product = $result->fetch_assoc()) {
    $products[] = $product;
}

function buildQuery(array $overrides = []): string {
    $params = array_merge($_GET, $overrides);

    if (isset($params['category']) && $params['category'] === 'All') {
        unset($params['category']);
    }

    return http_build_query($params);
}

$baseQuery = buildQuery(['category' => 'All']);
$baseQueryPrefix = $baseQuery === '' ? '?' : '?' . $baseQuery . '&';

function matchesFilters(array $product): bool {
    global $selectedCategory, $searchTerm, $minPrice, $maxPrice;

    if ($selectedCategory !== 'All' && (string) $product['category_id'] !== (string) $selectedCategory) {
        return false;
    }

    if ($searchTerm !== '') {
        $text = strtolower($product['name'] . ' ' . ($product['description'] ?? '') . ' ' . $product['seller_name'] . ' ' . $product['category_name']);
        if (strpos($text, strtolower($searchTerm)) === false) {
            return false;
        }
    }

    $price = floatval($product['price']);

    if ($minPrice !== null && $price < $minPrice) {
        return false;
    }

    if ($maxPrice !== null && $price > $maxPrice) {
        return false;
    }

    return true;
}

$filteredProducts = array_filter($products, 'matchesFilters');
?>

<div class="marketplace-container">

    <div class="marketplace-header">
        <div>
            <h1>Marketplace</h1>
            <p>Browse products from the Rising Tide community.</p>
        </div>

        <div class="marketplace-category-bar">
            <div class="category-pill-group">
                <?php foreach ($allCategories as $category): ?>
                    <a
                        class="category-pill<?php echo (string) $selectedCategory === (string) $category['id'] ? ' active' : ''; ?>"
                        href="?<?php echo buildQuery(['category' => $category['id']]); ?>"
                    >
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="category-dropdown">
                <label for="category-select">Category</label>
                <select id="category-select" onchange="window.location.href='<?php echo $baseQueryPrefix; ?>category=' + encodeURIComponent(this.value);">
                    <?php foreach ($allCategories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo (string) $selectedCategory === (string) $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

    </div>

    <div class="marketplace-grid-wrapper">

        <aside class="marketplace-filters">
            <h3>Filter products</h3>

            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="search">Search</label>
                    <input id="search" name="search" type="text" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search products, sellers">
                </div>

                <div class="filter-group">
                    <label for="sidebar-category">Category</label>
                    <select id="sidebar-category" name="category">
                        <?php foreach ($allCategories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo (string) $selectedCategory === (string) $category['id'] ? 'selected' : ''; ?> >
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Price range</label>
                    <div class="price-row">
                        <input name="min_price" type="number" step="0.01" min="0" placeholder="Min" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                        <input name="max_price" type="number" step="0.01" min="0" placeholder="Max" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply filters</button>
                    <a class="btn btn-secondary" href="/rising-tide/products/marketplace.php">Reset</a>
                </div>
            </form>
        </aside>

        <div class="marketplace-grid">
            <?php if (count($filteredProducts) === 0): ?>
                <div class="marketplace-empty">
                    <h2>No products found</h2>
                    <p>Try adjusting your category or filters to see more items.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($filteredProducts as $product): ?>
                <div class="marketplace-card" data-category="<?php echo htmlspecialchars($product['category_name']); ?>" data-price="<?php echo htmlspecialchars($product['price']); ?>" data-seller="<?php echo htmlspecialchars($product['seller_name']); ?>">
                    <img 
                        class="marketplace-image"
                        src="../assets/images/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                    >

                    <div class="marketplace-content">
                        <div class="marketplace-card-top">
                            <span class="marketplace-tag"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <span class="marketplace-date">R <?php echo number_format($product['price'], 2); ?></span>
                        </div>

                        <h2><?php echo htmlspecialchars($product['name']); ?></h2>

                        <p class="marketplace-seller">
                            Seller: <?php echo htmlspecialchars($product['seller_name']); ?>
                        </p>

                        <a class="btn btn-primary" href="view.php?id=<?php echo $product['id']; ?>">
                            View Product
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>