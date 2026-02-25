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
$novo_local = $data['novo_local'] ?? '';
$motivo = $data['motivo'] ?? '';

// Validar dados
if (!$id || empty($novo_local) || empty($motivo)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

try {
    // Iniciar transação
    $conn->begin_transaction();

    // 1. Primeiro buscar dados atuais do móvel
    $sql_select = "SELECT nome, localizacao FROM moveis WHERE id = ? AND excluido = 0";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param('i', $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $dados_atuais = $result->fetch_assoc();
    $stmt_select->close();

    if (!$dados_atuais) {
        throw new Exception('Móvel não encontrado');
    }

    // Verificar se o local realmente está mudando
    if ($dados_atuais['localizacao'] === $novo_local) {
        throw new Exception('O novo local é igual ao local atual');
    }

    // 2. Registrar no histórico
    $descricao_acao = "Transferência de local. Motivo: $motivo\n";
    $descricao_acao .= "Local anterior: {$dados_atuais['localizacao']} → Novo local: $novo_local";

    $sql_historico = "INSERT INTO movimentacoes 
                     (id_movel, tipo_acao, descricao_acao, local_anterior, local_novo, data_movimentacao) 
                     VALUES (?, 'transferencia', ?, ?, ?, NOW())";
    
    $stmt_historico = $conn->prepare($sql_historico);
    $stmt_historico->bind_param('isss', $id, $descricao_acao, $dados_atuais['localizacao'], $novo_local);
    
    if (!$stmt_historico->execute()) {
        throw new Exception('Erro ao registrar no histórico: ' . $stmt_historico->error);
    }
    $stmt_historico->close();

    // 3. Atualizar o local do móvel
    $sql_update = "UPDATE moveis SET localizacao = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('si', $novo_local, $id);
    
    if (!$stmt_update->execute()) {
        throw new Exception('Erro ao atualizar local: ' . $stmt_update->error);
    }
    
    if ($stmt_update->affected_rows === 0) {
        throw new Exception('Nenhum registro foi atualizado');
    }
    
    $stmt_update->close();

    // Confirmar transação
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Item transferido com sucesso'
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