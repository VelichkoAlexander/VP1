<?php
require_once 'src/function.php';


$userName = trim($_POST['name']);
$userPhone = trim($_POST['phone']);
$userEmail = trim($_POST['email']);
$userAddress = 'Улица: ' . trim($_POST['street']) . ' Дом: ' . trim($_POST['home']) . ' Корпус: '
    . trim($_POST['part']) . ' Квартира: ' . trim($_POST['appt']) . ' Этаж: ' . trim($_POST['floor']);
$comment = trim($_POST['comment']);
$payment = $_POST['payment'] || false;
$callback = $_POST['callback'] || false;
$error = '';
$userId = '';
if (empty($userName) && isset($userName)) {
    $error = 'Введите имя';
} elseif (empty($userPhone) && isset($userPhone)) {
    $error = 'Введите Телефон';
} elseif (empty($userEmail) && isset($userEmail)) {
    $error = 'Введите Email';
}
if ($error !== '') {
    errorMessage($error);
}
$db = connect();
$checkEmail = 'SELECT id FROM users WHERE email = ?';

try {
    $query = $db->prepare($checkEmail);
    $query->execute([$userEmail]);
} catch (PDOException $e) {
    errorMessage($e->getMessage());
}
if ($userId = $query->fetchColumn()) {


} else {
    $sql = 'INSERT INTO users(email, name, phone) VALUE(?,?,?)';
    try {
        $query = $db->prepare($sql);
        $query->execute([$userEmail, $userName, $userPhone]);
    } catch (PDOException $e) {
        errorMessage($e->getMessage());
    }
    $userId = $db->lastInsertId();
}
createOrder($userId, $userAddress, $comment, $payment, $callback);