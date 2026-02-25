<?php
// buscar_utilizados.php

// Conectar ao banco de dados
require_once '../../Conexao/conexao.php';



// Consulta para buscar os utilizados com o nome do produto
$sql = "
    SELECT u.*, p.nome AS nome_produto
    FROM utilizados u
    JOIN produtos p ON u.produto_id = p.id
    ORDER BY u.data_saida DESC
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $utilizados = [];
    while ($row = $result->fetch_assoc()) {
        $utilizados[] = $row;
    }
    echo json_encode($utilizados);
} else {
    echo json_encode([]); // Retorna um array vazio se não houver dados
}

$conn->close();
?>