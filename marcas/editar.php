<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id, marca FROM marcas WHERE id = ?'
);

$stmt->execute([$id]);

$marca = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$marca) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['marca'] ?? '');

    if ($nome === '') {

        $erro = 'Informe o nome da marca.';

    } else {

        try {

            $stmt = $pdo->prepare(
                'UPDATE marcas SET marca = ? WHERE id = ?'
            );

            $stmt->execute([
                $nome,
                $id
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $erro = 'Não foi possível atualizar a marca.';
        }
    }
}

$titulo = 'Editar Marca';

require_once '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Editar Marca</h1>
        <p>Altere os dados da marca.</p>
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
        value="<?= htmlspecialchars($_POST['marca'] ?? $marca['marca']) ?>"
        required
    >

    <button class="btn" type="submit">
        Salvar
    </button>

    <a class="btn btn-secondary" href="index.php">
        Cancelar
    </a>

</form>

<?php require_once '../includes/footer.php'; ?>