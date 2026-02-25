<?php
// calendario_funcoes.php
require_once '../../Conexao/conexao.php';

/**
 * Busca todos os eventos do calendário
 */
function getEventosCalendario() {
    global $conn;
    
    $eventos = [];
    $sql = "SELECT * FROM calendario_eventos ORDER BY data_evento ASC";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $eventos[] = [
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'descricao' => $row['descricao'],
                'data' => $row['data_evento'],
                'tipo' => $row['tipo'],
                'situacao' => $row['situacao'] ?? 'teveAula',
                'teveEvento' => (bool)($row['teve_evento'] ?? 0),
                'nomeEvento' => $row['nome_evento'] ?? '',
                'motivo' => $row['motivo'] ?? '',
                'anexos' => $row['anexos'] ?? ''
            ];
        }
    }
    
    return $eventos;
}

/**
 * Adiciona um novo evento/relatório
 */
function adicionarEventoCalendario($titulo, $descricao, $data, $tipo = 'escolar', $usuario = 'admin', $dadosExtras = []) {
    global $conn;
    
    $titulo = mysqli_real_escape_string($conn, $titulo);
    $descricao = mysqli_real_escape_string($conn, $descricao);
    $data = mysqli_real_escape_string($conn, $data);
    $tipo = mysqli_real_escape_string($conn, $tipo);
    $usuario = mysqli_real_escape_string($conn, $usuario);
    
    // Novos campos
    $situacao = mysqli_real_escape_string($conn, $dadosExtras['situacao'] ?? 'teveAula');
    $teve_evento = isset($dadosExtras['teveEvento']) ? (int)$dadosExtras['teveEvento'] : 0;
    $nome_evento = mysqli_real_escape_string($conn, $dadosExtras['nomeEvento'] ?? '');
    $motivo = mysqli_real_escape_string($conn, $dadosExtras['motivo'] ?? '');
    
    // Processar anexos (salvar paths dos arquivos)
    $anexos = '';
    if (!empty($_FILES)) {
        $uploadDir = '../uploads/relatorios/' . date('Y/m/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $arquivos = [];
        $camposFile = ['anexosEvento', 'anexosAula', 'anexosSemAula'];
        
        foreach ($camposFile as $campo) {
            if (isset($_FILES[$campo]) && !empty($_FILES[$campo]['name'][0])) {
                foreach ($_FILES[$campo]['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES[$campo]['error'][$key] == 0) {
                        $nomeArquivo = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $_FILES[$campo]['name'][$key]);
                        $caminhoCompleto = $uploadDir . $nomeArquivo;
                        
                        if (move_uploaded_file($tmp_name, $caminhoCompleto)) {
                            $arquivos[] = $caminhoCompleto;
                        }
                    }
                }
            }
        }
        
        if (!empty($arquivos)) {
            $anexos = mysqli_real_escape_string($conn, json_encode($arquivos));
        }
    }
    
    $sql = "INSERT INTO calendario_eventos (
                titulo, 
                descricao, 
                data_evento, 
                tipo, 
                usuario,
                situacao,
                teve_evento,
                nome_evento,
                motivo,
                anexos
            ) VALUES (
                '$titulo', 
                '$descricao', 
                '$data', 
                '$tipo', 
                '$usuario',
                '$situacao',
                $teve_evento,
                '$nome_evento',
                '$motivo',
                '$anexos'
            )";
    
    return mysqli_query($conn, $sql);
}

/**
 * Atualiza um evento existente
 */
function atualizarEventoCalendario($id, $titulo, $descricao, $data, $tipo = 'escolar', $dadosExtras = []) {
    global $conn;
    
    $id = (int)$id;
    $titulo = mysqli_real_escape_string($conn, $titulo);
    $descricao = mysqli_real_escape_string($conn, $descricao);
    $data = mysqli_real_escape_string($conn, $data);
    $tipo = mysqli_real_escape_string($conn, $tipo);
    
    // Novos campos
    $situacao = mysqli_real_escape_string($conn, $dadosExtras['situacao'] ?? 'teveAula');
    $teve_evento = isset($dadosExtras['teveEvento']) ? (int)$dadosExtras['teveEvento'] : 0;
    $nome_evento = mysqli_real_escape_string($conn, $dadosExtras['nomeEvento'] ?? '');
    $motivo = mysqli_real_escape_string($conn, $dadosExtras['motivo'] ?? '');
    
    // Processar novos anexos (manter os antigos + novos)
    $anexosAtuais = [];
    
    // Buscar anexos atuais
    $sqlSelect = "SELECT anexos FROM calendario_eventos WHERE id = $id";
    $result = mysqli_query($conn, $sqlSelect);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        if (!empty($row['anexos'])) {
            $anexosAtuais = json_decode($row['anexos'], true) ?: [];
        }
    }
    
    // Adicionar novos anexos
    if (!empty($_FILES)) {
        $uploadDir = '../uploads/relatorios/' . date('Y/m/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $camposFile = ['anexosEvento', 'anexosAula', 'anexosSemAula'];
        
        foreach ($camposFile as $campo) {
            if (isset($_FILES[$campo]) && !empty($_FILES[$campo]['name'][0])) {
                foreach ($_FILES[$campo]['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES[$campo]['error'][$key] == 0) {
                        $nomeArquivo = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $_FILES[$campo]['name'][$key]);
                        $caminhoCompleto = $uploadDir . $nomeArquivo;
                        
                        if (move_uploaded_file($tmp_name, $caminhoCompleto)) {
                            $anexosAtuais[] = $caminhoCompleto;
                        }
                    }
                }
            }
        }
    }
    
    $anexosJson = !empty($anexosAtuais) ? mysqli_real_escape_string($conn, json_encode($anexosAtuais)) : '';
    
    $sql = "UPDATE calendario_eventos SET 
                titulo = '$titulo',
                descricao = '$descricao',
                data_evento = '$data',
                tipo = '$tipo',
                situacao = '$situacao',
                teve_evento = $teve_evento,
                nome_evento = '$nome_evento',
                motivo = '$motivo',
                anexos = '$anexosJson'
            WHERE id = $id";
    
    return mysqli_query($conn, $sql);
}

/**
 * Remove um evento
 */
function removerEventoCalendario($id) {
    global $conn;
    
    $id = (int)$id;
    
    // Buscar anexos para deletar os arquivos
    $sqlSelect = "SELECT anexos FROM calendario_eventos WHERE id = $id";
    $result = mysqli_query($conn, $sqlSelect);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        if (!empty($row['anexos'])) {
            $anexos = json_decode($row['anexos'], true);
            if (is_array($anexos)) {
                foreach ($anexos as $arquivo) {
                    if (file_exists($arquivo)) {
                        unlink($arquivo);
                    }
                }
            }
        }
    }
    
    $sql = "DELETE FROM calendario_eventos WHERE id = $id";
    return mysqli_query($conn, $sql);
}

/**
 * Busca eventos por data
 */
function getEventosPorData($data) {
    global $conn;
    
    $data = mysqli_real_escape_string($conn, $data);
    $eventos = [];
    
    $sql = "SELECT * FROM calendario_eventos WHERE data_evento = '$data' ORDER BY tipo";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $eventos[] = $row;
        }
    }
    
    return $eventos;
}
?>