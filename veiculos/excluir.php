<?php

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/upload.php';
require_once '../includes/flash.php';
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!csrfValido()) {
    flashDefinir('erro', 'Requisição inválida. Tente novamente.');
    header('Location: index.php');
    exit;
}

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$id) {
    flashDefinir('erro', 'Veículo inválido.');
    header('Location: index.php');
    exit;
}

try {

    $stmt = $pdo->prepare('SELECT foto FROM veiculos WHERE id = ?');
    $stmt->execute([$id]);

    $foto = $stmt->fetchColumn();

    $stmt = $pdo->prepare('DELETE FROM veiculos WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {

        removerFoto(is_string($foto) ? $foto : null);

        flashDefinir('sucesso', 'Veículo excluído com sucesso.');

    } else {

        flashDefinir('erro', 'Veículo não encontrado.');
    }

} catch (PDOException $e) {

    error_log('Erro ao excluir veículo: ' . $e->getMessage());
    flashDefinir('erro', 'Não foi possível excluir o veículo.');
}

header('Location: index.php');
exit;
