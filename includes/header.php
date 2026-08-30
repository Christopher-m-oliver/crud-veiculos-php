<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $titulo ?? 'Sistema de Veículos' ?></title>

    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

<header class="topbar">

    <div class="container nav">

        <a class="logo" href="/index.php">
            MotorHUB
        </a>

        <nav>
            <a href="/index.php">Início</a>
            <a href="/veiculos/index.php">Veículos</a>
            <a href="/marcas/index.php">Marcas</a>
            <a href="/logout.php">Sair</a>
        </nav>

    </div>

</header>

<main class="container">