<?php
require_once '../../Conexao/conexao.php';
require_once __DIR__ . '/SimpleXLSXGen.php';  // Mesma pasta deste arquivo



use Shuchkin\SimpleXLSXGen;

// ===========================================
// ABA 1: PRODUTOS
// ===========================================
$produtos = [];
$produtos[] = [
    'ID', 
    'NOME DO PRODUTO', 
    'DESCRIÇÃO', 
    'CATEGORIA', 
    'QUANTIDADE', 
    'UNIDADE', 
    'DATA DE ENTRADA', 
    'VALIDADE'
];

$sql_produtos = "SELECT * FROM produtos ORDER BY id DESC";
$result_produtos = $conn->query($sql_produtos);

if ($result_produtos && $result_produtos->num_rows > 0) {
    while($row = $result_produtos->fetch_assoc()) {
        $produtos[] = [
            $row['id'],
            $row['nome'],
            $row['descricao'],
            $row['categoria'],
            $row['quantidade'],
            $row['unidade'],
            date('d/m/Y', strtotime($row['entrada'])),
            date('d/m/Y', strtotime($row['validade']))
        ];
    }
}

// ===========================================
// ABA 2: UTILIZADOS
// ===========================================
$utilizados = [];
$utilizados[] = [
    'ID', 
    'ID DO PRODUTO', 
    'NOME DO PRODUTO', 
    'QUANTIDADE UTILIZADA', 
    'RESPONSÁVEL', 
    'DATA DE SAÍDA', 
    'DATA DO REGISTRO'
];

$sql_utilizados = "SELECT u.*, p.nome as nome_produto 
                   FROM utilizados u 
                   LEFT JOIN produtos p ON u.produto_id = p.id 
                   ORDER BY u.data_saida DESC";
$result_utilizados = $conn->query($sql_utilizados);

if ($result_utilizados && $result_utilizados->num_rows > 0) {
    while($row = $result_utilizados->fetch_assoc()) {
        $utilizados[] = [
            $row['id'],
            $row['produto_id'],
            $row['nome_produto'] ?? 'Produto não encontrado',
            $row['quantidade_utilizada'],
            $row['responsavel'],
            date('d/m/Y', strtotime($row['data_saida'])),
            date('d/m/Y H:i:s', strtotime($row['data_registro']))
        ];
    }
}

// ===========================================
// CRIAR EXCEL COM 2 ABAS - CORREÇÃO AQUI!
// ===========================================

// CRIA a primeira aba (PRODUTOS)
$xlsx = SimpleXLSXGen::fromArray($produtos, 'PRODUTOS');

// ADICIONA a segunda aba (UTILIZADOS)
$xlsx->addSheet($utilizados, 'UTILIZADOS');

// Nome do arquivo
$data_hora = date('d-m-Y_H-i');
$nome_arquivo = "Relatorio_Completo_{$data_hora}.xlsx";

// Download
$xlsx->downloadAs($nome_arquivo);
exit;
?>