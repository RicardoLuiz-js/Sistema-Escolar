<?php
require_once '../../Conexao/conexao.php';

$sql = "SELECT id, nome, descricao, categoria, quantidade, unidade, entrada, validade FROM produtos";
$result = $conn->query($sql);

$produtos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($produtos);
?>