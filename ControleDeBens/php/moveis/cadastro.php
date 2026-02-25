<?php
require_once '../../../Conexao/conexao.php';

// Log para debug
error_log('🎯 cadastro.php acessado');
error_log('📝 Método: ' . $_SERVER['REQUEST_METHOD']);

// Verificar se o método é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('❌ Método não permitido');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Receber dados JSON
$input = json_decode(file_get_contents('php://input'), true);
error_log('📦 Dados recebidos: ' . print_r($input, true));


// Verificar se o método é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Receber dados JSON
$input = json_decode(file_get_contents('php://input'), true);
$nome = trim($input['nome'] ?? '');
$descricao = trim($input['descricao'] ?? '');
$localizacao = trim($input['localizacao'] ?? '');
$codigo = trim($input['codigo'] ?? '');

// Validar campos obrigatórios
if (empty($nome) || empty($localizacao) || empty($codigo)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
    exit;
}

// Verificar se o código já existe
$sql_verificar = "SELECT id FROM moveis WHERE numero_tombo = ? AND excluido = 0";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param('s', $codigo);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

if ($result_verificar->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Código (número de tombo) já existe']);
    $stmt_verificar->close();
    exit;
}
$stmt_verificar->close();

try {
    // Iniciar transação
    $conn->begin_transaction();

    // 1. Inserir o novo móvel
    $sql_inserir = "INSERT INTO moveis (nome, descricao, localizacao, numero_tombo, data_cadastro) 
                    VALUES (?, ?, ?, ?, NOW())";
    
    $stmt_inserir = $conn->prepare($sql_inserir);
    $stmt_inserir->bind_param('ssss', $nome, $descricao, $localizacao, $codigo);
    
    if (!$stmt_inserir->execute()) {
        throw new Exception('Erro ao cadastrar item: ' . $stmt_inserir->error);
    }
    
    $id_novo_movel = $stmt_inserir->insert_id;
    $stmt_inserir->close();

    // 2. Registrar no histórico
    $tipo_acao = 'cadastro';
    $descricao_acao = "Item cadastrado: $nome. Código: $codigo. Local: $localizacao";
    
    $sql_historico = "INSERT INTO movimentacoes 
                     (id_movel, tipo_acao, descricao_acao, local_anterior, local_novo, data_movimentacao) 
                     VALUES (?, ?, ?, NULL, ?, NOW())";
    
    $stmt_historico = $conn->prepare($sql_historico);
    $stmt_historico->bind_param('isss', $id_novo_movel, $tipo_acao, $descricao_acao, $localizacao);
    
    if (!$stmt_historico->execute()) {
        throw new Exception('Erro ao registrar no histórico: ' . $stmt_historico->error);
    }
    $stmt_historico->close();

    // Confirmar transação
    $conn->commit();

    // Retornar sucesso
    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'Item cadastrado com sucesso!',
        'id' => $id_novo_movel
    ]);

} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollback();
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erro no cadastro: ' . $e->getMessage()
    ]);
}
?>