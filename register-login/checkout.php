<?php
session_start();
include('database.php');

$userID = $_SESSION['user']['id'];

$query = "SELECT * FROM cart_items WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

$stmt->close();

$totalPrice = 0;
$orderedItems = []; 

foreach ($cartItems as $item) {
    $totalPrice += ($item['item_price'] * $item['quantity']);

    $orderedItems[] = [
        'item_name' => $item['item_name'],
        'item_price' => $item['item_price'],
        'quantity' => $item['quantity']
    ];
}

function updateCartCount($conn, $userID, $cartCount) {
    $query = "UPDATE users SET cart_count = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $cartCount, $userID);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['place_order'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $email = $_POST['email'];
    $payment_method = $_POST['payment_method'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $pin_code = $_POST['pin_code'];

    $insert_order_query = "INSERT INTO orders (user_id, address, payment_type, ordered_items) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_order_query);
    $stmt->bind_param("isss", $userID, $address, $payment_method, json_encode($orderedItems));
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    $clear_cart_query = "DELETE FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($clear_cart_query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->close();

    $cartCount = 0;
    updateCartCount($conn, $userID, $cartCount);

    $_SESSION['cart_count'] = 0;

    header('Location: order.php?order_id=' . $order_id);
    exit;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .heading {
            text-align: center;
            color: #333;
        }

        .order-summary {
            margin-bottom: 30px;
        }

        .order-summary table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-summary th, .order-summary td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .checkout-form {
            background-color: #f2f2f2;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .inputBox {
            margin-bottom: 15px;
        }

        .inputBox input, .inputBox select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .inputBox select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #fff;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill="%23333" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 12l-6-6H4l6 6 6-6h-2l-6 6z"/></svg>');
            background-size: 12px;
            background-repeat: no-repeat;
            background-position-x: calc(100% - 10px);
            background-position-y: center;
            padding-right: 30px;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .home-button {
            display: block;
            width: 100%;
            margin-top: 20px;
        }

        .home-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .home-button a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="home-button">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
        </div>
    <h1 class="heading">Checkout</h1>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo $item['item_name']; ?></td>
                        <td><?php echo 'P' . number_format($item['item_price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo 'P' . number_format($item['item_price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3">Grand Total</td>
                    <td><?php echo 'P' . number_format($totalPrice, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="checkout-form">
        <h2>Complete Your Order</h2>
        <form action="" method="POST">
            <div class="inputBox">
                <input type="text" name="name" placeholder="Your Name" required>
            </div>
            <div class="inputBox">
                <input type="tel" name="number" placeholder="Your Phone Number" required>
            </div>
            <div class="inputBox">
                <input type="email" name="email" placeholder="Your Email" required>
            </div>
            <div class="inputBox">
                <select name="payment_method" required>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="Gcash">Gcash</option>
                </select>
            </div>
            <div class="inputBox">
                <input type="text" name="address" placeholder="Address" required>
            </div>
            <div class="inputBox">
                <input type="text" name="country" placeholder="Country" required>
            </div>
            <div class="inputBox">
                <input type="text" name="state" placeholder="State" required>
            </div>
            <div class="inputBox">
                <input type="text" name="city" placeholder="City" required>
            </div>
            <div class="inputBox">
                <input type="text" name="zip_code" placeholder="ZIP Code" required>
            </div>

            <input type="submit" name="place_order" value="Place Order" class="btn">
        </form>
    </div>

</div>

</body>
</html>
