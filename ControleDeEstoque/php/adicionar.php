<?php
require_once '../../Conexao/conexao.php';

// Recebe dados do formulário
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$categoria = $_POST['categoria'];
$quantidade = $_POST['quantidade'];
$unidade = $_POST['unidade'];
$entrada = $_POST['entrada'];
$validade = $_POST['validade'];

// Validação dos campos
if (empty($nome) || empty($descricao) || empty($categoria) || empty($quantidade) || empty($unidade) || empty($entrada)) {
    die("Todos os campos são obrigatórios.");
}

if ($quantidade < 0) {

    die("A quantidade não pode ser negativa.");
}

// Verifica se há um produto com o mesmo nome e quantidade 0
$sql_check_quantidade_zero = "SELECT id FROM produtos WHERE nome = ? AND quantidade = 0";
$stmt_quantidade_zero = $conn->prepare($sql_check_quantidade_zero);
$stmt_quantidade_zero->bind_param("s", $nome);
$stmt_quantidade_zero->execute();
$result_quantidade_zero = $stmt_quantidade_zero->get_result();

$message = ""; // Variável para armazenar a mensagem

if ($result_quantidade_zero->num_rows > 0) {
    // Atualiza o produto existente com quantidade 0
    $row_quantidade_zero = $result_quantidade_zero->fetch_assoc();
    $id_quantidade_zero = $row_quantidade_zero['id'];

    // Atualiza a quantidade e a data de validade
    $sql_update = "UPDATE produtos SET quantidade = ?, validade = ?, entrada = ?, descricao = ?, categoria = ?, unidade = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("isssssi", $quantidade, $validade, $entrada, $descricao, $categoria, $unidade, $id_quantidade_zero);

    if ($stmt_update->execute()) {
        $message = "Produto com quantidade 0 atualizado com sucesso.";
    } else {
        $message = "Erro ao atualizar produto: " . $stmt_update->error;
    }
} else {
    // Verifica se o produto já existe (não vencido ou com quantidade > 0)
    $sql_check = "SELECT id, quantidade FROM produtos WHERE nome = ? AND validade = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $nome, $validade);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Produto já existe, atualiza a quantidade
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $quantidade_existente = $row['quantidade'];
        $nova_quantidade = $quantidade_existente + $quantidade;

        $sql_update = "UPDATE produtos SET quantidade = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $nova_quantidade, $id);

        if ($stmt_update->execute()) {
            $message = "Quantidade do produto atualizada com sucesso";
        } else {
            $message = "Erro ao atualizar quantidade: " . $stmt_update->error;
        }
    } else {
        // Produto não existe, insere um novo
        $sql_insert = "INSERT INTO produtos (nome, descricao, categoria, quantidade, unidade, entrada, validade)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssisss", $nome, $descricao, $categoria, $quantidade, $unidade, $entrada, $validade);

        if ($stmt_insert->execute()) {
            $message = "Novo produto cadastrado com sucesso";
        } else {
            $message = "Erro: " . $stmt_insert->error;
        }
    }
}

$conn->close();

// Redireciona de volta para o index.php com a mensagem
header("Location: ../html/adicionarhtml.php?message=" . urlencode($message));
exit();
?>