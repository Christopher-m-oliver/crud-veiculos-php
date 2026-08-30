<?php

session_start();

require_once 'config/database.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($usuario === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, usuario, senha FROM usuarios WHERE usuario = ?'
        );

        $stmt->execute([$usuario]);

        $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            $dadosUsuario &&
            password_verify($senha, $dadosUsuario['senha'])
        ) {
            $_SESSION['usuario_id'] = $dadosUsuario['id'];
            $_SESSION['usuario'] = $dadosUsuario['usuario'];

            header('Location: index.php');
            exit;
        }

        $erro = 'Usuário ou senha inválidos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-page">

    <div class="login-card">

        <h1>AutoManager</h1>
        <p>Entre para acessar o sistema.</p>

        <?php if ($erro): ?>
            <div class="error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label for="usuario">Usuário</label>

            <input
                type="text"
                id="usuario"
                name="usuario"
                required
            >

            <label for="senha">Senha</label>

            <input
                type="password"
                id="senha"
                name="senha"
                required
            >

            <button class="btn login-btn" type="submit">
                Entrar
            </button>

        </form>

    </div>

</body>

</html>