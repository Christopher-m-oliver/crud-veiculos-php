<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $marca = trim($_POST['marca'] ?? '');

    if ($marca === '') {

        $erro = 'Informe o nome da marca.';

    } else {

        try {

            $stmt = $pdo->prepare(
                'INSERT INTO marcas (marca) VALUES (?)'
            );

            $stmt->execute([$marca]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $erro = 'Não foi possível cadastrar a marca.';
        }
    }
}

$titulo = 'Nova Marca';

require_once '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Nova Marca</h1>
        <p>Cadastre uma nova marca de veículo.</p>
    </div>
</div>

<?php if ($erro): ?>

    <div class="error">
        <?= htmlspecialchars($erro) ?>
    </div>

<?php endif; ?>

<form method="POST">

    <label for="marca">Marca</label>

    <input
        type="text"
        id="marca"
        name="marca"
        value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>"
        required
    >

    <button class="btn" type="submit">
        Cadastrar
    </button>

    <a class="btn btn-secondary" href="index.php">
        Cancelar
    </a>

</form>

<?php require_once '../includes/footer.php'; ?>