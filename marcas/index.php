<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

$stmt = $pdo->query(
    'SELECT id, marca FROM marcas ORDER BY marca'
);

$marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$titulo = 'Marcas';

require_once '../includes/header.php';
?>

<div class="page-header">

    <div>
        <h1>Marcas</h1>
        <p>Gerencie as marcas cadastradas.</p>
    </div>

    <a class="btn" href="criar.php">
        Nova marca
    </a>

</div>

<div class="card">

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Marca</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($marcas as $marca): ?>

            <tr>

                <td><?= $marca['id'] ?></td>

                <td>
                    <?= htmlspecialchars($marca['marca']) ?>
                </td>

                <td class="actions">

                    <a
                        class="btn btn-secondary"
                        href="editar.php?id=<?= $marca['id'] ?>"
                    >
                        Editar
                    </a>

                    <a
                        class="btn btn-danger"
                        href="excluir.php?id=<?= $marca['id'] ?>"
                        onclick="return confirm('Deseja excluir esta marca?')"
                    >
                        Excluir
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php require_once '../includes/footer.php'; ?>