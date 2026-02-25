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

                <a href="estoque.php" class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>Controle de Estoque</span>
                </a>

                <div class="menu-item active">
                    <i class="fas fa-laptop"></i>
                    <span>Controle de Bens</span>
                </div>





             


            </div>

        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="welcome">
                    <h2><i class="fas fa-laptop"></i> Controle de Bens</h2>
                    <p>Gerencie eventos, reuniões e atividades importantes</p>
                </div>

                <button class="buttonExport" onclick=" window.print();">
                    <h2> <i class="  fa-solid fa-file-pdf"></i></h2>
                    <p>Exportar PDF</p>
                </button>

                <a href="../php/exportar_excel_bens.php"><button class="buttonExport">
                        <h2> <i class="fa-solid fa-file-excel"></i> </h2>
                        <p>Exportar Excel</p>
                    </button></a>



            </div>

            <h2 class="dashboard-title">Estatísticas</h2>

            <div class="cardsEstoque">



                <!-- Card 1: Inventário Atual (Gráfico de Barras) -->
                <div class="card-header">
                    <h3 class="card-title">Inventário Atual</h3>
                    <div class="card-icon bg-blue">
                        <i class="fas fa-laptop"></i>
                    </div>
                </div>
                <div class="card-value">
                    <canvas id="inventarioChart" width="400" height="200"></canvas>
                </div>

                <!-- Card 2: Quantidades (Tabela de Móveis) -->
                <div class="card-header">
                    <h3 class="card-title">Quantidades</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-table"></i>
                    </div>
                </div>
                <div class="card-value">
                    <table class="vp"
                        style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; font-size: medium;"
                        id="tabelaInventario"></table>
                </div>

                <!-- Card 3: Movimentações (Gráfico de Linha) -->
                <div class="card-header">
                    <h3 class="card-title">Movimentações Recentes</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="card-value">
                    <canvas id="consumidosChart" width="400" height="200"></canvas>
                </div>

                <!-- Card 4: Últimas Movimentações (Tabela) -->
                <div class="card-header">
                    <h3 class="card-title">Últimas Movimentações</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div class="card-value">
                    <table class="vp" style="width: 100%; border-collapse: collapse; font-size: medium;"
                        id="tabelaMovimentacao">
                        <thead>
                            <tr>
                                <th>Móvel</th>
                                <th>Tipo</th>
                                <th>Origem</th>
                                <th>Destino</th>
                                <th>Data/Hora</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaMovimentacao"></tbody>
                    </table>
                </div>

                <!-- Card 5: Tipos de Movimentação (Tabela) -->
                <div class="card-header">
                    <h3 class="card-title">Tipos de Movimentação</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                </div>
                <div class="card-value">
                    <table class="vp" style="width: 100%; border-collapse: collapse; font-size: medium;"
                        id="tabelaObsoletos">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Percentual</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaObsoletos"></tbody>
                    </table>
                </div>

                <!-- Card 6: Móveis Mais Movimentados (Tabela) -->
                <div class="card-header">
                    <h3 class="card-title">Móveis Mais Movimentados</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-ranking-star"></i>
                    </div>
                </div>
                <div class="card-value">
                    <table class="vp" style="width: 100%; border-collapse: collapse; font-size: medium;"
                        id="tabelaReposicao">
                        <thead>
                            <tr>
                                <th>Móvel</th>
                                <th>Movimentações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaReposicao"></tbody>
                    </table>
                </div>

                <!-- Card 7: Locais que Mais Recebem Móveis (Tabela) -->
                <div class="card-header">
                    <h3 class="card-title">Locais que Mais Recebem</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                </div>
                <div class="card-value">
                    <table class="vp" style="width: 100%; border-collapse: collapse; font-size: medium;"
                        id="tabelaValidade">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Recebimentos</th>
                                <th>Percentual</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaValidade"></tbody>
                    </table>
                </div>

                <!-- Card 8: Distribuição por Tipo (Gráfico de Doughnut) -->
                <div class="card-header">
                    <h3 class="card-title">Distribuição por Tipo</h3>
                    <div class="card-icon bg-blue">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                </div>
                <div style="display: flex;  justify-content: center; align-items: center; text-align: center;">
                    <div class="card-value" style="width: 600px; justify-content: center; align-items: center; text-align: center;">
                        <canvas  id="pessoasChart" width="300" height="200"></canvas>
                    </div>

                </div>


















            </div>



        </div>
    </div>

    <script src="../js/index.js"></script>
    <script>
        function formatarData(data) {
            return new Date(data).toLocaleDateString('pt-BR');
        }

        function formatarDataHora(data) {
            return new Date(data).toLocaleString('pt-BR');
        }

        // Buscar dados dos móveis e movimentações
        Promise.all([
            fetch('../../ControleDeBens/php/moveis/buscar.php').then(res => res.json()),
            fetch('../../ControleDeBens/php/movimentacoes/get.php').then(res => res.json())
        ])
            .then(([moveis, movimentacoes]) => {
                if (moveis.error) {
                    console.error('Erro nos móveis:', moveis.error);
                    return;
                }
                if (movimentacoes.error) {
                    console.error('Erro nas movimentações:', movimentacoes.error);
                    return;
                }

                // 1. GRÁFICO DE INVENTÁRIO - Quantidade de móveis por localização
                const contagemPorLocal = {};
                moveis.forEach(movel => {
                    const local = movel.localizacao || 'Não definido';
                    contagemPorLocal[local] = (contagemPorLocal[local] || 0) + 1;
                });

                const ctxInventario = document.getElementById('inventarioChart').getContext('2d');
                new Chart(ctxInventario, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(contagemPorLocal),
                        datasets: [{
                            label: 'Quantidade de Móveis por Sala',
                            data: Object.values(contagemPorLocal),
                            backgroundColor: 'rgba(26, 35, 126, 0.7)',
                            borderColor: 'rgba(26, 35, 126, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição de Móveis por Localização'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Quantidade'
                                }
                            }
                        }
                    }
                });

                // Tabela de inventário - Lista todos os móveis
                const tbodyInventario = document.getElementById('tabelaInventario');
                tbodyInventario.innerHTML = '';
                moveis.forEach(movel => {
                    const row = document.createElement('tr');
                    row.innerHTML = ` 
            <td>${movel.nome}</td>
            <td>${movel.localizacao || 'Não definido'}</td>
            <td>${movel.numero_tombo || 'Sem tombo'}</td>
            <td>${formatarData(movel.data_cadastro)}</td> 
        `;
                    tbodyInventario.appendChild(row);
                });

                // 2. GRÁFICO DE MOVIMENTAÇÕES (substituindo "Mais Consumidos")
                const movimentacoesPorDia = {};
                movimentacoes.forEach(mov => {
                    const data = new Date(mov.data_movimentacao);
                    const dia = data.toLocaleDateString('pt-BR');
                    movimentacoesPorDia[dia] = (movimentacoesPorDia[dia] || 0) + 1;
                });

                // Pegar últimos 30 dias para o gráfico
                const ultimos30Dias = Object.keys(movimentacoesPorDia)
                    .sort((a, b) => new Date(a.split('/').reverse().join('-')) - new Date(b.split('/').reverse().join('-')))
                    .slice(-30);

                const ctxConsumidos = document.getElementById('consumidosChart').getContext('2d');
                new Chart(ctxConsumidos, {
                    type: 'line',
                    data: {
                        labels: ultimos30Dias,
                        datasets: [{
                            label: 'Movimentações por Dia',
                            data: ultimos30Dias.map(dia => movimentacoesPorDia[dia]),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Movimentações de Móveis (Últimos 30 dias)'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Quantidade de Movimentações'
                                }
                            }
                        }
                    }
                });

                // Tabela de movimentações
                const tbodyMovimentacao = document.getElementById('tabelaMovimentacao');
                if (tbodyMovimentacao) {
                    tbodyMovimentacao.innerHTML = '';
                    movimentacoes.slice(0, 20).forEach(mov => { // Mostrar últimas 20 movimentações
                        const movel = moveis.find(m => m.id == mov.id_movel) || { nome: 'Móvel não encontrado' };
                        const row = document.createElement('tr');
                        row.innerHTML = `
                <td>${movel.nome}</td>
                <td>${mov.tipo_acao}</td>
                <td>${mov.local_anterior || '-'}</td>
                <td>${mov.local_novo || '-'}</td>
                <td>${formatarDataHora(mov.data_movimentacao)}</td>
            `;
                        tbodyMovimentacao.appendChild(row);
                    });
                }

                // 3. TIPOS DE MOVIMENTAÇÃO (substituindo "Produtos Obsoletos")
                const tiposMovimentacao = {};
                movimentacoes.forEach(mov => {
                    tiposMovimentacao[mov.tipo_acao] = (tiposMovimentacao[mov.tipo_acao] || 0) + 1;
                });

                const tbodyObsoletos = document.getElementById('tabelaObsoletos');
                tbodyObsoletos.innerHTML = '';
                Object.entries(tiposMovimentacao).forEach(([tipo, quantidade]) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td><strong>${tipo}</strong></td>
            <td>${quantidade}</td>
            <td>${((quantidade / movimentacoes.length) * 100).toFixed(1)}%</td>
        `;
                    tbodyObsoletos.appendChild(row);
                });

                // 4. MÓVEIS MAIS MOVIMENTADOS (substituindo "Reposição de Estoque")
                const moveisMovimentados = {};
                movimentacoes.forEach(mov => {
                    moveisMovimentados[mov.id_movel] = (moveisMovimentados[mov.id_movel] || 0) + 1;
                });

                const topMoveisMovimentados = Object.entries(moveisMovimentados)
                    .sort((a, b) => b[1] - a[1])
                    .slice(0, 5)
                    .map(([id_movel, quantidade]) => {
                        const movel = moveis.find(m => m.id == id_movel) || { nome: 'Móvel não encontrado' };
                        return { nome: movel.nome, quantidade };
                    });

                const tbodyReposicao = document.getElementById('tabelaReposicao');
                tbodyReposicao.innerHTML = '';
                topMoveisMovimentados.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td><strong>${item.nome}</strong></td>
            <td>${item.quantidade} movimentação(ões)</td>
        `;
                    tbodyReposicao.appendChild(row);
                });

                // 5. MOVIMENTAÇÕES POR LOCAL (substituindo "Validade de Produtos")
                const movimentacoesPorLocal = {};
                movimentacoes.forEach(mov => {
                    if (mov.local_novo) {
                        movimentacoesPorLocal[mov.local_novo] = (movimentacoesPorLocal[mov.local_novo] || 0) + 1;
                    }
                });

                const topLocais = Object.entries(movimentacoesPorLocal)
                    .sort((a, b) => b[1] - a[1])
                    .slice(0, 5);

                const tbodyValidade = document.getElementById('tabelaValidade');
                tbodyValidade.innerHTML = '';
                topLocais.forEach(([local, quantidade]) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
            <td><strong>${local}</strong></td>
            <td>${quantidade} movimentação(ões)</td>
            <td>${((quantidade / movimentacoes.length) * 100).toFixed(1)}%</td>
        `;
                    tbodyValidade.appendChild(row);
                });

                // 6. GRÁFICO DE TIPOS DE MOVIMENTAÇÃO (substituindo "Pessoas que Mais Utilizaram")
                const ctxPessoas = document.getElementById('pessoasChart').getContext('2d');
                new Chart(ctxPessoas, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(tiposMovimentacao),
                        datasets: [{
                            data: Object.values(tiposMovimentacao),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição por Tipo de Movimentação'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Informações de resumo
                console.log(`Total de móveis: ${moveis.length}`);
                console.log(`Total de movimentações: ${movimentacoes.length}`);
                console.log(`Total de localizações: ${Object.keys(contagemPorLocal).length}`);

            })
            .catch(error => console.error('Erro ao carregar dados:', error));
    </script>
</body>

</html>