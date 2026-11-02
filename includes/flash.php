<?php 

function flashDefinir(string $tipo, string $mensagem): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
 
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}
 
function flashExibir(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION['flash'])) {
        return;
    }
 
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
 
    $tipo = in_array($flash['tipo'], ['sucesso', 'erro'], true) ? $flash['tipo'] : 'erro';
 
    echo '<div class="alert alert-' . $tipo . '" role="status">'
        . htmlspecialchars($flash['mensagem'])
        . '</div>';
}
