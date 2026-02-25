<?php
require_once '../php/estoqueCounter.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon"
        href="../imagens/graduacao.ico">
    <title>Dashboard Escolar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/index.css">
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebarContent">
                <div class="logo">
                    <h1>Escola Jarbas Passarinho</h1>
                    <p>Painel de Controle</p>
                </div>

                <div class="menu-item active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </div>

                <a href="estoque.php"  class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>Controle de Estoque</span>
                </a>

                <a href="bens.php" class="menu-item">
                    <i class="fas fa-laptop"></i>
                    <span>Controle de Bens</span>
                </a>





           


            </div>

        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="welcome">
                    <h2>Bem-vindo, Administrador</h2>
                    <p>Segunda-feira, 15 de Maio de 2026</p>
                </div>


            </div>

            <h2 class="dashboard-title">Visão Geral</h2>

            <div class="cards">


                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Itens em Estoque</h3>
                        <div class="card-icon bg-orange">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    <div class="card-value">
                        <?php echo getEstoqueFormatado(); ?>
                    </div>

                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bens Patrimoniais</h3>
                        <div class="card-icon bg-green">
                            <i class="fas fa-laptop"></i>
                        </div>
                    </div>
                    <div class="card-value">
                        <p id="total-itens"></p>
                    </div>

                </div>
            </div>

            

            <h2 class="dashboard-title">Sistemas da Escola</h2>

            <div class="systems">
                <div class="system-card">
                    <div class="system-header estoque">
                        <i class="fas fa-boxes"></i>
                        <h3>Controle de Estoque</h3>
                    </div>
                    <div class="system-body">
                        <h4 class="system-title">Gerenciamento de Inventário</h4>
                        <p class="system-desc">Controle completo de entradas, saídas e níveis de estoque de materiais
                            de limpeza merenda escolar e outros insumos.</p>
                        <a data-tipo="estoque" class="btn">Acessar Sistema</a>
                    </div>
                </div>

                <div class="system-card">
                    <div class="system-header bens">
                        <i class="fas fa-laptop-house"></i>
                        <h3>Controle de Bens</h3>
                    </div>
                    <div class="system-body">
                        <h4 class="system-title">Patrimônio da Escola</h4>
                        <p class="system-desc">Sistema de controle e gestão de todos os bens permanentes da escola,
                            incluindo computadores, móveis, equipamentos e instrumentos.</p>
                        <a  data-tipo="bens" class="btn">Acessar Sistema</a>
                    </div>
                </div>

   <div class="system-card">
                    <div class="system-header diario">
                       <i class="fas fa-calendar-alt"></i>
                        <h3>Diário Escolar</h3>
                    </div>
                    <div class="system-body">
                        <h4 class="system-title">Gerenciamento de Imagens e arquivos</h4>
                        <p class="system-desc">Sistema de controle e gestão de relatórios diários da escola,
                            incluindo imagens, arquivos, eventos e ocorrências.</p>
                        <a  data-tipo="diario" class="btn">Acessar Sistema</a>
                    </div>
                </div>

            </div>


        </div>
    </div>

    <script src="../js/index.js"></script>
</body>

</html>