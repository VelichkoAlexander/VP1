<?php
require './vendor/autoload.php';
function connect()
{
    $host = 'localhost';
    $dbname = 'wp1';
    $user = 'root';
    $pass = '';
    try {
        return new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

    } catch (PDOException $e) {
        echo $e->getMessage();
        die;
    }
}

function createOrder($user_id, $address, $comment, $no_call, $card)
{
    $db = connect();
    $order = 'INSERT INTO orders (user_id, address, comment, card, no_call) VALUE(?,?,?,?,?)';
    $query = $db->prepare($order);
    $query->execute([$user_id, $address, $comment, $no_call, $card]);
    if (!$query) {
        print_r($query->errorInfo());
        die;
    }
    $orderId = $db->lastInsertId();
    sendOrderMail($orderId, $db);
}


function sendOrderMail($orderId)
{
    $db = connect();
    $order = 'SELECT orders.address, orders.user_id, users.email
 FROM orders LEFT JOIN users ON orders.user_id = users.id WHERE orders.id = ?';
    try {
        $query = $db->prepare($order);
        $query->execute([$orderId]);
    } catch (PDOException $e) {
        errorMessage($e->getMessage());
    }
    $data = $query->fetch(PDO::FETCH_ASSOC);
    $orderCounting = countOrders($data['user_id'], $db);
    $orderCountingMessage = '';
    if ($orderCounting > 1) {
        $orderCountingMessage .= 'Спасибо! Это уже ' . $orderCounting . ' заказ';
    } else {
        $orderCountingMessage .= 'Спасибо - это ваш первый заказ';
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
    try {
        $transport = (new Swift_SmtpTransport(' smtp.mail.ru', 465, 'ssl'))
            ->setUsername('')
            ->setPassword('');
// Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);
// Create a message
        $message = (new Swift_Message('Order'))
            ->setFrom(['loftschool.phpmailtest@mail.ru' => 'loftschool.phpmailtest@mail.ru'])
            ->setTo([$data['email']])
            ->setBody($message);
// Send the message
        $result = $mailer->send($message);
        var_dump(['res' => $result]);
    } catch (Exception $e) {
        json_encode(['error' => $e->getMessage()]);
    }
    echo json_encode(['success' => true]);
    die;

//    $headers = "MIME-Version: 1.0" . "\r\n";
//    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//    $headers .= 'From: <webmaster@example.com>' . "\r\n";
//    if (mail($data['email'], "Order", $message)) {
//        echo json_encode(['success' => true]);
//        die;
//    };
}

function countOrders($userId, $db)
{
    $db = connect();
    $order = 'SELECT count(id) FROM orders WHERE user_id = ?';
    try {
        $query = $db->prepare($order);
        $query->execute([$userId]);
    } catch (PDOException $e) {
        errorMessage($e->getMessage());
    }
    $data = $query->fetch(PDO::FETCH_ASSOC);
    return $data["count(id)"];
}

function errorMessage($error)
{
    echo json_encode(['error' => $error]);
    die;
}
