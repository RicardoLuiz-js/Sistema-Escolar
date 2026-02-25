<?php
// Conectar ao banco de dados
require_once '../../Conexao/conexao.php';



header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID não fornecido']);
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT id, nome, descricao, categoria, quantidade, unidade, entrada, validade
        FROM produtos 
        WHERE id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Item não encontrado']);
    exit;
}

$item = $result->fetch_assoc();
echo json_encode($item, JSON_UNESCAPED_UNICODE);

$stmt->close();
?>