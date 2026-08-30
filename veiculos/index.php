<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

$stmt = $pdo->query("
    SELECT
        v.id,
        v.modelo,
        v.potencia,
        v.ano_fabricacao,
        v.tipo,
        m.marca
    FROM veiculos v
    INNER JOIN marcas m ON v.marca_id = m.id
    ORDER BY v.modelo
");

$veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$titulo = 'Veículos';

require_once '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Veículos</h1>
        <p>Gerencie os veículos cadastrados.</p>
    </div>

    <a class="btn" href="criar.php">
        Novo veículo
    </a>
</div>

<div class="card">

    <table>

        <thead>
            <tr>
                <th>Modelo</th>
                <th>Marca</th>
                <th>Potência</th>
                <th>Ano</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($veiculos as $veiculo): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($veiculo['modelo']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($veiculo['marca']) ?>
                </td>

                <td>
                    <?= $veiculo['potencia'] ?> cv
                </td>

                <td>
                    <?= $veiculo['ano_fabricacao'] ?>
                </td>

                <td>
                    <?= htmlspecialchars($veiculo['tipo']) ?>
                </td>

                <td class="actions">

                    <a
                        class="btn btn-secondary"
                        href="editar.php?id=<?= $veiculo['id'] ?>"
                    >
                        Editar
                    </a>

                    <a
                        class="btn btn-danger"
                        href="excluir.php?id=<?= $veiculo['id'] ?>"
                        onclick="return confirm('Deseja excluir este veículo?')"
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