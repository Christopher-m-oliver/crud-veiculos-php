<?php

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/validacao.php';
require_once '../includes/upload.php';
require_once '../includes/flash.php';
require_once '../includes/csrf.php';

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM veiculos WHERE id = ?');
$stmt->execute([$id]);

$veiculo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$veiculo) {
    flashDefinir('erro', 'Veículo não encontrado.');
    header('Location: index.php');
    exit;
}

$marcas = $pdo
    ->query('SELECT id, marca FROM marcas ORDER BY marca')
    ->fetchAll(PDO::FETCH_ASSOC);

$erros = [];

$valores = [
    'modelo'         => (string) $veiculo['modelo'],
    'marca_id'       => (string) $veiculo['marca_id'],
    'potencia'       => (string) $veiculo['potencia'],
    'ano_fabricacao' => (string) $veiculo['ano_fabricacao'],
    'tipo'           => (string) $veiculo['tipo'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (postExcedeuLimite()) {

        $erros['geral'] = 'O envio excede o tamanho máximo permitido pelo servidor.';

    } elseif (!csrfValido()) {

        $erros['geral'] = 'Requisição inválida. Recarregue a página e tente novamente.';

    } else {

        // Validação SEMPRE no servidor, independente do HTML
        $resultado = validarVeiculo($pdo, $_POST);

        $valores = $resultado['valores'];
        $erros   = $resultado['erros'];

        if (fotoEnviada()) {
            $erroFoto = validarFoto($_FILES['foto']);

            if ($erroFoto !== null) {
                $erros['foto'] = $erroFoto;
            }
        }

        if (!$erros) {

            $fotoAtual  = $veiculo['foto'];
            $fotoNova   = null;
            $fotoFinal  = $fotoAtual;
            $removerAtual = ($_POST['remover_foto'] ?? '') === '1';

            try {

                if (fotoEnviada()) {
                    $fotoNova  = salvarFoto($_FILES['foto']);
                    $fotoFinal = $fotoNova;
                } elseif ($removerAtual) {
                    $fotoFinal = null;
                }

                $stmt = $pdo->prepare("
                    UPDATE veiculos
                    SET
                        modelo = ?,
                        marca_id = ?,
                        potencia = ?,
                        ano_fabricacao = ?,
                        tipo = ?,
                        foto = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $resultado['dados']['modelo'],
                    $resultado['dados']['marca_id'],
                    $resultado['dados']['potencia'],
                    $resultado['dados']['ano_fabricacao'],
                    $resultado['dados']['tipo'],
                    $fotoFinal,
                    $id,
                ]);

                // Só depois de o banco confirmar é que a foto antiga é apagada
                if ($fotoFinal !== $fotoAtual) {
                    removerFoto($fotoAtual);
                }

                flashDefinir('sucesso', 'Veículo atualizado com sucesso.');
                header('Location: index.php');
                exit;

            } catch (PDOException $e) {

                removerFoto($fotoNova);

                error_log('Erro ao atualizar veículo: ' . $e->getMessage());
                $erros['geral'] = 'Não foi possível salvar as alterações. Tente novamente.';

            } catch (RuntimeException $e) {

                error_log('Erro ao gravar foto: ' . $e->getMessage());
                $erros['geral'] = 'Não foi possível gravar a imagem. Tente novamente.';
            }
        }
    }
}

$urlFotoAtual = urlFoto($veiculo['foto']);

$titulo = 'Editar Veículo';

require_once '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Editar Veículo</h1>
        <p>Altere os dados do veículo.</p>
    </div>
</div>

<?php if (isset($erros['geral'])): ?>
    <div class="alert alert-erro">
        <?= htmlspecialchars($erros['geral']) ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <?= csrfCampo() ?>

    <label for="modelo">Modelo</label>

    <input
        type="text"
        id="modelo"
        name="modelo"
        maxlength="<?= MODELO_MAX ?>"
        value="<?= htmlspecialchars($valores['modelo']) ?>"
        required
    >
    <?= erroCampo($erros, 'modelo') ?>

    <label for="marca_id">Marca</label>

    <select id="marca_id" name="marca_id" required>

        <?php foreach ($marcas as $marca): ?>
            <option
                value="<?= (int) $marca['id'] ?>"
                <?= $valores['marca_id'] === (string) $marca['id'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($marca['marca']) ?>
            </option>
        <?php endforeach; ?>

    </select>
    <?= erroCampo($erros, 'marca_id') ?>

    <label for="potencia">Potência (cv)</label>

    <input
        type="number"
        id="potencia"
        name="potencia"
        min="<?= POTENCIA_MIN ?>"
        max="<?= POTENCIA_MAX ?>"
        value="<?= htmlspecialchars($valores['potencia']) ?>"
        required
    >
    <?= erroCampo($erros, 'potencia') ?>

    <label for="ano_fabricacao">Ano de fabricação</label>

    <input
        type="number"
        id="ano_fabricacao"
        name="ano_fabricacao"
        min="<?= ANO_MIN ?>"
        max="<?= date('Y') ?>"
        value="<?= htmlspecialchars($valores['ano_fabricacao']) ?>"
        required
    >
    <?= erroCampo($erros, 'ano_fabricacao') ?>

    <fieldset>

        <legend>Tipo</legend>

        <?php foreach (TIPOS_VEICULO as $valor => $nome): ?>
            <label>
                <input
                    type="radio"
                    name="tipo"
                    value="<?= $valor ?>"
                    <?= $valores['tipo'] === $valor ? 'checked' : '' ?>
                    required
                >
                <?= htmlspecialchars($nome) ?>
            </label>
        <?php endforeach; ?>

        <?= erroCampo($erros, 'tipo') ?>

    </fieldset>

    <?php if ($urlFotoAtual): ?>

        <label>Foto atual</label>

        <img
            class="foto-atual"
            src="<?= htmlspecialchars($urlFotoAtual) ?>"
            alt="Foto de <?= htmlspecialchars($veiculo['modelo']) ?>"
        >

        <label class="checkbox-linha">
            <input type="checkbox" name="remover_foto" value="1">
            Remover foto atual
        </label>

    <?php endif; ?>

    <label for="foto">
        <?= $urlFotoAtual ? 'Trocar foto (opcional)' : 'Foto (opcional)' ?>
    </label>

    <input
        type="file"
        id="foto"
        name="foto"
        accept="image/jpeg,image/png,image/webp"
    >
    <small>JPG, PNG ou WebP, até 2 MB.</small>
    <?= erroCampo($erros, 'foto') ?>

    <button class="btn" type="submit">Salvar</button>

    <a class="btn btn-secondary" href="index.php">Cancelar</a>

</form>

<?php require_once '../includes/footer.php'; ?>
