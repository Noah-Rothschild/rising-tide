<?php
include('../includes/auth_check.php');
include('../includes/header.php');
include('../config/db.php');

$categories = mysqli_query($conn, "SELECT id, name FROM categories");
?>

<div class="form-container">

    <h1>Create Product Listing</h1>
    <p>Fill in the details below to add a new item to your store.</p>

    <form action="store.php" method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label for="name">Product Name</label>
            <input id="name" type="text" name="name" placeholder="Enter product name" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Describe the product" required></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input id="price" type="number" step="0.01" name="price" placeholder="0.00" required>
        </div>

        <div class="form-group">
            <label for="stock">Stock</label>
            <input id="stock" type="number" name="stock" value="1" min="0">
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Product Image</label>
            <input id="image" type="file" name="image" accept="image/*">
        </div>

        <button type="submit">Create Product</button>

    </form>

</div>

<?php include('../includes/footer.php'); ?>