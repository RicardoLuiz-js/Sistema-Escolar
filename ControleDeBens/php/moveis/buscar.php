<?php
require_once '../../../Conexao/conexao.php';

// Adicionar headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT id, nome, descricao, localizacao, numero_tombo, data_cadastro 
        FROM moveis 
        WHERE excluido = 0 
        ORDER BY data_cadastro DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        'error' => 'Erro na consulta: ' . $conn->error,
        'success' => false
    ]);
    exit;
}

$moveis = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $moveis[] = $row;
    }
}

echo json_encode($moveis, JSON_UNESCAPED_UNICODE);
?>