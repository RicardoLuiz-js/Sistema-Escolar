<?php
require_once '../../../Conexao/conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Receber dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$nome = $data['nome'] ?? '';
$descricao = $data['descricao'] ?? '';
$numero_tombo = $data['numero_tombo'] ?? '';
$motivo = $data['motivo'] ?? '';

// Validar dados
if (!$id || empty($nome) || empty($motivo)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

try {
    // Iniciar transação
    $conn->begin_transaction();

    // 1. Primeiro buscar dados atuais do móvel
    $sql_select = "SELECT nome, descricao, numero_tombo, localizacao FROM moveis WHERE id = ? AND excluido = 0";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param('i', $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $dados_atuais = $result->fetch_assoc();
    $stmt_select->close();

    if (!$dados_atuais) {
        throw new Exception('Móvel não encontrado');
    }

    // 2. Registrar no histórico antes de atualizar
    $descricao_acao = "Edição de informações. Motivo: $motivo\n";
    $descricao_acao .= "Alterações: ";
    
    $alteracoes = [];
    if ($dados_atuais['nome'] !== $nome) {
        $alteracoes[] = "Nome: '{$dados_atuais['nome']}' → '$nome'";
    }
    if ($dados_atuais['descricao'] !== $descricao) {
        $alteracoes[] = "Descrição alterada";
    }
    if ($dados_atuais['numero_tombo'] !== $numero_tombo) {
        $alteracoes[] = "Tombo: '{$dados_atuais['numero_tombo']}' → '$numero_tombo'";
    }
    
    $descricao_acao .= implode(', ', $alteracoes);

    $sql_historico = "INSERT INTO movimentacoes 
                     (id_movel, tipo_acao, descricao_acao, local_anterior, data_movimentacao) 
                     VALUES (?, 'edicao', ?, ?, NOW())";
    
    $stmt_historico = $conn->prepare($sql_historico);
    $stmt_historico->bind_param('iss', $id, $descricao_acao, $dados_atuais['localizacao']);
    
    if (!$stmt_historico->execute()) {
        throw new Exception('Erro ao registrar no histórico: ' . $stmt_historico->error);
    }
    $stmt_historico->close();

    // 3. Atualizar as informações do móvel
    $sql_update = "UPDATE moveis SET nome = ?, descricao = ?, numero_tombo = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('sssi', $nome, $descricao, $numero_tombo, $id);
    
    if (!$stmt_update->execute()) {
        throw new Exception('Erro ao atualizar: ' . $stmt_update->error);
    }
    
    if ($stmt_update->affected_rows === 0) {
        throw new Exception('Nenhum registro foi atualizado');
    }
    
    $stmt_update->close();

    // Confirmar transação
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Informações atualizadas com sucesso'
    ]);

} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollback();
    echo json_encode([
        'success' => false, 
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>