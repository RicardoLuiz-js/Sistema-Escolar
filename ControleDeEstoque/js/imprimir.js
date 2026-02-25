function carregarUtilizados() {
    fetch('../php/buscar_utilizados.php') // Endpoint para buscar os utilizados
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabela-historico').getElementsByTagName('tbody')[0];
            tbody.innerHTML = ''; // Limpa o conteúdo atual

            data.forEach(utilizado => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${utilizado.nome_produto}</td>
                    <td>${utilizado.quantidade_utilizada}</td>
                    <td>${utilizado.responsavel}</td>
                    <td>${inverterData(utilizado.data_saida)}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar produtos utilizados:', error);
        });
}
   // Carrega a tabela de utilizados
             carregarUtilizados();



  function searchTableUtilizados() {
        const input = document.getElementById("searchInputHistorico");
        const filter = input.value.toUpperCase(); // Converte o termo de pesquisa para maiúsculas
        const table = document.getElementById("tabela-historico");
        const tr = table.getElementsByTagName("tr");
    
        for (let i = 1; i < tr.length; i++) { // Começa em 1 para ignorar o cabeçalho
            const tdProduto = tr[i].getElementsByTagName("td")[0]; // Coluna "Produto"
            const tdResponsavel = tr[i].getElementsByTagName("td")[2]; // Coluna "Responsável"
    
            if (tdProduto && tdResponsavel) {
                const txtProduto = tdProduto.textContent || tdProduto.innerText;
                const txtResponsavel = tdResponsavel.textContent || tdResponsavel.innerText;
    
                // Verifica se o termo de pesquisa está presente no nome do produto ou no responsável
                if (txtProduto.toUpperCase().indexOf(filter) > -1 || txtResponsavel.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Mostra a linha
                } else {
                    tr[i].style.display = "none"; // Oculta a linha
                }
            }
        }
    }

    function applyFiltersUtilizados() {
    const filter = document.getElementById("filterSelectHistorico").value;
    const table = document.getElementById("tabela-historico");
    const tr = table.getElementsByTagName("tr");

    // Aplicar ordenação pelo filtro selecionado
    if (filter !== "opcao1") {
        const rows = Array.from(tr).slice(1); // Ignorar o cabeçalho
        rows.sort((a, b) => {
            const aValue = a.getElementsByTagName("td")[getColumnIndexUtilizados(filter)].textContent;
            const bValue = b.getElementsByTagName("td")[getColumnIndexUtilizados(filter)].textContent;

            if (filter === "opcao3") { // Quantidade Utilizada
                return parseFloat(aValue) - parseFloat(bValue); // Ordenar números
            } else if (filter === "opcao5") { // Data de Saída
                const dataA = converterDataBRParaDate(aValue); // Converte para Date
                const dataB = converterDataBRParaDate(bValue); // Converte para Date
                return dataA - dataB; // Ordenar datas
            } else {
                return aValue.localeCompare(bValue); // Ordenar textos (Produto ou Responsável)
            }
        });

        // Reinserir as linhas ordenadas na tabela
        const tbody = table.getElementsByTagName("tbody")[0];
        tbody.innerHTML = ""; // Limpar o corpo da tabela
        rows.forEach(row => tbody.appendChild(row));
    }
}


function getColumnIndexUtilizados(criteria) {
    switch (criteria) {
        case "opcao2": return 0; // Coluna "Produto"
        case "opcao3": return 1; // Coluna "Quantidade Utilizada"
        case "opcao4": return 2; // Coluna "Responsável"
        case "opcao5": return 3; // Coluna "Data de Saída"
        default: return 0; // Padrão: ordenar pela coluna "Produto"
    }
}



function carregarProdutos() {
    fetch('../php/buscar_produtos.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabela-produtos-h');
            tbody.innerHTML = ''; // Limpa o conteúdo atual

            data.forEach(produto => {
                // Inverter as datas de entrada e validade
                const entradaInvertida = inverterData(produto.entrada);
                const validadeInvertida = inverterData(produto.validade);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td value="${produto.id}">${produto.nome}</td>
                    <td value="${produto.id}">${produto.descricao}</td>
                    <td value="${produto.id}">${produto.categoria}</td>
                    <td value="${produto.id}">${produto.quantidade}</td>
                    <td value="${produto.id}">${produto.unidade}</td>
                    <td value="${produto.id}">${entradaInvertida}</td>
                    <td value="${produto.id}">${validadeInvertida}</td>
                 
                `;
                tbody.appendChild(row);
            });

   
        })
        .catch(error => {
            console.error('Erro ao carregar produtos:', error);
        });
}
// Função para inverter a data de YYYY-MM-DD para DD/MM/YYYY
function inverterData(data) {
    const partes = data.split('-');
    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}


    document.addEventListener('DOMContentLoaded', carregarProdutos);



function imprimir() {
                // Abrir a caixa de diálogo de impressão
                window.print();
            }
        