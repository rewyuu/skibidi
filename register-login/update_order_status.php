<?php
session_start();
include('database.php');

if (!isset($_SESSION["admin"])) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderID = $_POST['order_id'];
    $status = $_POST['status'];

    $update_query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $orderID);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
} else {
    header('Location: admin.php');
    exit;
}
?>
