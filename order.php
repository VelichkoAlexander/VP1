<?php
require_once 'db_connect.php';
//var_dump($_POST);

$userName = addslashes(trim($_POST['name']));
$userPhone = addslashes(trim($_POST['phone']));
$userEmail = addslashes(trim($_POST['email']));
$userAddress = 'Улица: ' . trim($_POST['street']) . ' Дом: ' . trim($_POST['home']) . ' Корпус: '
    . trim($_POST['part']) . ' Квартира: ' . trim($_POST['appt']) . ' Этаж: ' . trim($_POST['floor']);
$comment = addslashes(trim($_POST['comment']));
$payment = addslashes(trim($_POST['payment'])) || false;
$callback = addslashes(trim($_POST['callback'])) || false;
$error = '';


if (empty($userName) && isset($userName)) {
    $error = 'Введите имя';
} elseif (empty($userPhone) && isset($userPhone)) {
    $error = 'Введите Телефон';
} elseif (empty($userEmail) && isset($userEmail)) {
    $error = 'Введите Email';
}

if ($error !== '') {
    echo $error;
    die;
}
$checkEmail = 'SELECT id FROM users WHERE email = ?';
$query = $pdo->prepare($checkEmail);
$query->execute([$userEmail]);
if ($userId = $query->fetchColumn()) {

    insertOrder($userId, $userAddress, $comment, $payment, $callback, $pdo);

} else {
    $sql = 'INSERT INTO users(email, name, phone) VALUE(?,?,?)';
    $query = $pdo->prepare($sql);
    if (!$query) {
        print_r($query->errorInfo());
    }
    $query->execute([$userEmail, $userName, $userPhone]);
    $userId = $pdo->lastInsertId();
    insertOrder($userId, $userAddress, $comment, $payment, $callback, $pdo);

}

//
//print_r($query->errorInfo());


function insertOrder($user_id, $address, $comment, $no_call, $card, &$pdo)
{
    $order = 'INSERT INTO orders (user_id, address, comment, card, no_call) VALUE(?,?,?,?,?)';
    $query = $pdo->prepare($order);
    $query->execute([$user_id, $address, $comment, $no_call, $card]);
    if (!$query) {
        print_r($query->errorInfo());
        die;
    }
    $orderId = $pdo->lastInsertId();
    sendOrderMail($orderId, $pdo);
}


function sendOrderMail($orderId, &$pdo)
{
    $order = 'SELECT orders.address, orders.user_id, users.email
 FROM orders LEFT JOIN users ON orders.user_id = users.id WHERE orders.id = ?';
    $query = $pdo->prepare($order);
    $query->execute([$orderId]);
    if (($query->errorInfo()[0]) != 00000) {
        print_r($query->errorInfo()[2]);
        die;
    }
    $data = $query->fetch(PDO::FETCH_ASSOC);
    $orderCounting = countOrders($data['user_id'],$pdo);
    $orderCountingMessage = '';
    if($orderCounting > 1) {
        $orderCountingMessage.='Спасибо! Это уже '.$orderCounting.' заказ';
    } else {
        $orderCountingMessage.='Спасибо - это ваш первый заказ';
    }
    $message = "
<html>
<head>
<title>Order email</title>
</head>
<body>
<p>№{$orderId}</p>
<p>Ваш заказ будет доставлен по адресу: {$data["address"]}</p>
<p>DarkBeefBurger за 500 рублей, 1 шт,</p>
<p>{$orderCountingMessage}</p>
</body>
</html>
";
    echo $message;
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <webmaster@example.com>' . "\r\n";
    mail($data['email'], "Order", $message);
}

function countOrders($userId, &$pdo)
{
    $order = 'SELECT count(id) FROM orders WHERE user_id = ?';
    $query = $pdo->prepare($order);
    $query->execute([$userId]);
    if (($query->errorInfo()[0]) != 00000) {
        print_r($query->errorInfo()[2]);
        die;
    }
    $data = $query->fetch(PDO::FETCH_ASSOC);
    return $data["count(id)"];
}