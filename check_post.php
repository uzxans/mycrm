<?php
require_once __DIR__.'/boot.php';
if (check_post()) {
    header('Location: ' . root());
    die;
}
?>