<?php
include('../includes/auth_check.php');
include('../includes/header.php');
?>

<div class="form-container">

    <h1>Create Product Listing</h1>

    <form action="store.php" method="POST" enctype="multipart/form-data">

        <input type="text" name="title" placeholder="Product Title" required>

        <textarea name="description" placeholder="Product Description" required></textarea>

        <input type="number" step="0.01" name="price" placeholder="Price" required>

        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Create Listing</button>

    </form>

</div>

<?php include('../includes/footer.php'); ?>