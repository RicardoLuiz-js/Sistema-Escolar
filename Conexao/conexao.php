<?php
// php/conexao.php
$servername = "localhost";
$username = "root";
$password = "jb40028922";
$dbname = "sistema_escolar";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Configurar charset para suportar acentos e caracteres especiais
$conn->set_charset("utf8mb4");

// Retornar a conexão para ser usada em outros arquivos
return $conn;
?>