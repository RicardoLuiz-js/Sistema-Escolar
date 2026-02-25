<?php
require_once '../php/calendario_funcoes.php';

// Capturar filtros
$filtro_data = $_GET['data'] ?? '';
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_busca = $_GET['busca'] ?? '';

// Buscar TODOS os eventos
$todosEventos = getEventosCalendario();

// Aplicar filtros manualmente
$eventosDB = array_filter($todosEventos, function($evento) use ($filtro_data, $filtro_tipo, $filtro_busca) {
    $match = true;
    
    // Filtrar por data
    if (!empty($filtro_data) && $evento['data'] !== $filtro_data) {
        $match = false;
    }
    
    // Filtrar por tipo
    if (!empty($filtro_tipo) && $evento['tipo'] !== $filtro_tipo) {
        $match = false;
    }
    
    // Filtrar por busca (título, descrição, nome do evento)
    if (!empty($filtro_busca) && $match) {
        $busca = strtolower($filtro_busca);
        $titulo = strtolower($evento['titulo'] ?? '');
        $descricao = strtolower($evento['descricao'] ?? '');
        $nomeEvento = strtolower($evento['nomeEvento'] ?? '');
        
        if (strpos($titulo, $busca) === false && 
            strpos($descricao, $busca) === false && 
            strpos($nomeEvento, $busca) === false) {
            $match = false;
        }
    }
    
    return $match;
});

// Reindexar array (opcional)
$eventosDB = array_values($eventosDB);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Diário Escolar</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/svg+xml" href="../imagens/icon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/calendario.css">
    <link rel="stylesheet" href="../css/relatorios.css">
</head>

<body>
    <header>
        <a href="index.php">
            <p>Diário Escolar</p>
        </a>
    </header>

    <main>
        <div id="mobile-menu-icon">&#9776;</div>

      <div id="Menu">
    <div class="MenuContainer">
        <div id="close-menu-icon">&#10005;</div> <!-- Botão de "X" para fechar o menu -->
        
        <a href="index.php">
            <div class="circle-container">
                 <div class="circle"><img class="icon" src="../imagens/home.png" alt=""></div>
                <p>Home</p>
            </div>
        </a>
        
        <a href="relatorios.php">
            <div class="circle-container">
                <div class="circle">
                    <i class="fas fa-file-alt fa-2x" style="color: #27ae60;"></i>
                </div>
                <p>Relatórios</p>
            </div>
        </a>
        
        <a href="imagens.php">
            <div class="circle-container">
                <div class="circle">
                    <i class="fas fa-images fa-2x" style="color: #e67e22;"></i>
                </div>
                <p>Imagens</p>
            </div>
        </a>
        
        <a href="arquivos.php">
            <div class="circle-container">
                <div class="circle">
                    <i class="fas fa-folder-open fa-2x" style="color: #f1c40f;"></i>
                </div>
                <p>Arquivos</p>
            </div>
        </a>

        <a href="limpar.php">
            <div class="circle-container">
                <div class="circle">
                    <i class="fas fa-trash-alt fa-2x" style="color: #e74c3c;"></i>
                </div>
                <p>Limpar Dados</p>
            </div>
        </a>
    </div>
</div>

        <div id="corpo">
            <div id="cabecalho">
                <p>Relatórios do Diário Escolar</p>
            </div>

            <div class="relatorios-container">
                <!-- Header com botão novo -->
                <div class="relatorios-header">
                    <h2>
                        <i class="fas fa-file-alt" style="color: #3498db;"></i>
                        Todos os Relatórios
                    </h2>
                    <button class="btn-novo-relatorio" onclick="window.location.href='index.php'">
                        <i class="fas fa-plus"></i>
                        Novo Relatório
                    </button>
                </div>

            

                <!-- Cards de estatísticas -->
                <?php
                $total = count($eventosDB);
                $eventos = array_filter($eventosDB, fn($e) => $e['tipo'] == 'evento');
                $aulas = array_filter($eventosDB, fn($e) => $e['tipo'] == 'aula');
                $suspensoes = array_filter($eventosDB, fn($e) => $e['tipo'] == 'suspensao');
                ?>
                
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total</h3>
                            <p><?php echo $total; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon evento">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Eventos</h3>
                            <p><?php echo count($eventos); ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon aula">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Aulas</h3>
                            <p><?php echo count($aulas); ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon suspensao">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Suspensões</h3>
                            <p><?php echo count($suspensoes); ?></p>
                        </div>
                    </div>
                </div>

    <!-- Seção de pesquisa avançada -->
                <div class="search-section">
                    <form method="GET" action="" id="searchForm">
                        <div class="search-grid">
                            <div class="search-item">
                                <label><i class="fas fa-calendar"></i> Data</label>
                                <input type="date" name="data" value="<?php echo htmlspecialchars($filtro_data); ?>">
                            </div>
                            
                            <div class="search-item">
                                <label><i class="fas fa-tag"></i> Tipo</label>
                                <select name="tipo" style="width: 234px; height: 49px; color:black;">
                                    <option value="">Todos os tipos</option>
                                    <option value="evento" <?php echo $filtro_tipo == 'evento' ? 'selected' : ''; ?>>Evento</option>
                                    <option value="aula" <?php echo $filtro_tipo == 'aula' ? 'selected' : ''; ?>>Aula</option>
                                    <option value="suspensao" <?php echo $filtro_tipo == 'suspensao' ? 'selected' : ''; ?>>Suspensão</option>
                                    <option value="escolar" <?php echo $filtro_tipo == 'escolar' ? 'selected' : ''; ?>>Escolar</option>
                                </select>
                            </div>
                            
                            <div class="search-item">
                                <label><i class="fas fa-search"></i> Buscar</label>
                                <input type="text" name="busca" placeholder="Título, descrição, evento..." value="<?php echo htmlspecialchars($filtro_busca); ?>">
                            </div>
                        </div>
                        
                        <div class="search-actions">
                            <button type="submit" class="btn-search">
                                <i class="fas fa-filter"></i>
                                Filtrar Resultados
                            </button>
                            <button type="button" class="btn-clear" onclick="limparFiltros()">
                                <i class="fas fa-times"></i>
                                Limpar Filtros
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabela de relatórios -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="relatoriosTableBody">
                            <?php if (empty($eventosDB)): ?>
                                <tr>
                                    <td colspan="5" class="no-results">
                                        <i class="fas fa-folder-open"></i>
                                        <p>Nenhum relatório encontrado</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($eventosDB as $evento): 
                                    $data = new DateTime($evento['data']);
                                    $dataFormatada = $data->format('d/m/Y');
                                    
                                    // Determinar classe do tipo
                                    $tipoClass = '';
                                    switch($evento['tipo']) {
                                        case 'evento': $tipoClass = 'tipo-evento'; break;
                                        case 'aula': $tipoClass = 'tipo-aula'; break;
                                        case 'suspensao': $tipoClass = 'tipo-suspensao'; break;
                                        default: $tipoClass = 'tipo-escolar';
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $dataFormatada; ?></td>
                                        <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                        <td>
                                            <span class="tipo-badge <?php echo $tipoClass; ?>">
                                                <?php echo ucfirst($evento['tipo']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($evento['descricao'], 0, 50)) . '...'; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-icon btn-view" onclick="visualizarRelatorio(<?php echo $evento['id']; ?>)" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-icon btn-edit" onclick="editarRelatorio(<?php echo $evento['id']; ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon btn-delete" onclick="excluirRelatorio(<?php echo $evento['id']; ?>)" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de visualização -->
    <div class="view-modal" id="viewModal">
        <div class="view-modal-content">
            <div class="view-modal-header">
                <h3><i class="fas fa-file-alt"></i> Detalhes do Relatório</h3>
                <button class="close-view" onclick="fecharVisualizacao()">&times;</button>
            </div>
            <div class="view-modal-body" id="viewModalBody">
                <!-- Preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <div id="message-box"></div>

    <script>
        let eventos = <?php echo json_encode($eventosDB); ?>;
    </script>
    <script src="../js/relatorios.js"></script>
</body>
</html>