<?php
include('../includes/auth_check.php');
include('../includes/header.php');
?>

<div class="dashboard-container">

    <div class="dashboard-header">
        <h1>My Product Listings</h1>
        <p>Manage your products from here.</p>
    </div>

    <div class="dashboard-grid">

        <?php
        include('../config/db.php');

        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='dashboard-card'>";

                echo "<img 
                        class='product-image'
                        src='../assets/images/uploads/" . htmlspecialchars($row['image']) . "'
                        alt='" . htmlspecialchars($row['title']) . "'
                    >";

                echo "<div class='dashboard-card-content'>";

                echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";

                echo "<p>" . htmlspecialchars($row['description']) . "</p>";

                echo "<p class='product-price'>
                        R" . number_format($row['price'], 2) . "
                    </p>";

                echo "<div class='card-buttons'>";

                echo "<a 
                        class='btn btn-primary'
                        href='../products/edit.php?id=" . $row['id'] . "'
                    >
                        Edit
                    </a>";

                echo "<a 
                        class='btn btn-danger'
                        href='../products/delete.php?id=" . $row['id'] . "'
                        onclick='return confirm(\"Delete this product?\")'
                    >
                        Delete
                    </a>";

                echo "</div>";

                echo "</div>";

                echo "</div>";
            }
        } else {
            echo "<p>You have no product listings.</p>";
        }
        ?>

    </div>

</div>

<?php include('../includes/footer.php'); ?>