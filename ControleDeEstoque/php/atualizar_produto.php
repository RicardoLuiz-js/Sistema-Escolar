<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $categoria = $_POST['categoria'];
    $quantidade = $_POST['quantidade'];
    $unidade = $_POST['unidade'];
    $entrada = $_POST['entrada'];
    $validade = $_POST['validade'];

    // Conecta ao banco de dados
    require_once '../../Conexao/conexao.php';

 

    // Atualiza os dados do produto
    $stmt = $conn->prepare("UPDATE produtos SET nome = ?, descricao = ?, categoria = ?, quantidade = ?, unidade = ?, entrada = ?, validade = ? WHERE id = ?");
    $stmt->bind_param('sssisssi', $nome, $descricao, $categoria, $quantidade, $unidade, $entrada, $validade, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o produto.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
?>