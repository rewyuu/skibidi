<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: ../register-login/login.php");
    exit();
}
include('../register-login/database.php');

$message = '';

function sanitizeInput($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    $delete_product_query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$product_id'");
    if ($delete_product_query) {
        $product_data = mysqli_fetch_assoc($delete_product_query);
        if ($product_data) {
            $image_file = './admin-panel-2/images/' . $product_data['image'];

            if (file_exists($image_file)) {
                unlink($image_file);
            }

            $delete_product = mysqli_query($conn, "DELETE FROM products WHERE id = '$product_id'");
            if ($delete_product) {
                $message = 'Product deleted successfully!';
            } else {
                $message = 'Failed to delete product.';
            }
        } else {
            $message = 'Product not found.';
        }
    } else {
        $message = 'Query failed to execute.';
    }
}

if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    if ($_FILES['image']['name']) {
        $target_dir = "images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . generateUniqueFilename($_FILES["image"]["name"], $target_dir);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES["image"]["size"] > 500000) {
            $message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $message = "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = basename($target_file);

                $add_product_query = "INSERT INTO products (name, price, image, category) VALUES ('$name', '$price', '$image', '$category')";
                $add_product = mysqli_query($conn, $add_product_query);
                if ($add_product) {
                    $message = 'Product added successfully!';
                } else {
                    $message = 'Failed to add product.';
                }
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $message = "Please upload an image.";
    }
}

if (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $message = '';

    $current_product_query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$product_id'");
    if (!$current_product_query) {
        $message = 'Failed to fetch current product data.';
    } else {
        $current_product_data = mysqli_fetch_assoc($current_product_query);

        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $image = $current_product_data['image'];

        if ($_FILES['edit_image']['name']) {
            $target_dir = "images/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . generateUniqueFilename($_FILES["edit_image"]["name"], $target_dir);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["edit_image"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $message = "File is not an image.";
                $uploadOk = 0;
            }

            if ($_FILES["edit_image"]["size"] > 500000) {
                $message = "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                $message = "Sorry, your file was not uploaded.";
            } else {
                if (move_uploaded_file($_FILES["edit_image"]["tmp_name"], $target_file)) {
                    $image = basename($target_file);
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        }

        $update_fields = [];
        if (!empty($name)) {
            $update_fields[] = "name = '$name'";
        }
        if (!empty($price)) {
            $update_fields[] = "price = '$price'";
        }
        if (!empty($category)) {
            $update_fields[] = "category = '$category'";
        }
        $update_fields[] = "image = '$image'";

        $update_query = "UPDATE products SET " . implode(", ", $update_fields) . " WHERE id = '$product_id'";
        $update_product = mysqli_query($conn, $update_query);

        if ($update_product) {
            $message = 'Product updated successfully!';
        } else {
            $message = 'Failed to update product.';
        }
    }
}

if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $update_query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    $update_result = mysqli_stmt_execute($stmt);

    if ($update_result) {
        $message = 'Order status updated successfully!';
    } else {
        $message = 'Failed to update order status: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}


if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['user']['id'];
    $address = sanitizeInput($_POST['address']);
    $payment_type = sanitizeInput($_POST['payment_type']);
    $items = array();

    foreach ($_POST['product'] as $product_id => $quantity) {
        $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$product_id'");
        $product_data = mysqli_fetch_assoc($product_query);

        $items[] = array(
            'id' => $product_id,
            'name' => $product_data['name'],
            'price' => $product_data['price'],
            'quantity' => $quantity
        );
    }

    $items_json = json_encode($items);
    $insert_query = "INSERT INTO orders (user_id, address, payment_type, status, ordered_items) VALUES ('$user_id', '$address', '$payment_type', 'Pending', '$items_json')";
    $insert_result = mysqli_query($conn, $insert_query);

    if ($insert_result) {
        $message = 'Order placed successfully!';
    } else {
        $message = 'Failed to place order.';
    }
}

function generateUniqueFilename($originalFilename, $target_dir) {
    $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
    $basename = pathinfo($originalFilename, PATHINFO_FILENAME);
    $unique_filename = $basename . '_' . uniqid() . '_' . time() . '.' . $extension;
    return $unique_filename;
}

$products = mysqli_query($conn, "SELECT * FROM products");
$orders = mysqli_query($conn, "SELECT * FROM orders");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            background-color: #f0f0f0;
            padding: 10px;
            margin: 0;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        form {
            margin-bottom: 10px;
        }

        form input[type="text"], form input[type="number"], form input[type="file"], form textarea {
            width: calc(100% - 22px);
            padding: 8px;
            margin-bottom: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        img {
            max-width: 50px;
            max-height: 50px;
            vertical-align: middle;
        }

        .actions-form {
            display: inline-block;
        }

        .actions-form input[type="submit"] {
            padding: 6px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .actions-form input[type="submit"]:hover {
            background-color: #e53935;
        }

        .actions-form input[type="submit"][name="edit_product"] {
            background-color: #4CAF50;
            color: white;
        }

        .message {
            background-color: #57bcfd;
            color: white;
            padding: 8px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <form action="../register-login/logout.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
    <h1>Admin Panel</h1>

    <h2>Manage Products</h2>

    <?php if ($message) echo "<div class='message'>$message</div>"; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="file" name="image" placeholder="Image" required>
        <input type="text" name="category" placeholder="Category" required>
        <input type="submit" name="add_product" value="Add Product">
    </form>

    <h2>Products List</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Image</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = mysqli_fetch_assoc($products)) { ?>
            <tr>
                <td><?php echo $product['name']; ?></td>
                <td><?php echo $product['price']; ?></td>
                <td><img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></td>
                <td><?php echo $product['category']; ?></td>
                <td>
                    <form action="" method="post" enctype="multipart/form-data" class="actions-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="text" name="name" placeholder="New Name">
                        <input type="number" step="0.01" name="price" placeholder="New Price">
                        <input type="file" name="edit_image" placeholder="New Image">
                        <input type="text" name="category" placeholder="New Category">
                        <input type="submit" name="edit_product" value="Update">
                    </form>
                    <br>
                    <form action="" method="post" class="actions-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="submit" name="delete_product" value="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Orders</h2>
<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Address</th>
            <th>Payment Type</th>
            <th>Status</th>
            <th>Ordered Items</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($order = mysqli_fetch_assoc($orders)) { ?>
        <tr>
            <td><?php echo $order['user_id']; ?></td>
            <td><?php echo $order['address']; ?></td>
            <td><?php echo $order['payment_type']; ?></td>
            <td>
            <form action="" method="post" class="actions-form">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <select name="status">
                <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Order Accepted" <?php if ($order['status'] == 'Order Accepted') echo 'selected'; ?>>Order Accepted</option>
                <option value="Order Declined" <?php if ($order['status'] == 'Order Declined') echo 'selected'; ?>>Order Declined</option>
                <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
            </select>
            <input type="submit" name="update_order" value="Update">
        </form> 
            </td>
            <td>
                <?php
                    $ordered_items = json_decode($order['ordered_items'], true);
                    foreach ($ordered_items as $item) {
                        echo "{$item['item_name']} - {$item['quantity']} <br>";
                    }
                ?>
            </td>
            <td><?php echo $order['created_at']; ?></td>
            <td>
                <form action="" method="post" class="actions-form">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <input type="submit" name="delete_order" value="Delete" onclick="return confirm('Are you sure you want to delete this order?');">
                </form>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</body>
</html>
