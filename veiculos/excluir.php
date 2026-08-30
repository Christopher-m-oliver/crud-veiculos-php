<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    'DELETE FROM veiculos WHERE id = ?'
);

$stmt->execute([$id]);

header('Location: index.php');
exit;