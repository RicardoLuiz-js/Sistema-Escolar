<?php
// html/imagens.php
require_once '../php/imagens_funcoes.php';

// Pasta selecionada (via GET)
$pastaSelecionada = $_GET['pasta'] ?? '';
$busca = $_GET['busca'] ?? '';

// Escanear imagens
$imagensEncontradas = escanearImagens($pastaSelecionada);

// Filtrar por busca
if (!empty($busca)) {
    $imagensEncontradas = array_filter($imagensEncontradas, function($img) use ($busca) {
        return stripos($img['nome'], $busca) !== false;
    });
}

// Pegar todas as pastas disponíveis
$pastasDisponiveis = getPastasDisponiveis();

// Determinar pasta atual para exibição
$pastaAtual = !empty($pastaSelecionada) ? $pastaSelecionada : 'Todas as pastas';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imagens - Diário Escolar</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/imagens.css">
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
                <div id="close-menu-icon">&#10005;</div>
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
                <p>Galeria de Imagens</p>
            </div>

            <div class="imagens-container">
                <!-- Header -->
                <div class="imagens-header">
                    <h2>
                        <i class="fas fa-images"></i>
                        Galeria de Imagens
                    </h2>
                    
                    <div class="header-controls">
                        <!-- Seletor de pasta -->
                        <div class="pasta-selector">
                            <i class="fas fa-folder"></i>
                            <select name="pasta" id="pastaSelect" onchange="mudarPasta()" style="color:black; height:45px">
                                <option value=""> Todas as pastas</option>
                                <?php foreach ($pastasDisponiveis as $pasta): ?>
                                    <option value="<?php echo $pasta['caminho']; ?>" 
                                            <?php echo $pastaSelecionada == $pasta['caminho'] ? 'selected' : ''; ?>
                                            <?php echo !$pasta['temImagem'] ? 'disabled' : ''; ?>>
                                        <?php echo $pasta['label']; ?> 
                                        (<?php echo $pasta['caminho']; ?>)
                                        <?php echo !$pasta['temImagem'] ? '- (vazia)' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Busca -->
                        <form method="GET" class="search-box">
                            <input type="hidden" name="pasta" value="<?php echo htmlspecialchars($pastaSelecionada); ?>">
                            <input type="text" name="busca" placeholder="Buscar imagens..." value="<?php echo htmlspecialchars($busca); ?>">
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
                    <?php
                    $totalImagens = count($imagensEncontradas);
                    $tamanhoTotal = array_sum(array_column($imagensEncontradas, 'tamanho'));
                    $tamanhoFormatado = formatarTamanho($tamanhoTotal);
                    ?>
                    
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Imagens</h3>
                            <p><?php echo $totalImagens; ?></p>
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
                            <small>com imagens</small>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon calendario">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Mês atual</h3>
                            <p><?php echo date('m/Y'); ?></p>
                            <small><?php echo getNomeMes(date('m')); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Galeria -->
                <div class="galeria-grid">
                    <?php if (empty($imagensEncontradas)): ?>
                        <div class="no-results">
                            <i class="fas fa-images"></i>
                            <h3>Nenhuma imagem encontrada</h3>
                            <p>As imagens aparecerão aqui quando você anexá-las aos relatórios.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($imagensEncontradas as $imagem): 
                            $tamanhoArquivo = formatarTamanho($imagem['tamanho']);
                            $extensao = strtoupper(pathinfo($imagem['nome'], PATHINFO_EXTENSION));
                        ?>
                            <div class="imagem-card" onclick="abrirImagem('<?php echo $imagem['caminho']; ?>', '<?php echo addslashes($imagem['nome']); ?>')">
                                <div class="imagem-preview-container">
                                    <img src="../<?php echo $imagem['caminho']; ?>" class="imagem-preview" alt="<?php echo $imagem['nome']; ?>">
                                    <div class="imagem-overlay">
                                        <i class="fas fa-eye"></i> Clique para ampliar
                                    </div>
                                    <button class="btn-caminho tooltip" data-tooltip="Ver caminho completo" onclick="event.stopPropagation(); copiarCaminho('<?php echo $imagem['caminho']; ?>')">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                                <div class="imagem-info">
                                    <div class="imagem-nome" title="<?php echo $imagem['nome']; ?>">
                                        <?php echo $imagem['nome']; ?>
                                    </div>
                                    <div class="imagem-metadata">
                                        <span><i class="far fa-file"></i> <?php echo $extensao; ?></span>
                                        <span><i class="fas fa-weight-hanging"></i> <?php echo $tamanhoArquivo; ?></span>
                                    </div>
                                    <div class="imagem-acoes">
                                        <button class="btn-imagem btn-view" onclick="event.stopPropagation(); abrirImagem('<?php echo $imagem['caminho']; ?>', '<?php echo addslashes($imagem['nome']); ?>')">
                                            <i class="fas fa-eye"></i> Visualizar
                                        </button>
                                        <a href="../<?php echo $imagem['caminho']; ?>" download class="btn-imagem btn-download" onclick="event.stopPropagation();">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="imagem-modal" id="imagemModal">
        <div class="imagem-modal-content">
            <button class="close-modal" onclick="fecharModal()">&times;</button>
            <img id="modalImagem" src="" alt="">
            <div class="modal-info">
                <span id="modalNome"></span>
                <a id="modalDownload" href="#" download class="modal-download">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>

    <div id="message-box"></div>

    <!-- JavaScript -->
    <script src="../js/imagens.js"></script>
</body>
</html>