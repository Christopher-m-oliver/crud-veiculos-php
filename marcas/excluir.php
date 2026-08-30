<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

try {

    $stmt = $pdo->prepare(
        'DELETE FROM marcas WHERE id = ?'
    );

    $stmt->execute([$id]);

} catch (PDOException $e) {

    echo 'Não é possível excluir esta marca. Ela pode estar vinculada a um veículo.';
    exit;

}

header('Location: index.php');
exit;