<?php
require_once '../../../Conexao/conexao.php';

$sql = "SELECT DISTINCT localizacao FROM moveis WHERE excluido = 0 ORDER BY localizacao";
$result = $conn->query($sql);

$locais = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $locais[] = $row['localizacao'];
    }
}

header('Content-Type: application/json');
echo json_encode($locais, JSON_UNESCAPED_UNICODE);
?>