<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: ../register-login/login.php");
    exit();
}
include('../register-login/database.php');

$message = '';

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $target_dir = "images/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "File is not an image.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        $message = "Sorry, file already exists.";
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
            $image = basename($_FILES["image"]["name"]);

            $insert_product = mysqli_query($conn, "INSERT INTO products (name, price, image, category) VALUES ('$name', '$price', '$image', '$category')");
            if ($insert_product) {
                $message = 'Product added successfully!';
            } else {
                $message = 'Failed to add product.';
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    }
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
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f0f0f0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        form input[type="text"], form input[type="number"], form input[type="file"], form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
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
            padding: 8px 12px;
        }

        .message {
            background-color: #f44336;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
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
            </tr>
        </thead>
        <tbody>
            <?php while ($product = mysqli_fetch_assoc($products)) { ?>
            <tr>
                <td><?php echo $product['name']; ?></td>
                <td><?php echo $product['price']; ?></td>
                <td><img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></td>
                <td><?php echo $product['category']; ?></td>
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
            </tr>
        </thead>
        <tbody>
            <?php while ($order = mysqli_fetch_assoc($orders)) { ?>
            <tr>
                <td><?php echo $order['user_id']; ?></td>
                <td><?php echo $order['address']; ?></td>
                <td><?php echo $order['payment_type']; ?></td>
                <td><?php echo $order['status']; ?></td>
                <td><?php echo json_encode($order['ordered_items']); ?></td>
                <td><?php echo $order['created_at']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
