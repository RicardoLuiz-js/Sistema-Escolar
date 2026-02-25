<!-- estoqueCounter.php -->
<?php
require_once '../../Conexao/conexao.php';

function getEstoqueTotal() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COALESCE(SUM(quantidade), 0) as total FROM produtos");
    return (int)mysqli_fetch_assoc($result)['total'];
}

function getEstoqueFormatado() {
    return number_format(getEstoqueTotal(), 0, ',', '.');
}
?>