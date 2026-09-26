<?php

require_once 'includes/auth.php';
require_once 'config/database.php';

function formatarSqlParaExibicao(string $sql): string
{
    $linhas = explode("\n", $sql);
    $linhas = array_map('trim', $linhas);
    $linhas = array_filter(
        $linhas,
        fn($linha) => $linha !== ''
    );

    return implode("\n", $linhas);
}

$modo = $_GET['modo'] ?? 'vulneravel';

if (!in_array($modo, ['vulneravel', 'protegido'], true)) {
    $modo = 'vulneravel';
}

$busca = $_POST['busca'] ?? '';

$resultados = [];
$sqlExibido = '';
$parametroExibido = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($modo === 'vulneravel') {

            // Versão vulnerável:
            // o valor fornecido pelo usuário é concatenado
            // diretamente na consulta SQL.
            $sql = "
                SELECT id, marca
                FROM marcas
                WHERE marca = '$busca'
                ORDER BY marca
            ";

            $sqlExibido = formatarSqlParaExibicao($sql);

            $stmt = $pdo->query($sql);

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else {

            // Versão protegida:
            // a estrutura SQL e os dados do usuário
            // são enviados separadamente.
            $sql = "
                SELECT id, marca
                FROM marcas
                WHERE marca = ?
                ORDER BY marca
            ";

            $sqlExibido = formatarSqlParaExibicao($sql);
            $parametroExibido = $busca;

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $busca
            ]);

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {

        $erro =
            'A consulta gerou um erro de SQL. '
            . 'Isso também pode acontecer quando a entrada '
            . 'altera a estrutura esperada da consulta.';
    }
}

$titulo = 'Demonstração de SQL Injection';

require_once 'includes/header.php';

?>

<style>

    .demo-subtitle {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 16px;
    }

    .demo-grid {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    .demo-card {
        background: #ffffff;
        padding: 28px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow:
            0 10px 24px
            rgba(15, 23, 42, 0.06);
    }

    .demo-card h2 {
        margin-top: 0;
        margin-bottom: 12px;
        font-size: 22px;
    }

    .demo-card form {
        max-width: none;
        padding: 0;
        box-shadow: none;
    }

    .mode-switch {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .mode-active {
        outline:
            3px solid
            rgba(37, 99, 235, 0.18);

        transform: translateY(-1px);

        box-shadow:
            0 8px 18px
            rgba(37, 99, 235, 0.18);
    }

    .warning {
        background: #fff7ed;
        color: #9a3412;
        border: 1px solid #fed7aa;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 18px;
    }

    .safe {
        background: #ecfdf5;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 18px;
    }

    .status-badge {
        display: inline-block;
        margin-bottom: 14px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-safe {
        background: #dcfce7;
        color: #166534;
    }

    pre {
        background: #0f172a;
        color: #e2e8f0;
        padding: 16px;
        border-radius: 10px;
        overflow-x: auto;
        white-space: pre;
        line-height: 1.5;
        margin: 12px 0 0;
    }

    code {
        font-family:
            Consolas,
            Monaco,
            monospace;
    }

    .result-count {
        color: #475569;
        margin-top: 0;
        margin-bottom: 18px;
        font-size: 15px;
        font-weight: 600;
    }

    .demo-table {
        margin-top: 12px;
        border-radius: 10px;
        overflow: hidden;
    }

    .demo-table th {
        background: #f8fafc;
        font-weight: 700;
    }

    .demo-table td,
    .demo-table th {
        padding: 14px 16px;
    }

    .parameter-box {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 12px;
        border-radius: 8px;
        margin-top: 12px;
    }

    .parameter-box strong {
        display: block;
        margin-bottom: 6px;
    }

    @media (max-width: 850px) {

        .demo-grid {
            grid-template-columns: 1fr;
        }
    }

</style>

<div class="page-header">

    <div>

        <h1>Demonstração de SQL Injection</h1>

        <p class="demo-subtitle">
            Comparação prática entre uma consulta vulnerável
            e uma consulta parametrizada.
        </p>

    </div>

</div>

<div class="mode-switch">

    <a
        class="
            btn btn-danger
            <?= $modo === 'vulneravel'
                ? 'mode-active'
                : '' ?>
        "
        href="?modo=vulneravel"
    >
        Modo vulnerável
    </a>

    <a
        class="
            btn
            <?= $modo === 'protegido'
                ? 'mode-active'
                : '' ?>
        "
        href="?modo=protegido"
    >
        Modo protegido
    </a>

</div>

<div class="demo-grid">

    <section class="demo-card">

        <?php if ($modo === 'vulneravel'): ?>

            <span
                class="
                    status-badge
                    status-danger
                "
            >
                Consulta vulnerável
            </span>

            <div class="warning">

                <strong>Atenção:</strong>

                esta versão concatena
                a entrada do usuário
                diretamente no SQL
                e existe apenas para
                demonstração em ambiente local.

            </div>

        <?php else: ?>

            <span
                class="
                    status-badge
                    status-safe
                "
            >
                Consulta protegida
            </span>

            <div class="safe">

                <strong>Versão protegida:</strong>

                a consulta utiliza
                prepared statement
                e parâmetro.

            </div>

        <?php endif; ?>

        <h2>Buscar marca</h2>

        <form method="POST">

            <label for="busca">
                Nome da marca
            </label>

            <input
                type="text"
                id="busca"
                name="busca"
                value="<?= htmlspecialchars($busca) ?>"
                placeholder="Ex.: Toyota"
                autocomplete="off"
                required
            >

            <button
                class="btn"
                type="submit"
            >
                Executar consulta
            </button>

        </form>

        <?php if ($erro): ?>

            <div
                class="error"
                style="margin-top: 18px;"
            >
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>

        <?php if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && !$erro
        ): ?>

            <h2 style="margin-top: 28px;">
                Resultado
            </h2>

            <p class="result-count">

                Registros encontrados:
                <?= count($resultados) ?>

            </p>

            <?php if ($resultados): ?>

                <table class="demo-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Marca</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach (
                            $resultados
                            as $marca
                        ): ?>

                            <tr>

                                <td>
                                    <?= (int) $marca['id'] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $marca['marca']
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <p>
                    Nenhuma marca encontrada.
                </p>

            <?php endif; ?>

        <?php endif; ?>

    </section>

    <section class="demo-card">

        <h2>Consulta utilizada</h2>

        <?php if ($modo === 'vulneravel'): ?>

            <p>
                O conteúdo digitado
                é colocado diretamente
                dentro da string SQL.
            </p>

            <pre><code>$sql = "
SELECT id, marca
FROM marcas
WHERE marca = '$busca'
ORDER BY marca
";</code></pre>

            <?php if ($sqlExibido): ?>

                <h3>
                    SQL resultante
                </h3>

                <pre><code><?= htmlspecialchars(
                    $sqlExibido
                ) ?></code></pre>

            <?php endif; ?>

        <?php else: ?>

            <p>
                A estrutura da consulta
                fica separada do valor
                informado pelo usuário.
            </p>

            <pre><code>$stmt = $pdo->prepare(
    'SELECT id, marca
     FROM marcas
     WHERE marca = ?
     ORDER BY marca'
);

$stmt->execute([$busca]);</code></pre>

            <?php if ($sqlExibido): ?>

                <h3>
                    Estrutura SQL
                </h3>

                <pre><code><?= htmlspecialchars(
                    $sqlExibido
                ) ?></code></pre>

                <div class="parameter-box">

                    <strong>
                        Valor enviado como parâmetro:
                    </strong>

                    <code>
                        <?= htmlspecialchars(
                            $parametroExibido
                        ) ?>
                    </code>

                </div>

            <?php endif; ?>

        <?php endif; ?>

    </section>

</div>

<?php require_once 'includes/footer.php'; ?>