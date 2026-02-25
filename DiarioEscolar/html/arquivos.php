<?php
// html/arquivos.php
require_once '../php/arquivos_funcoes.php';

// Pasta selecionada (via GET)
$pastaSelecionada = $_GET['pasta'] ?? '';
$busca = $_GET['busca'] ?? '';

// Escanear arquivos
$arquivosEncontrados = escanearArquivos($pastaSelecionada);

// Filtrar por busca
if (!empty($busca)) {
    $arquivosEncontrados = array_filter($arquivosEncontrados, function($arquivo) use ($busca) {
        return stripos($arquivo['nome'], $busca) !== false;
    });
}

// Agrupar por tipo
$arquivosPorTipo = agruparPorTipo($arquivosEncontrados);

// Pegar todas as pastas disponíveis
$pastasDisponiveis = getPastasDisponiveisArquivos();

// Determinar pasta atual para exibição
$pastaAtual = !empty($pastaSelecionada) ? $pastaSelecionada : 'Todas as pastas';

// Estatísticas
$totalArquivos = count($arquivosEncontrados);
$tamanhoTotal = array_sum(array_column($arquivosEncontrados, 'tamanho'));
$tamanhoFormatado = formatarTamanho($tamanhoTotal);
$totalTipos = count($arquivosPorTipo);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arquivos - Diário Escolar</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/arquivos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="../imagens/icon.svg">
</head>
<body>
    <header>
        <a href="index.php">
            <p>Diário Escolar</p>
        </a>
    </header>

    <main>
        <div id="mobile-menu-icon">&#9776;</div>

        <!-- Menu -->
        <div id="Menu">
            <div class="MenuContainer">
          
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
                <p> Gerenciador de Arquivos</p>
            </div>

            <div class="arquivos-container">
                <!-- Header -->
                <div class="arquivos-header">
                    <h2>
                        <i class="fas fa-folder-open"></i>
                        Todos os Arquivos
                    </h2>
                    
                    <div class="header-controls">
                        <!-- Seletor de pasta -->
                        <div class="pasta-selector">
                            <i class="fas fa-folder"></i>
                            <select name="pasta" id="pastaSelect" onchange="mudarPasta()" style="color:black; height:45px">
                                <option value="">Todas as pastas</option>
                                <?php foreach ($pastasDisponiveis as $pasta): ?>
                                    <option value="<?php echo $pasta['caminho']; ?>" 
                                            <?php echo $pastaSelecionada == $pasta['caminho'] ? 'selected' : ''; ?>
                                            <?php echo !$pasta['temArquivo'] ? 'disabled' : ''; ?>>
                                        <?php echo $pasta['label']; ?> 
                                        (<?php echo $pasta['caminho']; ?>)
                                        <?php echo !$pasta['temArquivo'] ? '- (vazio)' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Busca -->
                        <form method="GET" class="search-box">
                            <input type="hidden" name="pasta" value="<?php echo htmlspecialchars($pastaSelecionada); ?>">
                            <input type="text" name="busca" placeholder="Buscar arquivos..." value="<?php echo htmlspecialchars($busca); ?>">
                            <button type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <i class="fas fa-folder-open"></i>
                    <a href="?pasta=">Todas as pastas</a>
                    
                    <?php if (!empty($pastaSelecionada)): 
                        $partes = explode('/', trim($pastaSelecionada, '/'));
                        $caminho = '';
                        foreach ($partes as $parte) {
                            if ($parte == 'uploads' || $parte == 'relatorios') continue;
                            $caminho .= '/' . $parte;
                            ?>
                            <span>/</span>
                            <a href="?pasta=uploads/relatorios<?php echo $caminho; ?>/">
                                <?php echo is_numeric($parte) && strlen($parte) == 2 ? getNomeMes($parte) : $parte; ?>
                            </a>
                        <?php } ?>
                    <?php endif; ?>
                </div>

                <!-- Estatísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-file"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Arquivos</h3>
                            <p><?php echo $totalArquivos; ?></p>
                            <small><?php echo $pastaAtual; ?></small>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon tamanho">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Tamanho Total</h3>
                            <p><?php echo $tamanhoFormatado; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pasta">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Pastas</h3>
                            <p><?php echo count($pastasDisponiveis); ?></p>
                            <small>com arquivos</small>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon tipos">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Tipos</h3>
                            <p><?php echo $totalTipos; ?></p>
                            <small>categorias</small>
                        </div>
                    </div>
                </div>

                <!-- Abas de categorias -->
                <div class="categorias-tabs">
                    <button class="categoria-tab active" data-categoria="todos" onclick="filtrarCategoria('todos')">
                        <i class="fas fa-th-large"></i>
                        Todos
                        <span class="count"><?php echo $totalArquivos; ?></span>
                    </button>
                    
                    <?php foreach ($arquivosPorTipo as $tipo => $grupo): ?>
                        <button class="categoria-tab" data-categoria="<?php echo $tipo; ?>" onclick="filtrarCategoria('<?php echo $tipo; ?>')">
                            <i class="fas <?php echo $grupo['icone']; ?>"></i>
                            <?php echo $grupo['nome']; ?>
                            <span class="count"><?php echo count($grupo['arquivos']); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Visualização por categorias (padrão) -->
                <?php if (!empty($arquivosPorTipo)): ?>
                    <div class="categorias-grid">
                        <?php foreach ($arquivosPorTipo as $tipo => $grupo): ?>
                            <div class="categoria-card">
                                <div class="categoria-header">
                                    <div class="categoria-icone" style="background: <?php echo $grupo['cor']; ?>">
                                        <i class="fas <?php echo $grupo['icone']; ?>"></i>
                                    </div>
                                    <div class="categoria-info">
                                        <h3><?php echo $grupo['nome']; ?></h3>
                                        <p><?php echo count($grupo['arquivos']); ?> arquivo(s)</p>
                                    </div>
                                </div>
                                
                                <div class="categoria-arquivos">
                                    <?php foreach (array_slice($grupo['arquivos'], 0, 5) as $arquivo): ?>
                                        <div class="categoria-arquivo-item" onclick="abrirDetalhesArquivo({
                                            nome: '<?php echo addslashes($arquivo['nome']); ?>',
                                            extensao: '<?php echo $arquivo['extensao']; ?>',
                                            tamanho: '<?php echo formatarTamanho($arquivo['tamanho']); ?>',
                                            data: '<?php echo formatarDataArquivo($arquivo['data_modificacao']); ?>',
                                            caminho: '<?php echo $arquivo['caminho']; ?>'
                                        })">
                                            <i class="fas <?php echo getIconeArquivo($arquivo['extensao']); ?>" style="color: <?php echo getCorArquivo($arquivo['extensao']); ?>"></i>
                                            <div class="categoria-arquivo-info">
                                                <div class="categoria-arquivo-nome"><?php echo $arquivo['nome']; ?></div>
                                                <div class="categoria-arquivo-meta"><?php echo formatarTamanho($arquivo['tamanho']); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($grupo['arquivos']) > 5): ?>
                                        <div style="text-align: center; padding: 10px; color: #7f8c8d; font-size: 12px;">
                                            + <?php echo count($grupo['arquivos']) - 5; ?> arquivos
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Visualização em lista (quando filtra por categoria) -->
                <div class="arquivos-lista" style="display: none;">
                    <?php if (empty($arquivosEncontrados)): ?>
                        <div class="no-results">
                            <i class="fas fa-folder-open"></i>
                            <h3>Nenhum arquivo encontrado</h3>
                            <p>Os arquivos aparecerão aqui quando você anexá-los aos relatórios.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($arquivosEncontrados as $arquivo): 
                            $extensao = $arquivo['extensao'];
                            $icone = getIconeArquivo($extensao);
                            $cor = getCorArquivo($extensao);
                            $tamanhoFormatado = formatarTamanho($arquivo['tamanho']);
                            $dataFormatada = formatarDataArquivo($arquivo['data_modificacao']);
                            
                            // Determinar categoria para o filtro
                            $categoria = 'outros';
                            if (in_array($extensao, ['pdf'])) $categoria = 'pdf';
                            elseif (in_array($extensao, ['doc', 'docx'])) $categoria = 'word';
                            elseif (in_array($extensao, ['xls', 'xlsx', 'csv'])) $categoria = 'excel';
                            elseif (in_array($extensao, ['ppt', 'pptx'])) $categoria = 'powerpoint';
                            elseif (in_array($extensao, ['txt', 'rtf', 'md'])) $categoria = 'texto';
                            elseif (in_array($extensao, ['zip', 'rar', '7z'])) $categoria = 'compactado';
                            elseif (in_array($extensao, ['mp3', 'wav'])) $categoria = 'audio';
                            elseif (in_array($extensao, ['mp4', 'avi'])) $categoria = 'video';
                        ?>
                            <div class="arquivo-item" data-categoria="<?php echo $categoria; ?>" onclick="abrirDetalhesArquivo({
                                nome: '<?php echo addslashes($arquivo['nome']); ?>',
                                extensao: '<?php echo $extensao; ?>',
                                tamanho: '<?php echo $tamanhoFormatado; ?>',
                                data: '<?php echo $dataFormatada; ?>',
                                caminho: '<?php echo $arquivo['caminho']; ?>'
                            })">
                                <div class="arquivo-icone" style="background: <?php echo $cor; ?>">
                                    <i class="fas <?php echo $icone; ?>"></i>
                                </div>
                                
                                <div class="arquivo-info">
                                    <div class="arquivo-nome"><?php echo $arquivo['nome']; ?></div>
                                    <div class="arquivo-metadata">
                                        <span><i class="far fa-file"></i> <?php echo strtoupper($extensao); ?></span>
                                        <span><i class="fas fa-weight-hanging"></i> <?php echo $tamanhoFormatado; ?></span>
                                        <span><i class="far fa-calendar"></i> <?php echo $dataFormatada; ?></span>
                                    </div>
                                </div>
                                
                                <div class="arquivo-acoes">
                                    <a href="../<?php echo $arquivo['caminho']; ?>" download class="btn-arquivo btn-download tooltip" data-tooltip="Download" onclick="event.stopPropagation()">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn-arquivo btn-info tooltip" data-tooltip="Detalhes" onclick="event.stopPropagation(); abrirDetalhesArquivo({
                                        nome: '<?php echo addslashes($arquivo['nome']); ?>',
                                        extensao: '<?php echo $extensao; ?>',
                                        tamanho: '<?php echo $tamanhoFormatado; ?>',
                                        data: '<?php echo $dataFormatada; ?>',
                                        caminho: '<?php echo $arquivo['caminho']; ?>'
                                    })">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de detalhes -->
    <div class="arquivo-modal" id="arquivoModal">
        <div class="arquivo-modal-content">
            <div class="arquivo-modal-header">
                <h3><i class="fas fa-file"></i> Detalhes do Arquivo</h3>
                <button class="close-modal" onclick="fecharModal()">&times;</button>
            </div>
            <div class="arquivo-modal-body" id="modalBody">
                <!-- Preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <div id="message-box"></div>

    <!-- JavaScript -->
    <script src="../js/arquivos.js"></script>
</body>
</html>