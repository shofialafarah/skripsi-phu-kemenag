<?php
$password_plain = '123';
$password_hashed = password_hash($password_plain, PASSWORD_BCRYPT);

echo "Password Hashed dari 123 adalah: " . $password_hashed;
?>
