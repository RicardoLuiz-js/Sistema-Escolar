<?php
// calendario_ajax.php
require_once 'calendario_funcoes.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $titulo = $_POST['titulo'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $data = $_POST['data'] ?? '';
            $tipo = $_POST['tipo'] ?? 'escolar';
            
            // Dados extras do formulário
            $dadosExtras = [
                'situacao' => $_POST['situacao'] ?? 'teveAula',
                'teveEvento' => $_POST['teveEvento'] ?? 0,
                'nomeEvento' => $_POST['nomeEvento'] ?? '',
                'motivo' => $_POST['motivo'] ?? ''
            ];
            
            if (adicionarEventoCalendario($titulo, $descricao, $data, $tipo, 'admin', $dadosExtras)) {
                echo json_encode(['success' => true, 'message' => 'Relatório salvo com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar relatório: ' . mysqli_error($conn)]);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $titulo = $_POST['titulo'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $data = $_POST['data'] ?? '';
            $tipo = $_POST['tipo'] ?? 'escolar';
            
            // Dados extras do formulário
            $dadosExtras = [
                'situacao' => $_POST['situacao'] ?? 'teveAula',
                'teveEvento' => $_POST['teveEvento'] ?? 0,
                'nomeEvento' => $_POST['nomeEvento'] ?? '',
                'motivo' => $_POST['motivo'] ?? ''
            ];
            
            if (atualizarEventoCalendario($id, $titulo, $descricao, $data, $tipo, $dadosExtras)) {
                echo json_encode(['success' => true, 'message' => 'Relatório atualizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar relatório: ' . mysqli_error($conn)]);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (removerEventoCalendario($id)) {
                echo json_encode(['success' => true, 'message' => 'Relatório excluído com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir relatório: ' . mysqli_error($conn)]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}
?>