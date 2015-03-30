<?php
session_start();
require_once ('../config.php');

$hashed_password = md5($_GET['password']);

try {
    $dbh = new PDO(DSN, DBUSER, DBPASSWORD);
}
catch(PDOException $e) {
    print ('Error:' . $e->getMessage());
    die();
}

$sql = "SELECT * FROM users WHERE email = '" . $_GET['email'] . "' AND password = '" . $hashed_password . "'";
$stmt = $dbh->query($sql);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
  # 買ったリストを見に行って買ったものがあれば削除する
  $user_id =  $result['id'];
  $elems = $dbh->query("SELECT * FROM orders WHERE user_id=$user_id");
  foreach($elems as $elem) {
    $st = $dbh->query("SELECT * FROM order_items WHERE order_id=$elem[id]");
    while($row = $st->fetch(PDO::FETCH_ASSOC)) {
      for($i=0;$i<count($_SESSION['cartitems']);$i++) {
        if($row['item_id'] == $_SESSION['cartitems'][$i]) {
          # 実際の削除部分
          array_splice($_SESSION['cartitems'], $i, 1);
        }
      }
    }
  }
  $_SESSION['user'] = $result;
  header("Location: ./index.php");
}
else {
    header("Location: ./login_register.php");
    $_SESSION['loginregister_error'] = '<font size="5" color="#ff0000"><b>*login faild</b></font>';
}
