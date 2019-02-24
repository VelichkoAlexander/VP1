<?php
require_once './src/function.php';
?>

<h1> Админ панель</h1>

<?php
$users = 'SELECT * FROM users';
$db = connect();
try {
    $query = $db->prepare($users);
    $query->execute();
} catch (PDOException $e) {
    errorMessage($e->getMessage());
}
$data = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Users</h2>
<table>
    <thead>
    <tr>
        <th>id</th>
        <th>email</th>
        <th>name</th>
        <th>phone</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($data as $user) {
        echo '<tr>';
        echo '<td>' . $user['id'] . '</td>';
        echo '<td>' . $user['email'] . '</td>';
        echo '<td>' . $user['name'] . '</td>';
        echo '<td>' . $user['phone'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
<?php
$db = connect();

$orders = 'SELECT * FROM orders';
try {
    $query = $db->prepare($orders);
    $query->execute();
} catch (PDOException $e) {
    errorMessage($e->getMessage());
}
$data = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Orders</h2>
<table>
    <thead>
    <tr>
        <th>id</th>
        <th>user_id</th>
        <th>address</th>
        <th>comment</th>
        <th>card</th>
        <th>no_call</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($data as $order) {
        echo '<tr>';
        echo '<td>' . $order['id'] . '</td>';
        echo '<td>' . $order['user_id'] . '</td>';
        echo '<td>' . $order['address'] . '</td>';
        echo '<td>' . $order['comment'] . '</td>';
        echo '<td>' . $order['card'] . '</td>';
        echo '<td>' . $order['no_call'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
