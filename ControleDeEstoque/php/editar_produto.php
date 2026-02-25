<?php
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Conecta ao banco de dados
   require_once '../../Conexao/conexao.php';

    // Busca os dados do produto
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        echo json_encode($produto);
    } else {
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.']);
}
?>