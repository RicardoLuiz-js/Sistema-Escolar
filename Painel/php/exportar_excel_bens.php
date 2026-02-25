<?php
require_once '../../Conexao/conexao.php';
require_once __DIR__ . '/SimpleXLSXGen.php';

use Shuchkin\SimpleXLSXGen;

// ===========================================
// ABA 1: MÓVEIS - PATRIMÔNIO COMPLETO
// ===========================================
$moveis = [];
$moveis[] = [
    'ID',
    'NOME DO BEM',
    'DESCRIÇÃO',
    'LOCALIZAÇÃO',
    'Nº TOMBO',
    'DATA CADASTRO',
    'STATUS'
];

$sql_moveis = "SELECT * FROM moveis WHERE excluido = 0 ORDER BY id DESC";
$result_moveis = $conn->query($sql_moveis);

if ($result_moveis && $result_moveis->num_rows > 0) {
    while($row = $result_moveis->fetch_assoc()) {
        
        // Verificar se tem movimentações
        $sql_check = "SELECT COUNT(*) as total FROM movimentacoes WHERE id_movel = " . $row['id'];
        $check = $conn->query($sql_check);
        $total_mov = $check->fetch_assoc()['total'];
        
        $status = $total_mov > 0 ? '✅ Com movimentações' : '🆕 Sem movimentações';
        
        $moveis[] = [
            $row['id'],
            $row['nome'],
            $row['descricao'] ?? 'Sem descrição',
            $row['localizacao'] ?? 'Não informado',
            $row['numero_tombo'] ?? 'Não tombado',
            date('d/m/Y', strtotime($row['data_cadastro'])),
            $status
        ];
    }
}

// ===========================================
// ABA 2: MOVIMENTAÇÕES - HISTÓRICO COMPLETO
// ===========================================
$movimentacoes = [];
$movimentacoes[] = [
    'ID',
    'BEM',
    'TOMBO',
    'TIPO DE AÇÃO',
    'DESCRIÇÃO',
    'LOCAL ANTERIOR',
    'LOCAL NOVO',
    'DATA/HORA',
    'DIAS ATRÁS'
];

$sql_mov = "SELECT m.*, mv.nome as nome_bem, mv.numero_tombo 
            FROM movimentacoes m 
            LEFT JOIN moveis mv ON m.id_movel = mv.id 
            WHERE mv.excluido = 0 OR mv.excluido IS NULL
            ORDER BY m.data_movimentacao DESC";
$result_mov = $conn->query($sql_mov);

if ($result_mov && $result_mov->num_rows > 0) {
    while($row = $result_mov->fetch_assoc()) {
        
        // Calcular dias desde a movimentação
        $data_mov = new DateTime($row['data_movimentacao']);
        $hoje = new DateTime();
        $dias = $data_mov->diff($hoje)->days;
        
        if ($dias == 0) {
            $dias_texto = 'Hoje';
        } elseif ($dias == 1) {
            $dias_texto = 'Ontem';
        } else {
            $dias_texto = $dias . ' dias atrás';
        }
        
        // Cor da ação
        $tipo_acao = $row['tipo_acao'] ?? 'Movimentação';
        if ($tipo_acao == 'entrada') {
            $tipo_acao = '📥 Entrada';
        } elseif ($tipo_acao == 'saida') {
            $tipo_acao = '📤 Saída';
        } elseif ($tipo_acao == 'transferencia') {
            $tipo_acao = '🔄 Transferência';
        } elseif ($tipo_acao == 'manutencao') {
            $tipo_acao = '🔧 Manutenção';
        } elseif ($tipo_acao == 'baixa') {
            $tipo_acao = '❌ Baixa';
        }
        
        $movimentacoes[] = [
            $row['id'],
            $row['nome_bem'] ?? 'Bem removido',
            $row['numero_tombo'] ?? 'Sem tombo',
            $tipo_acao,
            $row['descricao_acao'] ?? 'Sem descrição',
            $row['local_anterior'] ?? '-',
            $row['local_novo'] ?? '-',
            date('d/m/Y H:i:s', strtotime($row['data_movimentacao'])),
            $dias_texto
        ];
    }
}

// ===========================================
// ABA 3: BENS SEM MOVIMENTAÇÃO (PARADOS)
// ===========================================
$bens_parados = [];
$bens_parados[] = [
    'ID',
    'BEM',
    'TOMBO',
    'LOCALIZAÇÃO',
    'DATA CADASTRO',
    'DIAS SEM MOVIMENTAÇÃO',
    'STATUS'
];

$sql_parados = "SELECT m.*, 
                (SELECT MAX(data_movimentacao) FROM movimentacoes WHERE id_movel = m.id) as ultima_mov
                FROM moveis m 
                WHERE m.excluido = 0 
                ORDER BY ultima_mov ASC";
$result_parados = $conn->query($sql_parados);

if ($result_parados && $result_parados->num_rows > 0) {
    while($row = $result_parados->fetch_assoc()) {
        
        if ($row['ultima_mov']) {
            $ultima = new DateTime($row['ultima_mov']);
            $hoje = new DateTime();
            $dias_parado = $ultima->diff($hoje)->days;
        } else {
            $dias_parado = 999; // Nunca movimentado
        }
        
        // Classificar tempo parado
        if ($dias_parado >= 999) {
            $status = '🆕 NUNCA MOVIMENTADO';
            $dias_texto = 'Nunca';
        } elseif ($dias_parado > 180) {
            $status = '🔴 MAIS DE 6 MESES';
            $dias_texto = $dias_parado . ' dias';
        } elseif ($dias_parado > 90) {
            $status = '🟡 MAIS DE 3 MESES';
            $dias_texto = $dias_parado . ' dias';
        } elseif ($dias_parado > 30) {
            $status = '🟠 MAIS DE 1 MÊS';
            $dias_texto = $dias_parado . ' dias';
        } else {
            $status = '🟢 MOVIMENTADO RECENTEMENTE';
            $dias_texto = $dias_parado . ' dias';
        }
        
        $bens_parados[] = [
            $row['id'],
            $row['nome'],
            $row['numero_tombo'] ?? 'Sem tombo',
            $row['localizacao'] ?? 'Não informado',
            date('d/m/Y', strtotime($row['data_cadastro'])),
            $dias_texto,
            $status
        ];
    }
}

// ===========================================
// ABA 4: RESUMO POR LOCALIZAÇÃO
// ===========================================
$resumo_local = [];
$resumo_local[] = [
    'LOCALIZAÇÃO',
    'TOTAL DE BENS',
    'COM TOMBO',
    'SEM TOMBO',
    'ÚLTIMA MOVIMENTAÇÃO',
    'STATUS'
];

$sql_local = "SELECT 
                localizacao,
                COUNT(*) as total,
                SUM(CASE WHEN numero_tombo IS NOT NULL AND numero_tombo != '' THEN 1 ELSE 0 END) as com_tombo,
                MAX(data_cadastro) as ultimo_cadastro
              FROM moveis 
              WHERE excluido = 0 
              GROUP BY localizacao 
              ORDER BY total DESC";
$result_local = $conn->query($sql_local);

if ($result_local && $result_local->num_rows > 0) {
    while($row = $result_local->fetch_assoc()) {
        $local = $row['localizacao'] ?? 'Não informado';
        $sem_tombo = $row['total'] - $row['com_tombo'];
        
        $resumo_local[] = [
            $local,
            $row['total'],
            $row['com_tombo'],
            $sem_tombo,
            date('d/m/Y', strtotime($row['ultimo_cadastro'])),
            $row['total'] > 10 ? '✅ ACERVO GRANDE' : '📦 ACERVO PEQUENO'
        ];
    }
}

// ===========================================
// GERAR EXCEL COM 4 ABAS
// ===========================================

// Criar primeira aba (MÓVEIS)
$xlsx = SimpleXLSXGen::fromArray($moveis, 'BENS PATRIMONIAIS');

// Adicionar demais abas
$xlsx->addSheet($movimentacoes, 'HISTÓRICO DE MOVIMENTAÇÕES')
     ->addSheet($bens_parados, 'BENS SEM MOVIMENTAÇÃO')
     ->addSheet($resumo_local, 'RESUMO POR LOCAL');

// Ajustar largura das colunas
$xlsx->setColWidth(1, 8);   // ID
$xlsx->setColWidth(2, 40);  // Nome do bem
$xlsx->setColWidth(3, 50);  // Descrição
$xlsx->setColWidth(4, 25);  // Localização
$xlsx->setColWidth(5, 20);  // Tombo

// Nome do arquivo
$data_hora = date('d-m-Y_H-i');
$nome_arquivo = "Relatorio_Bens_Patrimoniais_{$data_hora}.xlsx";

// Download
$xlsx->downloadAs($nome_arquivo);
exit;
?>