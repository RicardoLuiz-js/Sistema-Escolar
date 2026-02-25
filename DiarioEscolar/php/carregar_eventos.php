<?php
// carregar_eventos.php
require_once 'calendario_funcoes.php';
header('Content-Type: application/json');

$eventos = getEventosCalendario();
echo json_encode($eventos);