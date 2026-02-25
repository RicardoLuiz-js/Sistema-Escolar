<?php
// html/limpar.php
require_once '../php/limpar_funcoes.php';

// Estatísticas
$totalImagens = contarImagens();
$totalArquivos = contarArquivos();
$tamanhoTotal = calcularTamanhoTotal();
$tamanhoFormatado = formatarTamanho($tamanhoTotal);
$backups = listarBackups();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpar Arquivos - Diário Escolar</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/limpar.css">
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
                <p> Limpeza de Arquivos</p>
            </div>

            <div class="limpar-container">
                <!-- Header de aviso -->
                <div class="limpar-header">
                    <h2>
                        <i class="fas fa-exclamation-triangle"></i>
                        Atenção!
                    </h2>
                    <p>Esta área permite excluir imagens e arquivos. Os relatórios do banco de dados NÃO serão afetados.</p>
                    <div class="aviso-seguranca">
                        <i class="fas fa-shield-alt"></i>
                        <span>Senha necessária</span>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon imagens">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Imagens</h3>
                            <p><?php echo $totalImagens; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon arquivos">
                            <i class="fas fa-file"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Arquivos</h3>
                            <p><?php echo $totalArquivos; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon tamanho">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Espaço Ocupado</h3>
                            <p><?php echo $tamanhoFormatado; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total de Itens</h3>
                            <p><?php echo $totalImagens + $totalArquivos; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Seção de Backup -->
                <div class="backup-section">
                    <h3>
                        <i class="fas fa-archive"></i>
                        Backup de Segurança
                    </h3>
                    
                    <div class="backup-options">
                        <div class="backup-card selected" data-backup="tudo" onclick="selecionarBackupTipo('tudo')">
                            <i class="fas fa-database"></i>
                            <h4>Backup Completo</h4>
                            <p>Imagens + Arquivos</p>
                        </div>
                        
                        <div class="backup-card" data-backup="imagens" onclick="selecionarBackupTipo('imagens')">
                            <i class="fas fa-images"></i>
                            <h4>Só Imagens</h4>
                            <p>Apenas arquivos de imagem</p>
                        </div>
                        
                        <div class="backup-card" data-backup="arquivos" onclick="selecionarBackupTipo('arquivos')">
                            <i class="fas fa-file"></i>
                            <h4>Só Arquivos</h4>
                            <p>PDF, DOC, etc.</p>
                        </div>
                    </div>
                    
                    <button class="btn-limpar" onclick="criarBackup()" style="background: #27ae60; margin-bottom: 30px;">
                        <i class="fas fa-archive"></i>
                        Criar Backup Agora
                    </button>
                    
                    <?php if (!empty($backups)): ?>
                        <div class="backups-lista">
                            <div class="backups-header">
                                <span>Backups Disponíveis</span>
                                <span>Ações</span>
                            </div>
                            
                            <?php foreach ($backups as $backup): ?>
                                <div class="backup-item">
                                    <div class="backup-info">
                                        <i class="fas fa-file-zipper"></i>
                                        <div class="backup-details">
                                            <h4><?php echo $backup['nome']; ?></h4>
                                            <p><?php echo $backup['data']; ?> • <?php echo $backup['tamanho']; ?></p>
                                        </div>
                                    </div>
                                    <div class="backup-acoes">
                                        <button class="btn-backup btn-download-backup" onclick="baixarBackup('<?php echo $backup['nome']; ?>')" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn-backup btn-delete-backup" onclick="excluirBackup('<?php echo $backup['nome']; ?>')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Seção de Limpeza -->
                <div class="limpeza-section">
                    <h3>
                        <i class="fas fa-trash-alt"></i>
                        Limpar Arquivos
                    </h3>
                    
                    <!-- Senha -->
                    <div class="senha-container">
                        <label>
                            <i class="fas fa-lock"></i>
                            Senha:
                        </label>
                        <input type="password" id="senhaInput" placeholder="Digite a senha">
                        <div class="senha-info" id="senhaStatus">
                            <i class="fas fa-info-circle"></i>
                         
                        </div>
                    </div>
                    
                    <!-- Opções -->
                    <div class="opcoes-grid">
                        <div class="opcao-card imagens" data-tipo="imagens" onclick="selecionarTipo('imagens')">
                            <i class="fas fa-images"></i>
                            <h4>Imagens</h4>
                            <p>Excluir todas as imagens</p>
                            <span class="badge"><?php echo $totalImagens; ?> imagens</span>
                        </div>
                        
                        <div class="opcao-card arquivos" data-tipo="arquivos" onclick="selecionarTipo('arquivos')">
                            <i class="fas fa-file"></i>
                            <h4>Arquivos</h4>
                            <p>Excluir PDF, DOC, etc.</p>
                            <span class="badge"><?php echo $totalArquivos; ?> arquivos</span>
                        </div>
                        
                        <div class="opcao-card tudo" data-tipo="tudo" onclick="selecionarTipo('tudo')">
                            <i class="fas fa-trash-alt"></i>
                            <h4>Tudo</h4>
                            <p>Excluir imagens e arquivos</p>
                            <span class="badge"><?php echo $totalImagens + $totalArquivos; ?> itens</span>
                        </div>
                    </div>
                    
                    <!-- Botão de limpeza -->
                    <div class="btn-limpar-container">
                        <button class="btn-limpar" id="btnLimpar" onclick="mostrarConfirmacao()" disabled>
                            <i class="fas fa-exclamation-triangle"></i>
                            Executar Limpeza
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de confirmação -->
    <div class="confirm-modal" id="confirmModal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Confirmar Limpeza</h3>
            </div>
            <div class="confirm-modal-body">
                <i class="fas fa-trash-alt"></i>
                <h3>Tem certeza?</h3>
                <p id="confirmText">Esta ação não poderá ser desfeita!</p>
            </div>
            <div class="confirm-modal-footer">
                <button class="btn-confirm btn-confirm-yes" onclick="executarLimpeza()">
                    <i class="fas fa-check"></i>
                    Sim, executar
                </button>
                <button class="btn-confirm btn-confirm-no" onclick="fecharConfirmacao()">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de resultado -->
    <div class="result-modal" id="resultModal">
        <div class="result-modal-content">
            <div class="result-modal-header sucesso">
                <i class="fas fa-check-circle"></i>
                <h3>Limpeza Concluída</h3>
            </div>
            <div class="result-modal-body" id="resultBody">
                <!-- Resultado via JS -->
            </div>
            <div class="result-footer">
                <button class="btn-fechar" onclick="fecharResultado()">
                    <i class="fas fa-check"></i>
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text" id="loadingText">Processando...</div>
    </div>

    <!-- Mensagem flutuante -->
    <div id="message-box"></div>

    <!-- JavaScript -->
    <script src="../js/limpar.js"></script>
</body>
</html>