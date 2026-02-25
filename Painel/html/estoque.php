<?php
require_once '../php/estoqueCounter.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='15' fill='%231a237e'/><text x='50' y='70' font-family='Arial, sans-serif' font-size='60' text-anchor='middle' fill='white'>JP</text></svg>">
    <title>Dashboard Escolar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

                <a href="Index.php" class="menu-item ">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <div class="menu-item active">
                    <i class="fas fa-box"></i>
                    <span>Controle de Estoque</span>
                </div>

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
                    <h2><i class="fas fa-box"></i> Controle de Estoque</h2>
                    <p>Gerencie eventos, reuniões e atividades importantes</p>
                </div>

                <button class="buttonExport" onclick=" window.print();">
                  <h2> <i class="  fa-solid fa-file-pdf"></i></h2>  <p>Exportar PDF</p>
                </button>

                <a href="../php/exportar_excel.php"><button class="buttonExport">
                       <h2> <i class="fa-solid fa-file-excel"></i> </h2><p>Exportar Excel</p>
                    </button></a>



            </div>

            <h2 class="dashboard-title">Estatísticas</h2>

            <div class="cardsEstoque">



                <div class="card-header">
                    <h3 class="card-title">Inventário Atual</h3>
                    <div class="card-icon bg-blue">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
                <div class="card-value">
                    <canvas id="inventarioChart" width="400" height="200"></canvas>
                </div>




                <div class="card-header">
                    <h3 class="card-title">Quantidades</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-table"></i>

                    </div>
                </div>
                <div class="card-value">
                    <table class="vp"
                        style="display: grid; grid-template-columns: repeat(3, 1fr); /* 3 colunas iguais */ gap: 20px; font-size: medium;  "
                        id="tabelaInventario"></table>
                </div>




                <!-- Relatório de Produtos Mais Consumidos -->
                <div class="card-header">
                    <h3 class="card-title">Produtos Mais Consumidos</h3>

                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-chart-bar"></i>

                    </div>

                </div>

                <canvas id="consumidosChart" width="400" height="200"></canvas>




                <!-- Pessoas que Mais Utilizaram Produtos -->
                <div class="card-header">
                    <h3 class="card-title">Pessoas que Mais Utilizaram Produtos</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-chart-bar"></i>

                    </div>
                </div>

                <canvas id="pessoasChart" width="400" height="200"></canvas>




                <!-- Relatório de Movimentação de Estoque -->
                <div class="card-header">
                    <h3 class="card-title">Movimentação de Estoque</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-table"></i>

                    </div>
                </div>
                <div class="card-value">
                    <table class="vp"
                        style="display: grid; grid-template-columns: repeat(3, 1fr); /* 3 colunas iguais */ gap: 20px; font-size: medium;  "
                        id="tabelaMovimentacao"></table>

                </div>


                <!-- Relatório de Produtos Obsoletos -->
                <div class="card-header">
                    <h3 class="card-title">Produtos Obsoletos</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-table"></i>

                    </div>
                </div>

                <table class="vp" style="margin-top: 30%;" id="tabelaObsoletos"></table>


                <!-- Relatório de Reposição de Estoque -->
                <div class="card-header">
                    <h3 class="card-title">Reposição de Estoque</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-table"></i>

                    </div>
                </div>

                <table class="vp" style="margin-top: 30%;" id="tabelaReposicao"></table>


                <!-- Relatório de Validade de Produtos -->
                <div class="card-header">
                    <h3 class="card-title">Validade de Produtos</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-table"></i>

                    </div>
                </div>

                <table class="vp"
                    style="display: grid; grid-template-columns: repeat(3, 1fr); /* 3 colunas iguais */ gap: 20px; font-size: medium;  "
                    id="tabelaValidade"></table>








            </div>



        </div>
    </div>

    <script src="../js/index.js"></script>
    <script>
        function formatarData(data) {
            return new Date(data).toLocaleDateString('pt-BR');
        }




        // Relatório de Inventário Atual
        fetch('../../ControleDeEstoque/php/buscar_produtos.php')
            .then(response => response.json())
            .then(data => {
                const categorias = {};
                data.forEach(produto => {
                    if (categorias[produto.categoria]) {
                        categorias[produto.categoria] += parseInt(produto.quantidade);
                    } else {
                        categorias[produto.categoria] = parseInt(produto.quantidade);
                    }
                });

                const ctx = document.getElementById('inventarioChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(categorias),
                        datasets: [{
                            label: 'Quantidade em Estoque',
                            data: Object.values(categorias),
                            backgroundColor: 'rgba(26, 35, 126, 0.7)',
                            borderColor: 'rgba(26, 35, 126, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Tabela de valores
                const tbody = document.getElementById('tabelaInventario');
                tbody.innerHTML = '';
                data.forEach(produto => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td>${produto.nome}</td>
            <td>${produto.quantidade}</td>
            
          `;
                    tbody.appendChild(row);
                });
            });

        // Relatório de Movimentação de Estoque
        fetch('../../ControleDeEstoque/php/buscar_utilizados.php')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('tabelaMovimentacao');
                tbody.innerHTML = '';
                data.forEach(movimentacao => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td>${movimentacao.nome_produto}</td>
            <td>${movimentacao.quantidade_utilizada}</td>
            <td>${formatarData(movimentacao.data_saida)}</td>
            
           
          `;
                    tbody.appendChild(row);
                });
            });

        // Relatório de Produtos Mais Consumidos
        fetch('../../ControleDeEstoque/php/buscar_utilizados.php')
            .then(response => response.json())
            .then(data => {
                const produtosConsumidos = {};
                data.forEach(movimentacao => {
                    if (produtosConsumidos[movimentacao.nome_produto]) {
                        produtosConsumidos[movimentacao.nome_produto] += parseInt(movimentacao.quantidade_utilizada);
                    } else {
                        produtosConsumidos[movimentacao.nome_produto] = parseInt(movimentacao.quantidade_utilizada);
                    }
                });

                const ctx = document.getElementById('consumidosChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(produtosConsumidos),
                        datasets: [{
                            label: 'Quantidade Consumida',
                            data: Object.values(produtosConsumidos),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });

        // Relatório de Produtos Obsoletos
        fetch('../../ControleDeEstoque/php/buscar_utilizados.php')
            .then(response => response.json())
            .then(data => {
                const produtosObsoletos = data.filter(movimentacao => {
                    const dataSaida = new Date(movimentacao.data_saida);
                    const hoje = new Date();
                    const diffMeses = (hoje.getFullYear() - dataSaida.getFullYear()) * 12 + (hoje.getMonth() - dataSaida.getMonth());
                    return diffMeses > 6; // Produtos sem movimentação há mais de 6 meses
                });

                const tbody = document.getElementById('tabelaObsoletos');
                tbody.innerHTML = '';
                produtosObsoletos.forEach(produto => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td>${produto.nome_produto}</td>
            <td>${formatarData(produto.data_saida)}</td>
          `;
                    tbody.appendChild(row);
                });
            });

        // Relatório de Reposição de Estoque
        fetch('../../ControleDeEstoque/php/buscar_produtos.php')
            .then(response => response.json())
            .then(data => {
                const produtosReposicao = data.filter(produto => produto.quantidade < 5);

                const tbody = document.getElementById('tabelaReposicao');
                tbody.innerHTML = '';
                produtosReposicao.forEach(produto => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td>${produto.nome}</td>
            <td>${produto.quantidade}</td>
            <td>Minimo 5</td>
          `;
                    tbody.appendChild(row);
                });
            });

        // Relatório de Validade de Produtos
        fetch('../../ControleDeEstoque/php/buscar_produtos.php')
            .then(response => response.json())
            .then(data => {
                const produtosProximoVencimento = data.filter(produto => {
                    const dataValidade = new Date(produto.validade);
                    const hoje = new Date();
                    const diffDias = Math.floor((dataValidade - hoje) / (1000 * 60 * 60 * 24));
                    return diffDias <= 30; // Produtos que vencem em até 30 dias
                });

                const tbody = document.getElementById('tabelaValidade');
                tbody.innerHTML = '';
                produtosProximoVencimento.forEach(produto => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td>${produto.nome}</td>
            <td>${formatarData(produto.validade)}</td>
          `;
                    tbody.appendChild(row);
                });
            });

        // Relatório de Pessoas que Mais Utilizaram Produtos
        fetch('../../ControleDeEstoque/php/buscar_utilizados.php')
            .then(response => response.json())
            .then(data => {
                const pessoas = {};
                data.forEach(movimentacao => {
                    if (pessoas[movimentacao.responsavel]) {
                        pessoas[movimentacao.responsavel] += parseInt(movimentacao.quantidade_utilizada);
                    } else {
                        pessoas[movimentacao.responsavel] = parseInt(movimentacao.quantidade_utilizada);
                    }
                });

                // Ordenar as pessoas por quantidade utilizada (do maior para o menor)
                const pessoasOrdenadas = Object.entries(pessoas).sort((a, b) => b[1] - a[1]);

                // Pegar apenas os top 10 (ou menos) para o gráfico
                const topPessoas = pessoasOrdenadas.slice(0, 10);

                const ctx = document.getElementById('pessoasChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar', // Pode ser 'bar', 'pie', 'doughnut', etc.
                    data: {
                        labels: topPessoas.map(pessoa => pessoa[0]), // Nomes das pessoas
                        datasets: [{
                            label: 'Quantidade Utilizada',
                            data: topPessoas.map(pessoa => pessoa[1]), // Quantidades
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(199, 199, 199, 0.2)',
                                'rgba(83, 102, 255, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(199, 199, 199, 1)',
                                'rgba(83, 102, 255, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
    </script>
</body>

</html>