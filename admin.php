<?php
require './vendor/autoload.php';
require_once './src/function.php';

$loader = new \Twig\Loader\FilesystemLoader('Views');
$twig = new \Twig\Environment($loader);

$users = 'SELECT * FROM users';
$db = connect();
try {
    $query = $db->prepare($users);
    $query->execute();
} catch (PDOException $e) {
    errorMessage($e->getMessage());
}
$usersData = $query->fetchAll(PDO::FETCH_ASSOC);

$orders = 'SELECT * FROM orders';
try {
    $query = $db->prepare($orders);
    $query->execute();
} catch (PDOException $e) {
    errorMessage($e->getMessage());
}
$ordersData = $query->fetchAll(PDO::FETCH_ASSOC);

echo $twig->render('Dashboard.twig', ['usersData' => $usersData, 'ordersData' => $ordersData]);