<?php

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/csrf.php';
require_once '../includes/validacao.php';
require_once '../includes/upload.php';

$stmt = $pdo->query("
    SELECT
        v.id,
        v.modelo,
        v.potencia,
        v.ano_fabricacao,
        v.tipo,
        v.foto,
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
                <th>Foto</th>
                <th>Modelo</th>
                <th>Marca</th>
                <th>Potência</th>
                <th>Ano</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!$veiculos): ?>

            <tr>
                <td colspan="7">Nenhum veículo cadastrado.</td>
            </tr>

        <?php endif; ?>

        <?php foreach ($veiculos as $veiculo): ?>

            <?php $urlFoto = urlFoto($veiculo['foto']); ?>

            <tr>

                <td>
                    <?php if ($urlFoto): ?>
                        <img
                            class="foto-miniatura"
                            src="<?= htmlspecialchars($urlFoto) ?>"
                            alt="Foto de <?= htmlspecialchars($veiculo['modelo']) ?>"
                            loading="lazy"
                        >
                    <?php else: ?>
                        <span class="sem-foto">Sem foto</span>
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($veiculo['modelo']) ?></td>

                <td><?= htmlspecialchars($veiculo['marca']) ?></td>

                <td><?= (int) $veiculo['potencia'] ?> cv</td>

                <td><?= (int) $veiculo['ano_fabricacao'] ?></td>

                <td>
                    <?= htmlspecialchars(
                        TIPOS_VEICULO[$veiculo['tipo']] ?? $veiculo['tipo']
                    ) ?>
                </td>

                <td class="actions">

                    <a
                        class="btn btn-secondary"
                        href="editar.php?id=<?= (int) $veiculo['id'] ?>"
                    >
                        Editar
                    </a>

                    <form
                        class="inline"
                        method="POST"
                        action="excluir.php"
                        onsubmit="return confirm('Deseja excluir este veículo?')"
                    >
                        <?= csrfCampo() ?>
                        <input type="hidden" name="id" value="<?= (int) $veiculo['id'] ?>">
                        <button class="btn btn-danger" type="submit">
                            Excluir
                        </button>
                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php require_once '../includes/footer.php'; ?>