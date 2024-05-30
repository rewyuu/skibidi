<?php
session_start();
include('database.php');

$userID = $_SESSION['user']['id'];

// Fetch user details from the database
$query = "SELECT address, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userAddress = $user['address'];
$userEmail = $user['email'];
$userPhone = $user['phone'];
$stmt->close();

// Fetch cart items for the current user
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

// Calculate total price and prepare ordered items for insertion into orders table
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

// Function to update cart count for the user
function updateCartCount($conn, $userID, $cartCount) {
    $query = "UPDATE users SET cart_count = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $cartCount, $userID);
    $stmt->execute();
    $stmt->close();
}

// Process order placement
if (isset($_POST['place_order'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $payment_method = $_POST['payment_method'];

    // Determine phone number based on user selection
    if (isset($_POST['use_different_number'])) {
        $phone = $_POST['new_number'];
    } else {
        $phone = $userPhone;
    }

    // Determine address based on user selection
    if ($_POST['address_option'] === 'new') {
        $address = $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['zipcode'] . ', ' . $_POST['region'];
    } else {
        $address = $userAddress;
    }

    // Update phone number in the users table if it's changed
    if ($phone !== $userPhone) {
        $update_phone_query = "UPDATE users SET phone = ? WHERE id = ?";
        $stmt = $conn->prepare($update_phone_query);
        $stmt->bind_param("si", $phone, $userID);
        $stmt->execute();
        $stmt->close();
    }

    // Insert order details into orders table
    $insert_order_query = "INSERT INTO orders (user_id, address, payment_type, ordered_items) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_order_query);
    $stmt->bind_param("isss", $userID, $address, $payment_method, json_encode($orderedItems));
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Clear cart items for the user
    $clear_cart_query = "DELETE FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($clear_cart_query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->close();

    // Update cart count to 0 for the user
    $cartCount = 0;
    updateCartCount($conn, $userID, $cartCount);

    // Reset cart count in session
    $_SESSION['cart_count'] = 0;

    // Redirect to order confirmation page
    header('Location: order.php?order_id=' . $order_id);
    exit;
}

// Close database connection
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
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo 'P' . number_format($item['item_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
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
            <!-- User name -->
            <div class="inputBox">
                <input type="text" name="name" placeholder="Your Name" required>
            </div>

            <!-- User email -->
            <div class="inputBox">
                <input type="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" placeholder="Your Email" required>
            </div>

            <!-- Phone number options -->
            <div class="inputBox">
                <label><input type="checkbox" name="use_different_number" id="use_different_number"> Use Different Phone Number</label>
            </div>
            <div class="inputBox" id="new_number_box" style="display: none;">
                <input type="tel" name="new_number" placeholder="New Phone Number">
            </div>

            <!-- Address options -->
            <div class="inputBox">
                <label><input type="radio" name="address_option" value="saved" checked> Use Saved Address</label>
                <label><input type="radio" name="address_option" value="new"> Enter New Address</label>
            </div>
            <div class="inputBox" id="saved-address">
                <input type="text" name="address" value="<?php echo htmlspecialchars($userAddress); ?>" placeholder="Address" readonly>
            </div>
            <div class="inputBox" id="new-address" style="display: none;">
                <input type="text" name="street" placeholder="Street" required>
                <input type="text" name="city" placeholder="City" required>
                <input type="text" name="zipcode" placeholder="Zip Code" required>
                <input type="text" name="region" placeholder="Region" required>
            </div>

            <!-- Payment method -->
            <div class="inputBox">
                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="Gcash">Gcash</option>
                </select>
            </div>

            <!-- Place order button -->
            <input type="submit" name="place_order" value="Place Order" class="btn">
        </form>
    </div>
</div>

<!-- JavaScript to toggle phone number input -->
<script>
    document.getElementById('use_different_number').addEventListener('change', function() {
        var newNumberBox = document.getElementById('new_number_box');
        newNumberBox.style.display = this.checked ? 'block' : 'none';
    });
    
    document.querySelectorAll('input[name="address_option"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var savedAddressBox = document.getElementById('saved-address');
            var newAddressBox = document.getElementById('new-address');
            
            if (this.value === 'saved') {
                savedAddressBox.style.display = 'block';
                newAddressBox.style.display = 'none';
            } else if (this.value === 'new') {
                savedAddressBox.style.display = 'none';
                newAddressBox.style.display = 'block';
            }
        });
    });
</script>

</body>
</html>
