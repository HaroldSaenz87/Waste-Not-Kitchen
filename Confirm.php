<?php
session_start();
include("config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? 'customer';

if($user_type === 'customer'){
      

    $countQuery = "SELECT SUM(quantity) AS total_quantity FROM reservations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $countQuery);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $resut = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($resut);
    $totalQuant = $row['total_quantity'] ?? 0;

    $orderQuery = "INSERT INTO orders (user_id, amt, order_type) VALUE (?, ?, 'customer')";
    $stmt = mysqli_prepare($conn, $orderQuery);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $totalQuant);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    $itemQuery = "INSERT INTO order_items (order_id, plate_id, amt)
                SELECT ?, plate_id, quantity
                FROM reservations
                WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $itemQuery);
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmt);

    $deleteQuery = "DELETE FROM reservations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    header("Location: Customer.php?checkout=success");
    exit();
} elseif($user_type === 'donor'){
    $countQuery = "SELECT SUM(amt) AS total_quantity FROM donations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $countQuery);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $totalQuant = $row['total_quantity'] ?? 0;

    $orderQuery = "INSERT INTO orders (user_id, amt, order_type) VALUES (?, ?, 'donation')";
    $stmt = mysqli_prepare($conn, $orderQuery);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $totalQuant);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);


    $itemQuery = "INSERT INTO order_items (order_id, plate_id, amt)
                  SELECT ?, plate_id, amt
                  FROM donations
                  WHERE user_id = ?";
    
    $stmt = mysqli_prepare($conn, $itemQuery);
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmt);

    $deleteQuery = "DELETE FROM donations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    header("Location: Doner.php?checkout=success");
    exit();
}


?>