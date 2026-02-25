<?php
require_once '../../../Conexao/conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Receber dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$id_movel = $data['id_movel'] ?? null;
$motivo = $data['motivo'] ?? '';

if (!$id_movel || empty($motivo)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

try {
    // Iniciar transação
    $conn->begin_transaction();

    // 1. Primeiro buscar dados do móvel
    $sql_select = "SELECT nome, localizacao FROM moveis WHERE id = ? AND excluido = 0";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param('i', $id_movel);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $movel = $result->fetch_assoc();
    $stmt_select->close();

    if (!$movel) {
        throw new Exception('Móvel não encontrado ou já excluído');
    }

    // 2. Registrar na tabela de histórico
    $sql_historico = "INSERT INTO movimentacoes 
                     (id_movel, tipo_acao, descricao_acao, local_anterior, data_movimentacao) 
                     VALUES (?, 'exclusao', ?, ?, NOW())";
    
    $stmt_historico = $conn->prepare($sql_historico);
    $stmt_historico->bind_param('iss', $id_movel, $motivo, $movel['localizacao']);
    
    if (!$stmt_historico->execute()) {
        throw new Exception('Erro ao registrar no histórico: ' . $stmt_historico->error);
    }
    $stmt_historico->close();

    // 3. SOFT DELETE: Marcar como excluído em vez de excluir fisicamente
    $sql_update = "UPDATE moveis SET excluido = 1 WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('i', $id_movel);
    
    if (!$stmt_update->execute()) {
        throw new Exception('Erro ao marcar como excluído: ' . $stmt_update->error);
    }
    
    if ($stmt_update->affected_rows === 0) {
        throw new Exception('Nenhum registro foi atualizado');
    }
    
    $stmt_update->close();

    // Confirmar transação
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Item marcado como excluído com sucesso'
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