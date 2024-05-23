<?php
include('../register-login/database.php');

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Edit Product</h2>
    <form action="update_product.php" method="post">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="text" name="name" value="<?php echo $product['name']; ?>" placeholder="Product Name" required>
        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" placeholder="Price" required>
        <input type="text" name="image" value="<?php echo $product['image']; ?>" placeholder="Image URL" required>
        <input type="text" name="category" value="<?php echo $product['category']; ?>" placeholder="Category" required>
        <textarea name="description" placeholder="Description" required><?php echo $product['description']; ?></textarea>
        <input type="submit" name="update_product" value="Update Product">
    </form>
</body>
</html>
