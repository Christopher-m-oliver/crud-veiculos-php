<?php

const MODELO_MAX   = 100;
const POTENCIA_MIN = 1;
const POTENCIA_MAX = 2000;
const ANO_MIN      = 1900;

const TIPOS_VEICULO = [
    'Carro'    => 'Carro',
    'Moto'     => 'Moto',
    'Caminhao' => 'Caminhão',
];


function valorTexto(array $origem, string $campo): string
{
    $valor = $origem[$campo] ?? '';

    return is_string($valor) ? trim($valor) : '';
}

function validarVeiculo(PDO $pdo, array $entrada): array
{
    $valores = [
        'modelo'         => valorTexto($entrada, 'modelo'),
        'marca_id'       => valorTexto($entrada, 'marca_id'),
        'potencia'       => valorTexto($entrada, 'potencia'),
        'ano_fabricacao' => valorTexto($entrada, 'ano_fabricacao'),
        'tipo'           => valorTexto($entrada, 'tipo'),
    ];

    $erros = [];
    $dados = [];

    $tamanho = mb_strlen($valores['modelo']);

    if ($tamanho === 0) {
        $erros['modelo'] = 'Informe o modelo.';
    } elseif ($tamanho > MODELO_MAX) {
        $erros['modelo'] = 'O modelo deve ter no máximo ' . MODELO_MAX . ' caracteres.';
    } else {
        $dados['modelo'] = $valores['modelo'];
    }

    $marcaId = filter_var(
        $valores['marca_id'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($marcaId === false) {
        $erros['marca_id'] = 'Selecione uma marca.';
    } else {
        $stmt = $pdo->prepare('SELECT 1 FROM marcas WHERE id = ?');
        $stmt->execute([$marcaId]);

        if (!$stmt->fetchColumn()) {
            $erros['marca_id'] = 'A marca selecionada não existe.';
        } else {
            $dados['marca_id'] = $marcaId;
        }
    }

    $potencia = filter_var(
        $valores['potencia'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => POTENCIA_MIN, 'max_range' => POTENCIA_MAX]]
    );

    if ($potencia === false) {
        $erros['potencia'] = 'A potência deve ser um número inteiro entre '
            . POTENCIA_MIN . ' e ' . POTENCIA_MAX . ' cv.';
    } else {
        $dados['potencia'] = $potencia;
    }

    $anoAtual = (int) date('Y');

    $ano = filter_var(
        $valores['ano_fabricacao'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => ANO_MIN, 'max_range' => $anoAtual]]
    );

    if ($ano === false) {
        $erros['ano_fabricacao'] = 'O ano deve ser um número entre '
            . ANO_MIN . ' e ' . $anoAtual . '.';
    } else {
        $dados['ano_fabricacao'] = $ano;
    }

    if (!array_key_exists($valores['tipo'], TIPOS_VEICULO)) {
        $erros['tipo'] = 'Selecione um tipo válido.';
    } else {
        $dados['tipo'] = $valores['tipo'];
    }

    return ['dados' => $dados, 'erros' => $erros, 'valores' => $valores];
}

function erroCampo(array $erros, string $campo): string
{
    if (!isset($erros[$campo])) {
        return '';
    }

    return '<span class="field-error">' . htmlspecialchars($erros[$campo]) . '</span>';
}
