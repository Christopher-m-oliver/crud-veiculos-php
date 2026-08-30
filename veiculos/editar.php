<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT * FROM veiculos WHERE id = ?'
);

$stmt->execute([$id]);

$veiculo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$veiculo) {
    header('Location: index.php');
    exit;
}

// Busca as marcas para preencher o select
$stmt = $pdo->query(
    'SELECT id, marca FROM marcas ORDER BY marca'
);

$marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $modelo = trim($_POST['modelo'] ?? '');

    $marcaId = filter_input(
        INPUT_POST,
        'marca_id',
        FILTER_VALIDATE_INT
    );

    $potencia = filter_input(
        INPUT_POST,
        'potencia',
        FILTER_VALIDATE_INT
    );

    $ano = filter_input(
        INPUT_POST,
        'ano_fabricacao',
        FILTER_VALIDATE_INT
    );

    $tipo = $_POST['tipo'] ?? '';

    $tiposPermitidos = [
        'Carro',
        'Moto',
        'Caminhao'
    ];

    if (
        $modelo === '' ||
        !$marcaId ||
        !$potencia ||
        !$ano ||
        !in_array($tipo, $tiposPermitidos, true)
    ) {

        $erro = 'Preencha todos os campos corretamente.';

    } else {

        $stmt = $pdo->prepare("
            UPDATE veiculos
            SET
                modelo = ?,
                marca_id = ?,
                potencia = ?,
                ano_fabricacao = ?,
                tipo = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $modelo,
            $marcaId,
            $potencia,
            $ano,
            $tipo,
            $id
        ]);

        header('Location: index.php');
        exit;
    }
}

$titulo = 'Editar Veículo';

require_once '../includes/header.php';

$modeloAtual =
    $_POST['modelo']
    ?? $veiculo['modelo'];

$marcaAtual =
    $_POST['marca_id']
    ?? $veiculo['marca_id'];

$potenciaAtual =
    $_POST['potencia']
    ?? $veiculo['potencia'];

$anoAtual =
    $_POST['ano_fabricacao']
    ?? $veiculo['ano_fabricacao'];

$tipoAtual =
    $_POST['tipo']
    ?? $veiculo['tipo'];

// Valores usados internamente e textos exibidos
$tipos = [
    'Carro' => 'Carro',
    'Moto' => 'Moto',
    'Caminhao' => 'Caminhão'
];

?>

<div class="page-header">
    <div>
        <h1>Editar Veículo</h1>
        <p>Altere os dados do veículo.</p>
    </div>
</div>

<?php if ($erro): ?>

    <div class="error">
        <?= htmlspecialchars($erro) ?>
    </div>

<?php endif; ?>

<form method="POST">

    <label for="modelo">Modelo</label>

    <input
        type="text"
        id="modelo"
        name="modelo"
        value="<?= htmlspecialchars($modeloAtual) ?>"
        required
    >

    <label for="marca_id">Marca</label>

    <select
        id="marca_id"
        name="marca_id"
        required
    >

        <?php foreach ($marcas as $marca): ?>

            <option
                value="<?= $marca['id'] ?>"
                <?= $marca['id'] == $marcaAtual ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($marca['marca']) ?>
            </option>

        <?php endforeach; ?>

    </select>

    <label for="potencia">
        Potência (cv)
    </label>

    <input
        type="number"
        id="potencia"
        name="potencia"
        min="1"
        value="<?= htmlspecialchars($potenciaAtual) ?>"
        required
    >

    <label for="ano_fabricacao">
        Ano de fabricação
    </label>

    <input
        type="number"
        id="ano_fabricacao"
        name="ano_fabricacao"
        min="1900"
        max="<?= date('Y') ?>"
        value="<?= htmlspecialchars($anoAtual) ?>"
        required
    >

    <fieldset>

        <legend>Tipo</legend>

        <?php foreach ($tipos as $valor => $nome): ?>

            <label>

                <input
                    type="radio"
                    name="tipo"
                    value="<?= $valor ?>"
                    <?= $tipoAtual === $valor ? 'checked' : '' ?>
                    required
                >

                <?= htmlspecialchars($nome) ?>

            </label>

        <?php endforeach; ?>

    </fieldset>

    <button
        class="btn"
        type="submit"
    >
        Salvar
    </button>

    <a
        class="btn btn-secondary"
        href="index.php"
    >
        Cancelar
    </a>

</form>

<?php require_once '../includes/footer.php'; ?>