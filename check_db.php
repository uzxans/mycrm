<?php
require_once __DIR__.'/boot.php';
try {
    pdo()->query('SELECT * FROM `db`');
} catch (PDOException $e) {
    flash("Ошибка базы данных, пожалуйста обратитесь к администратору", 'danger');
    header('Location: ' . root());
    die;
}
?>