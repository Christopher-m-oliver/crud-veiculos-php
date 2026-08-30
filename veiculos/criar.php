<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

// Busca as marcas cadastradas para preencher o select
$stmt = $pdo->query(
    'SELECT id, marca FROM marcas ORDER BY marca'
);

$marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $modelo = trim($_POST['modelo'] ?? '');
    $marcaId = filter_input(INPUT_POST, 'marca_id', FILTER_VALIDATE_INT);
    $potencia = filter_input(INPUT_POST, 'potencia', FILTER_VALIDATE_INT);
    $ano = filter_input(INPUT_POST, 'ano_fabricacao', FILTER_VALIDATE_INT);
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
            INSERT INTO veiculos (
                modelo,
                marca_id,
                potencia,
                ano_fabricacao,
                tipo
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $modelo,
            $marcaId,
            $potencia,
            $ano,
            $tipo
        ]);

        header('Location: index.php');
        exit;
    }
}

$titulo = 'Novo Veículo';

require_once '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Novo Veículo</h1>
        <p>Cadastre um novo veículo.</p>
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
        value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>"
        required
    >

    <label for="marca_id">Marca</label>

    <select
        id="marca_id"
        name="marca_id"
        required
    >

        <option value="">
            Selecione uma marca
        </option>

        <?php foreach ($marcas as $marca): ?>

            <option
                value="<?= $marca['id'] ?>"
                <?= (($_POST['marca_id'] ?? '') == $marca['id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($marca['marca']) ?>
            </option>

        <?php endforeach; ?>

    </select>

    <label for="potencia">Potência (cv)</label>

    <input
        type="number"
        id="potencia"
        name="potencia"
        min="1"
        value="<?= htmlspecialchars($_POST['potencia'] ?? '') ?>"
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
        value="<?= htmlspecialchars($_POST['ano_fabricacao'] ?? '') ?>"
        required
    >

    <fieldset>

        <legend>Tipo</legend>

        <label>
            <input
                type="radio"
                name="tipo"
                value="Carro"
                <?= (($_POST['tipo'] ?? '') === 'Carro') ? 'checked' : '' ?>
                required
            >
            Carro
        </label>

        <label>
            <input
                type="radio"
                name="tipo"
                value="Moto"
                <?= (($_POST['tipo'] ?? '') === 'Moto') ? 'checked' : '' ?>
            >
            Moto
        </label>

        <label>
    <input
        type="radio"
        name="tipo"
        value="Caminhao"
        <?= (($_POST['tipo'] ?? '') === 'Caminhao') ? 'checked' : '' ?>
    >
    Caminhão
</label>

    </fieldset>

    <button class="btn" type="submit">
        Cadastrar
    </button>

    <a class="btn btn-secondary" href="index.php">
        Cancelar
    </a>

</form>

<?php require_once '../includes/footer.php'; ?>