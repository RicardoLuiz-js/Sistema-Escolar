     const eye = document.getElementById('eye');
        const content = document.getElementById('content');
             const eyeM = document.getElementById('eyeM');
        const contentM = document.getElementById('contentM');
            const tabela = document.getElementById('tabela-movimentacoes');
        
        eye.addEventListener('click', function() {
            // Alterna a classe 'closed' no olho
            this.classList.toggle('closed');
            
            // Alterna a visibilidade da div de conteúdo
            content.classList.toggle('hidden');
        });

           eyeM.addEventListener('click', function() {
            // Alterna a classe 'closed' no olho
            this.classList.toggle('closed');
            
            // Alterna a visibilidade da div de conteúdo
            contentM.classList.toggle('hidden');
        });
function carregarBens() {
    fetch('../php/movimentacoes/get.php')
        .then(response => response.json())
        .then(moveis => {
            const tbody = document.getElementById('tabela-movimentacoes');
            tbody.innerHTML = '';

            if (moveis.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">Nenhuma movimentação encontrada</td></tr>';
                return;
            }

            // Array para armazenar todas as promessas de busca de nomes
            const promessasNomes = moveis.map(mov => {
                return buscarNome(mov.id_movel).then(nome => {
                    return { mov, nome };
                });
            });

            // Quando todas as promessas forem resolvidas
            return Promise.all(promessasNomes);
        })
        .then(dadosComNomes => {
            const tbody = document.getElementById('tabela-movimentacoes');
            
            dadosComNomes.forEach(({ mov, nome }) => {
                const dataFormatada = formatarData(mov.data_movimentacao);
                
                const row = document.createElement('tr');
                row.className = 'tm';
                row.innerHTML = `
                    <td class="m">${nome}</td>
                    <td class="m">${mov.tipo_acao}</td>
                    <td class="m">${mov.descricao_acao}</td>
                    <td class="m">${mov.local_anterior || '-'}</td>
                    <td class="m">${mov.local_novo || '-'}</td>
                    <td class="m">${dataFormatada}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar móveis:', error);
            alert('Erro ao carregar dados: ' + error.message);
        });
}
 document.addEventListener('DOMContentLoaded', carregarBens);

function formatarData(data) {
    if (!data) return '';
    
    data = data.split('.')[0]; // Remove milissegundos
    
    const [dataPart, horaPart] = data.split(' ');
    
    if (dataPart) {
        const partes = dataPart.split('-');
        if (partes.length === 3) {
            let resultado = `${partes[2]}/${partes[1]}/${partes[0]}`;
            
            // Formata a hora se existir
            if (horaPart) {
                resultado += ` às ${horaPart}`;
            }
            
            return resultado;
        }
    }
    
    return data;
}

function buscarNome(id) {
    return fetch(`../php/moveis/buscarName.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            return data.nome || 'Nome não encontrado';
        })
        .catch(error => {
            console.error('Erro ao buscar nome:', error);
            return 'Erro ao carregar';
        });
}




// Função para pesquisar na tabela por nome do produto
function searchTableM() {
    const input = document.getElementById("searchInput-m");
    const filter = input.value.toUpperCase();
    const searchCount = document.getElementById("searchCount-m");
    
    // Busca apenas as linhas do corpo da tabela com classe "tm"
    const tbody = document.getElementById("tabela-movimentacoes");
    const tr = tbody.getElementsByClassName("tm");
    let searchNumber = 0;
    for (let i = 0; i < tr.length; i++) {
        const tds = tr[i].getElementsByClassName("m");
        if (tds.length > 0) {
            // Usa a PRIMEIRA coluna (índice 0) para a pesquisa - Nome do Produto
            const txtValue = tds[0].textContent || tds[0].innerText;
            if (txtValue.toUpperCase().includes(filter)) {
                tr[i].style.display = "";
                  searchNumber++;
                
                searchCount.textContent = searchNumber;
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}


function applyFilters(){
   const filter = document.getElementById("filtro-m").value;
   const rows = document.querySelectorAll("#myTable-movimentacoes #tabela-movimentacoes tr");
   

    let filterName = setFilter(filter);
    
    rows.forEach(row => {
        
        const acao = row.querySelector("td:nth-child(2)").textContent; // Coluna "acao"
       
        if (acao == filterName) {
            row.style.display = "";
           
            
        } else{ 
            if (filterName == 0){
                row.style.display = "";
        }else{
            row.style.display = "none";

        }
            
             
        }
    });

}


function setFilter(criteria) {
    switch (criteria) {
        case "opcao2": return "edicao"; 
        case "opcao3": return "exclusao"; 
        case "opcao4": return "transferencia"; 
        default: return 0;
    }
}

    

    // Adicionar evento de mudança ao select
            document.getElementById('filtro-local-m').addEventListener('change', function() {
                const filtro = this.value;
                
                // Obter todas as linhas da tabela
                const rows = Array.from(tabela.getElementsByTagName('tr'));
                
                if (filtro === 'novo' || filtro === 'antigo') {
                    // Converter datas para objetos Date para ordenação
                    const rowsWithDates = rows.map(row => {
                        const dateString = row.cells[5].textContent;
                        const [dataParte, horaParte] = dateString.split(' às ');
                        const [dia, mes, ano] = dataParte.split('/');
                        const [hora, minuto, segundo] = horaParte.split(':');
                        
                        return {
                            row: row,
                            date: new Date(ano, mes - 1, dia, hora, minuto, segundo)
                        };
                    });
                    
                    // Ordenar as linhas
                    if (filtro === 'novo') {
                        rowsWithDates.sort((a, b) => b.date - a.date);
                    } else {
                        rowsWithDates.sort((a, b) => a.date - b.date);
                    }
                    
                    // Extrair apenas as linhas ordenadas
                    const sortedRows = rowsWithDates.map(item => item.row);
                    
                    // Reinserir as linhas ordenadas na tabela
                    tabela.innerHTML = "";
                    sortedRows.forEach(row => tabela.appendChild(row));
                }
            });
        

               function imprimir() {
                // Abrir a caixa de diálogo de impressão
                window.print();
            }