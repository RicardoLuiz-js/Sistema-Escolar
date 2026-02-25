<?php
require_once '../../../Conexao/conexao.php';

// Adicionar headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT id, id_movel, tipo_acao, descricao_acao, local_anterior, local_novo, data_movimentacao 
        FROM movimentacoes 
        ORDER BY data_movimentacao DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        'error' => 'Erro na consulta: ' . $conn->error,
        'success' => false
    ]);
    exit;
}

$movimentacoes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $movimentacoes[] = $row;
    }
}

echo json_encode($movimentacoes, JSON_UNESCAPED_UNICODE);
?>