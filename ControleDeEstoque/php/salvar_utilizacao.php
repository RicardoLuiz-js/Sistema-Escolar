<?php
// salvar_utilizacao.php

// Conectar ao banco de dados
require_once '../../Conexao/conexao.php';

// Receber dados do formulário
$produto_id = $_POST['produto_id'] ?? null;
$quantidade_utilizada = $_POST['quantidade_utilizada'] ?? null;
$responsavel = $_POST['responsavel'] ?? null;
$data_saida = $_POST['data_saida'] ?? null;

// Validações
if (empty($produto_id)) {
    die(json_encode(['success' => false, 'error' => 'ID do produto não informado.']));
}

if (empty($quantidade_utilizada)) {
    die(json_encode(['success' => false, 'error' => 'Quantidade utilizada não informada.']));
}

if (empty($responsavel)) {
    die(json_encode(['success' => false, 'error' => 'Responsável não informado.']));
}

if (empty($data_saida)) {
    die(json_encode(['success' => false, 'error' => 'Data de saída não informada.']));
}

// Verificar se a quantidade utilizada é válida
if ($quantidade_utilizada <= 0) {
    die(json_encode(['success' => false, 'error' => 'A quantidade utilizada deve ser maior que zero.']));
}

// Verificar se a quantidade utilizada não excede a quantidade disponível
$sql_quantidade = "SELECT quantidade FROM produtos WHERE id = $produto_id";
$result = $conn->query($sql_quantidade);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $quantidade_disponivel = $row['quantidade'];

    if ($quantidade_utilizada > $quantidade_disponivel) {
        die(json_encode(['success' => false, 'error' => 'A quantidade utilizada não pode ser maior que a quantidade disponível.']));
    }
} else {
    die(json_encode(['success' => false, 'error' => 'Produto não encontrado.']));
}

// Verificar se a data de saída é válida (opcional)
$hoje = date('Y-m-d');
if ($data_saida > $hoje) {
    die(json_encode(['success' => false, 'error' => 'A data de saída não pode ser no futuro.']));
}

// Inserir dados na tabela utilizados
$sql = "INSERT INTO utilizados (produto_id, quantidade_utilizada, responsavel, data_saida) 
        VALUES ('$produto_id', '$quantidade_utilizada', '$responsavel', '$data_saida')";

if ($conn->query($sql) === TRUE) {
    // Atualizar a quantidade do produto
    $sql_update = "UPDATE produtos SET quantidade = quantidade - $quantidade_utilizada WHERE id = $produto_id";
    if ($conn->query($sql_update) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar a quantidade do produto: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao registrar a utilização: ' . $conn->error]);
}

$conn->close();
?>