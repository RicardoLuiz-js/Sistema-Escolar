<?php
require_once '../../../Conexao/conexao.php';

// Receber o parâmetro de localização se existir
$localizacao = $_GET['localizacao'] ?? '';

$sql = "SELECT COUNT(*) as total FROM moveis WHERE excluido = 0";

// Se tiver filtro por localização, adicionar WHERE
if (!empty($localizacao)) {
    $sql .= " AND localizacao = '" . $conn->real_escape_string($localizacao) . "'";
}

$result = $conn->query($sql);
$total = $result->fetch_assoc()['total'];

header('Content-Type: application/json');
echo json_encode(['total_itens' => (int)$total]);
?>