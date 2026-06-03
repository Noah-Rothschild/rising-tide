<?php
include('../includes/auth_check.php');
include('../includes/header.php');
include('../config/db.php');

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $_GET['id'], $user_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<p>Product not found or you don't have permission to edit it.</p>";
} else {
?>

<div class="form-container">

    <h1>Edit Product Listing</h1>

    <form class="edit-product-form"
        action="update.php"
        method="POST"
        enctype="multipart/form-data"
    >
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">

        <div class="form-group">
            <label for="title">Product Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>

        <div class="form-group">
            <label for="image">New Image (optional):</label>
            <input type="file" id="image" name="image" accept="image/*">
            <p class="current-image">Current image: <?php echo htmlspecialchars($product['image']); ?></p>
        </div>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>

</div>

<?php
}
include('../includes/footer.php');
?>
