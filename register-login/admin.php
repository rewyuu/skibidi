<?php
session_start();
include('database.php');

if (!isset($_SESSION["admin"])) {
    header("Location: admin.php");
    exit;
}

// Fetch all orders
$query = "SELECT * FROM orders";
$result = $conn->query($query);

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <h1>Admin Dashboard - Orders</h1>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Address</th>
                <th>Payment Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['user_id']; ?></td>
                    <td><?php echo $order['address']; ?></td>
                    <td><?php echo $order['payment_type']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td>
                        <form action="update_order_status.php" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status">
                                <option value="Pending">Pending</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
