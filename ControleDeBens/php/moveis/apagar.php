<?php
require_once '../../../Conexao/conexao.php';

// Verificar qual tabela deve ser apagada
if (isset($_GET['tabela'])) {
    $tabela = $_GET['tabela'];

    // Verificar se a tabela é válida
    if ($tabela == "movimentacoes") {
        // Query para apagar todos os dados da tabela
        $sql = "TRUNCATE TABLE $tabela";

        if ($conn->query($sql) === TRUE) {
            echo "Todos os dados da tabela " . ucfirst($tabela) . " foram apagados com sucesso.";
        } else {
            echo "Erro ao apagar dados: " . $conn->error;
        }
    } else {
        echo "Tabela inválida.";
    }
} else {
    echo "Nenhuma tabela especificada.";
}

// Fechar conexão
$conn->close();
?>