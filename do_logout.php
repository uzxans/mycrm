<?php

require_once __DIR__.'/boot.php';

$_SESSION['user_id'] = null;
setcookie('remember_token', '', time() - 3600, '/');
header('Location: '. root(). '/');
?>