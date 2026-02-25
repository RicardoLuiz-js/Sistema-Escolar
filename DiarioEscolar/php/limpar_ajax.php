<?php
// php/limpar_ajax.php
// NÃO TEM NADA ANTES DESTA LINHA! NEM ESPAÇOS!

// Desabilitar erros
ini_set('display_errors', 0);
error_reporting(0);

// Limpar buffers
while (ob_get_level()) ob_end_clean();

// Incluir funções
require_once 'limpar_funcoes.php';

// Garantir JSON
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método inválido');
    }

    $acao = $_POST['acao'] ?? '';
    
    switch ($acao) {
        case 'backup':
            $tipo = $_POST['tipo'] ?? 'tudo';
            
            // Validar tipo
            if (!in_array($tipo, ['tudo', 'imagens', 'arquivos'])) {
                throw new Exception('Tipo de backup inválido');
            }
            
            $arquivo = criarBackup($tipo);
            
            if ($arquivo) {
                echo json_encode([
                    'success' => true,
                    'arquivo' => $arquivo,
                    'message' => 'Backup criado com sucesso!'
                ]);
            } else {
                throw new Exception('Erro ao criar backup');
            }
            break;
            
        case 'limpar':
            $tipo = $_POST['tipo'] ?? '';
            
            if (!in_array($tipo, ['imagens', 'arquivos', 'tudo'])) {
                throw new Exception('Tipo de limpeza inválido');
            }
            
            if ($tipo === 'imagens') {
                $resultado = limparApenasImagens();
                echo json_encode([
                    'success' => true,
                    'tipo' => 'imagens',
                    'removidos' => $resultado['removidos'],
                    'espaco' => formatarTamanho($resultado['espaco'])
                ]);
            } 
            elseif ($tipo === 'arquivos') {
                $resultado = limparApenasArquivos();
                echo json_encode([
                    'success' => true,
                    'tipo' => 'arquivos',
                    'removidos' => $resultado['removidos'],
                    'espaco' => formatarTamanho($resultado['espaco'])
                ]);
            }
            elseif ($tipo === 'tudo') {
                $imagens = limparApenasImagens();
                $arquivos = limparApenasArquivos();
                echo json_encode([
                    'success' => true,
                    'tipo' => 'tudo',
                    'imagens' => $imagens['removidos'],
                    'arquivos' => $arquivos['removidos'],
                    'espaco' => formatarTamanho($imagens['espaco'] + $arquivos['espaco'])
                ]);
            }
            break;
            
        case 'excluir_backup':
            $arquivo = $_POST['arquivo'] ?? '';
            $caminho = "../backups/$arquivo";
            
            if (file_exists($caminho) && unlink($caminho)) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Erro ao excluir backup');
            }
            break;
            
        default:
            throw new Exception('Ação inválida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>