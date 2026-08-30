<?php

require_once 'includes/auth.php';
require_once 'config/database.php';

$totalVeiculos = $pdo
    ->query('SELECT COUNT(*) FROM veiculos')
    ->fetchColumn();

$totalMarcas = $pdo
    ->query('SELECT COUNT(*) FROM marcas')
    ->fetchColumn();

$titulo = 'Início';

require_once 'includes/header.php';
?>

<h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?>!</h1>

<p>
    Sistema de gerenciamento de veículos e marcas.
</p>

<div class="dashboard">

    <div class="card">
        <h2><?= $totalVeiculos ?></h2>
        <p>Veículos cadastrados</p>

        <a class="btn" href="/veiculos/index.php">
            Gerenciar veículos
        </a>
    </div>

    <div class="card">
        <h2><?= $totalMarcas ?></h2>
        <p>Marcas cadastradas</p>

        <a class="btn" href="/marcas/index.php">
            Gerenciar marcas
        </a>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>