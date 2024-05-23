<?php
include('../register-login/database.php');

if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $update_query = "UPDATE products SET name='$name', price='$price', image='$image', category='$category', description='$description' WHERE id=$product_id";
    $result = mysqli_query($conn, $update_query);

    if ($result) {
        header("Location: admin.php"); 
    } else {
        echo "Failed to update product.";
    }
}
?>
