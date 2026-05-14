<?php
include('../includes/header.php');
include('../config/db.php');

$query = "
    SELECT 
        products.*,
        users.username
    FROM products
    JOIN users
        ON products.user_id = users.id
    ORDER BY products.created_at DESC
";

$result = $conn->query($query);

function inferCategory(array $product): string {
    if (!empty($product['category'])) {
        return $product['category'];
    }

    $text = strtolower($product['title'] . ' ' . ($product['description'] ?? ''));

    $mappings = [
        'Clothing' => ['shirt', 'jacket', 'hoodie', 'jeans', 'dress', 'pants', 'skirt', 'shoes', 'cap'],
        'Electronics' => ['phone', 'charger', 'headphone', 'camera', 'speaker', 'laptop', 'watch', 'tablet'],
        'Home' => ['mug', 'lamp', 'candle', 'chair', 'table', 'sofa', 'pillow', 'blanket', 'decor'],
        'Beauty' => ['soap', 'cream', 'lip', 'perfume', 'makeup', 'skincare', 'serum', 'lotion'],
        'Books' => ['book', 'novel', 'journal', 'guide', 'manual', 'story', 'storybook'],
        'Art' => ['print', 'poster', 'painting', 'art', 'sculpture', 'drawing', 'canvas'],
    ];

    foreach ($mappings as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return $category;
            }
        }
    }

    return 'General';
}

$selectedCategory = $_GET['category'] ?? 'All';
$searchTerm = trim($_GET['search'] ?? '');
$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;

$products = [];
$allCategories = ['All'];

while ($product = $result->fetch_assoc()) {
    $product['category'] = inferCategory($product);

    if (!in_array($product['category'], $allCategories, true)) {
        $allCategories[] = $product['category'];
    }

    $products[] = $product;
}

function buildQuery(array $overrides = []): string {
    $params = array_merge($_GET, $overrides);
    return http_build_query($params);
}

function matchesFilters(array $product): bool {
    global $selectedCategory, $searchTerm, $minPrice, $maxPrice;

    if ($selectedCategory !== 'All' && $product['category'] !== $selectedCategory) {
        return false;
    }

    if ($searchTerm !== '') {
        $text = strtolower($product['title'] . ' ' . ($product['description'] ?? '') . ' ' . $product['username'] . ' ' . $product['category']);
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
                        class="category-pill<?php echo $selectedCategory === $category ? ' active' : ''; ?>"
                        href="?<?php echo buildQuery(['category' => $category]); ?>"
                    >
                        <?php echo htmlspecialchars($category); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="category-dropdown">
                <label for="category-select">Category</label>
                <select id="category-select" onchange="window.location.href='?<?php echo buildQuery(['category' => '']); ?>&category=' + encodeURIComponent(this.value);">
                    <?php foreach ($allCategories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $selectedCategory === $category ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
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
                            <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $selectedCategory === $category ? 'selected' : ''; ?> >
                                <?php echo htmlspecialchars($category); ?>
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
                <div class="marketplace-card" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-price="<?php echo htmlspecialchars($product['price']); ?>" data-seller="<?php echo htmlspecialchars($product['username']); ?>">
                    <img 
                        class="marketplace-image"
                        src="../assets/images/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['title']); ?>"
                    >

                    <div class="marketplace-content">
                        <div class="marketplace-card-top">
                            <span class="marketplace-tag"><?php echo htmlspecialchars($product['category']); ?></span>
                            <span class="marketplace-date">R <?php echo number_format($product['price'], 2); ?></span>
                        </div>

                        <h2><?php echo htmlspecialchars($product['title']); ?></h2>

                        <p class="marketplace-seller">
                            Seller: <?php echo htmlspecialchars($product['username']); ?>
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