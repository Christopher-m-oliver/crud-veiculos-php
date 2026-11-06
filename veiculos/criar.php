<?php

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/validacao.php';
require_once '../includes/upload.php';
require_once '../includes/flash.php';
require_once '../includes/csrf.php';

$marcas = $pdo
    ->query('SELECT id, marca FROM marcas ORDER BY marca')
    ->fetchAll(PDO::FETCH_ASSOC);

$erros = [];

$valores = [
    'modelo'         => '',
    'marca_id'       => '',
    'potencia'       => '',
    'ano_fabricacao' => '',
    'tipo'           => '',
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

            $nomeFoto = null;

            try {

                if (fotoEnviada()) {
                    $nomeFoto = salvarFoto($_FILES['foto']);
                }

                $stmt = $pdo->prepare("
                    INSERT INTO veiculos (
                        modelo,
                        marca_id,
                        potencia,
                        ano_fabricacao,
                        tipo,
                        foto
                    )
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $resultado['dados']['modelo'],
                    $resultado['dados']['marca_id'],
                    $resultado['dados']['potencia'],
                    $resultado['dados']['ano_fabricacao'],
                    $resultado['dados']['tipo'],
                    $nomeFoto,
                ]);

                flashDefinir('sucesso', 'Veículo cadastrado com sucesso.');
                header('Location: index.php');
                exit;

            } catch (PDOException $e) {

                removerFoto($nomeFoto);

                error_log('Erro ao cadastrar veículo: ' . $e->getMessage());
                $erros['geral'] = 'Não foi possível salvar o veículo. Tente novamente.';

            } catch (RuntimeException $e) {

                error_log('Erro ao gravar foto: ' . $e->getMessage());
                $erros['geral'] = 'Não foi possível gravar a imagem. Tente novamente.';
            }
        }
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

        <option value="">Selecione uma marca</option>

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

    <label for="foto">Foto (opcional)</label>

    <input
        type="file"
        id="foto"
        name="foto"
        accept="image/jpeg,image/png,image/webp"
    >
    <small>JPG, PNG ou WebP, até 2 MB.</small>
    <?= erroCampo($erros, 'foto') ?>

    <button class="btn" type="submit">Cadastrar</button>

    <a class="btn btn-secondary" href="index.php">Cancelar</a>

</form>

<?php require_once '../includes/footer.php'; ?>