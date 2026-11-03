<?php

const UPLOAD_DIR       = __DIR__ . '/../uploads/veiculos/';
const UPLOAD_URL       = '/uploads/veiculos/';
const UPLOAD_MAX_BYTES = 2 * 1024 * 1024;

const UPLOAD_TIPOS = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

function postExcedeuLimite(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST'
        && empty($_POST)
        && empty($_FILES)
        && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
}

function fotoEnviada(): bool
{
    return isset($_FILES['foto'])
        && ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
}

function validarFoto(array $arquivo): ?string
{
    $codigo = $arquivo['error'] ?? null;

    if (!is_int($codigo) || !is_string($arquivo['tmp_name'] ?? null)) {
        return 'Envio de imagem inválido.';
    }

    if ($codigo !== UPLOAD_ERR_OK) {
        return match ($codigo) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE => 'A imagem excede o tamanho máximo de 2 MB.',
            UPLOAD_ERR_PARTIAL   => 'O envio da imagem foi interrompido. Tente novamente.',
            default              => 'Não foi possível receber a imagem no servidor.',
        };
    }

    if (!is_uploaded_file($arquivo['tmp_name'])) {
        return 'Envio de imagem inválido.';
    }

    $tamanho = (int) ($arquivo['size'] ?? 0);

    if ($tamanho <= 0 || $tamanho > UPLOAD_MAX_BYTES) {
        return 'A imagem está vazia ou excede 2 MB.';
    }

    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);

    if (!isset(UPLOAD_TIPOS[$tipo]) || @getimagesize($arquivo['tmp_name']) === false) {
        return 'Formato inválido. Envie uma imagem JPG, PNG ou WebP.';
    }

    return null;
}

function salvarFoto(array $arquivo): string
{
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    $nome = bin2hex(random_bytes(16)) . '.' . UPLOAD_TIPOS[$tipo];

    if (
        !is_dir(UPLOAD_DIR)
        || !is_writable(UPLOAD_DIR)
        || !move_uploaded_file($arquivo['tmp_name'], UPLOAD_DIR . $nome)
    ) {
        throw new RuntimeException('Não foi possível gravar a imagem.');
    }

    return $nome;
}

function removerFoto(?string $nome): void
{
    if ($nome === null || !preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $nome)) {
        return;
    }

    $caminho = UPLOAD_DIR . $nome;

    if (is_file($caminho)) {
        unlink($caminho);
    }
}

function urlFoto(?string $nome): ?string
{
    if ($nome === null || $nome === '') {
        return null;
    }

    return UPLOAD_URL . rawurlencode($nome);
}