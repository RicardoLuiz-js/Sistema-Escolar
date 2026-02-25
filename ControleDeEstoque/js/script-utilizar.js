

function carregarUtilizados() {
    fetch('../php/buscar_utilizados.php') // Endpoint para buscar os utilizados
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabela-utilizados').getElementsByTagName('tbody')[0];
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




function carregarProdutos() {
    fetch('../php/buscar_produtos.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabela-produtos');
            tbody.innerHTML = ''; // Limpa o conteúdo atual

            data.forEach(produto => {
                const entradaInvertida = inverterData(produto.entrada);
                const validadeInvertida = inverterData(produto.validade);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td value="${produto.id}">${produto.nome}</td>
                    <td value="${produto.id}">${produto.descricao}</td>
                    <td class="visible" value="${produto.id}">${produto.categoria}</td>
                    <td value="${produto.id}">${produto.quantidade}</td>
                    <td class="visible" value="${produto.id}">${produto.unidade}</td>
                    <td class="visible" value="${produto.id}">${entradaInvertida}</td>
                    <td class="visible" value="${produto.id}">${validadeInvertida}</td>
                    
                    <td>
                        <button class="btn-utilizar" data-id="${produto.id}">
                            <img class="icon2" src="../imagens/botao-de-menos.png" alt="Utilizar">
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

     

            // Adiciona os event listeners aos botões de utilizar
            adicionarEventListenersUtilizar();


             // Carrega a tabela de utilizados
             carregarUtilizados();
        })
        .catch(error => {
            console.error('Erro ao carregar produtos:', error);
        });
}


function adicionarEventListenersUtilizar() {
    const botoesUtilizar = document.querySelectorAll('.btn-utilizar');

    botoesUtilizar.forEach(botao => {
        botao.addEventListener('click', function(event) {
            event.preventDefault(); // Impede o comportamento padrão do botão

            const produtoId = this.getAttribute('data-id');
            const produtoNome = this.closest('tr').querySelector('td:nth-child(1)').textContent; // Captura o nome do produto
            const quantidadeDisponivel = parseFloat(this.closest('tr').querySelector('td:nth-child(4)').textContent); // Captura a quantidade disponível

            // Abre o formulário de utilização com o nome do produto
            abrirFormularioUtilizacao(produtoId, produtoNome, quantidadeDisponivel);
        });
    });
}



function abrirFormularioUtilizacao(produtoId, produtoNome, quantidadeDisponivel) {
    // Cria o formulário de utilização
    const formulario = `
        <div id="formulario-utilizacao" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color:white;  background-color: #080D1C;  border-radius:10px; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); z-index: 1000;">
            <h2>Utilizar Produto</h2>
            <p><strong>Produto:</strong> ${produtoNome}</p>
            <p><strong>Quantidade Disponível:</strong> ${quantidadeDisponivel}</p>
            <form id="form-utilizar" class="form-utilizar">
                <input type="hidden" name="produto_id" value="${produtoId}">
                
                <label for="quantidade_utilizada">Quantidade Utilizada:</label>
                <input type="number" id="quantidade_utilizada" name="quantidade_utilizada" required><br><br>

                <label for="responsavel">Responsável:</label>
                <input type="text" id="responsavel" name="responsavel" required><br><br>

                <label for="data_saida">Data de Saída:</label>
                <input type="date" id="data_saida" name="data_saida" required><br><br>

                <button type="submit">Salvar Utilização</button>
                <button type="button" id="cancelar-utilizacao">Cancelar</button>
            </form>
        </div>
        <div id="overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;"></div>
    `;

    // Adiciona o formulário à página
    document.body.insertAdjacentHTML('beforeend', formulario);

    // Adiciona o evento de submit ao formulário
    document.getElementById('form-utilizar').addEventListener('submit', function(event) {
        event.preventDefault();

        // Captura os valores dos campos
        const quantidadeUtilizada = parseFloat(document.getElementById('quantidade_utilizada').value);
        const responsavel = document.getElementById('responsavel').value.trim();
        const dataSaida = document.getElementById('data_saida').value;

        // Validações
        if (quantidadeUtilizada <= 0) {
            alert('A quantidade utilizada deve ser maior que zero.');
            return;
        }

        if (quantidadeUtilizada > quantidadeDisponivel) {
            alert('A quantidade utilizada não pode ser maior que a quantidade disponível.');
            return;
        }

        if (!responsavel) {
            alert('O campo "Responsável" é obrigatório.');
            return;
        }

        if (!dataSaida) {
            alert('A data de saída é obrigatória.');
            return;
        }

        // Verifica se a data de saída é no futuro (opcional)
        const hoje = new Date().toISOString().split('T')[0]; // Formato YYYY-MM-DD
        if (dataSaida > hoje) {
            alert('A data de saída não pode ser no futuro.');
            return;
        }

        // Se todas as validações passarem, envia o formulário
        const formData = new FormData(this);

        fetch('../php/salvar_utilizacao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
              
                Message('Utilização registrada com sucesso!','green')
                
                fecharFormularioUtilizacao();
            } else {
                Message('Erro ao registrar a utilização','red');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
    });

    // Adiciona o evento de cancelar
    document.getElementById('cancelar-utilizacao').addEventListener('click', function() {
        fecharFormularioUtilizacao();
    });
}


// Função para fechar o formulário de utilização
function fecharFormularioUtilizacao() {
    const formulario = document.getElementById('formulario-utilizacao');
    const overlay = document.getElementById('overlay');
    if (formulario) formulario.remove();
    if (overlay) overlay.remove();
}









// Função para inverter a data de YYYY-MM-DD para DD/MM/YYYY
function inverterData(data) {
    const partes = data.split('-');
    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}


    document.addEventListener('DOMContentLoaded', carregarProdutos);



    function searchTableUtilizados() {
        const input = document.getElementById("searchInputUtilizados");
        const filter = input.value.toUpperCase(); // Converte o termo de pesquisa para maiúsculas
        const table = document.getElementById("tabela-utilizados");
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



// Função para pesquisar na tabela por nome do produto
function searchTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toUpperCase();
    const table = document.getElementById("myTable");
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) { // Começa em 1 para ignorar o cabeçalho
        const td = tr[i].getElementsByTagName("td")[0]; // Coluna "Nome do Produto"
        if (td) {
            const txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}


function applyFiltersUtilizados() {
    const filter = document.getElementById("filterSelectUtilizados").value;
    const table = document.getElementById("tabela-utilizados");
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


// Função para aplicar filtros de categoria e ordenação
function applyFilters() {
    const category = document.getElementById("categorySelect").value;
    const filter = document.getElementById("filterSelect").value;
    const table = document.getElementById("myTable");
    const tr = table.getElementsByTagName("tr");

    // Aplicar filtro de categoria
    for (let i = 1; i < tr.length; i++) {
        const tdCategory = tr[i].getElementsByTagName("td")[2]; // Coluna "Categoria"
        let shouldDisplay = true;

        if (category !== "opcao1" && tdCategory.textContent !== category) {
            shouldDisplay = false;
        }

        tr[i].style.display = shouldDisplay ? "" : "none";
    }

    // Aplicar ordenação pelo filtro selecionado
    if (filter !== "opcao1") {
        const rows = Array.from(tr).slice(1); // Ignorar o cabeçalho
        rows.sort((a, b) => {
            const aValue = a.getElementsByTagName("td")[getColumnIndex(filter)].textContent;
            const bValue = b.getElementsByTagName("td")[getColumnIndex(filter)].textContent;

            if (filter === "opcao4") { // Quantidade
                return parseInt(aValue) - parseInt(bValue); // Ordenar números
            } else {
                return aValue.localeCompare(bValue); // Ordenar textos (datas)
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


// Função para mapear o critério de filtro para o índice da coluna
function getColumnIndex(criteria) {
    switch (criteria) {
        case "opcao2": return 5; // Coluna "Data de Entrada"
        case "opcao3": return 6; // Coluna "Data de Validade"
        case "opcao4": return 3; // Coluna "Quantidade"
        default: return 0;
    }
}

// Função para filtrar produtos em falta
function filterEmFalta() {
    const rows = document.querySelectorAll("#myTable tbody tr");
    let count = 0;

    rows.forEach(row => {
        const quantidade = parseInt(row.querySelector("td:nth-child(4)").textContent); // Coluna "Quantidade"
        if (quantidade === 0) {
            row.style.display = "";
            count++;
        } else {
            row.style.display = "none";
        }
    });

    document.getElementById("emFaltaCount").textContent = count;
}

// Função para converter data no formato brasileiro (dd/mm/aaaa) para o formato Date do JavaScript
function converterDataBRParaDate(dataBR) {
    const [dia, mes, ano] = dataBR.split('/'); // Divide a data em dia, mês e ano
    return new Date(`${mes}/${dia}/${ano}`); // Retorna a data no formato Date (mm/dd/aaaa)
}

// Função para filtrar produtos perto da validade
function filterValidade() {
    const rows = document.querySelectorAll("#myTable tbody tr");
    const hoje = new Date(); // Data atual
    let count = 0;

    rows.forEach(row => {
        const dataValidadeBR = row.querySelector("td:nth-child(7)").textContent; // Coluna "Data de Validade" (formato brasileiro)
        const dataValidade = converterDataBRParaDate(dataValidadeBR); // Converte para Date
        const quantidade = parseInt(row.querySelector("td:nth-child(4)").textContent); // Coluna "Quantidade"
        const diasParaVencer = Math.floor((dataValidade - hoje) / (1000 * 60 * 60 * 24)); // Diferença em dias

        // Verifica se o produto vence em até 7 dias e se há quantidade disponível
        if (diasParaVencer >= 0 && diasParaVencer <= 7 && quantidade > 0) {
            row.style.display = ""; // Mostra a linha
            count++;
        } else {
            row.style.display = "none"; // Esconde a linha
        }
    });

    document.getElementById("validadeCount").textContent = count;
}

// Função para filtrar produtos estragados
function filterEstragados() {
    const rows = document.querySelectorAll("#myTable tbody tr");
    const hoje = new Date(); // Data atual
   
    let count = 0;

    rows.forEach(row => {
        const dataValidadeBR = row.querySelector("td:nth-child(7)").textContent; // Coluna "Data de Validade" (formato brasileiro)
        const dataValidade = converterDataBRParaDate(dataValidadeBR); // Converte para Date
        const quantidade = parseInt(row.querySelector("td:nth-child(4)").textContent); // Coluna "Quantidade"
        
        // Verifica se o produto está vencido e se a quantidade é maior que zero
        if (dataValidade < hoje && quantidade > 0) { 
            row.style.display = ""; // Mostra a linha
            count++;
        } else {
            row.style.display = "none"; // Esconde a linha
        }
    });

    document.getElementById("estragadosCount").textContent = count;
}

// Função para resetar os filtros e mostrar todos os produtos
function resetFilters() {
    const rows = document.querySelectorAll("#myTable tbody tr");
    rows.forEach(row => {
        row.style.display = "";
    });
}

// Função para atualizar os contadores ao carregar a página
function atualizarContadores() {
    const rows = document.querySelectorAll("#myTable tbody tr");
    const hoje = new Date(); // Data atual
    let emFaltaCount = 0;
    let validadeCount = 0;
    let estragadosCount = 0;

    rows.forEach(row => {
        const quantidade = parseInt(row.querySelector("td:nth-child(4)").textContent); // Coluna "Quantidade"
        const dataValidadeBR = row.querySelector("td:nth-child(7)").textContent; // Coluna "Data de Validade" (formato brasileiro)
        const dataValidade = converterDataBRParaDate(dataValidadeBR); // Converte para Date
        const diasParaVencer = Math.floor((dataValidade - hoje) / (1000 * 60 * 60 * 24)); // Diferença em dias

        if (quantidade === 0) emFaltaCount++;
        if (diasParaVencer >= 0 && diasParaVencer <= 7 && quantidade > 0) validadeCount++;
        if (dataValidade < hoje && quantidade > 0) estragadosCount++;


        if (quantidade === 0) {
            row.classList.add("em-falta"); // Produto em falta
        } else if (diasParaVencer >= 0 && diasParaVencer <= 7 && quantidade > 0) {
            row.classList.add("validade-proxima"); // Produto perto de vencer
        } else if (dataValidade < hoje && quantidade > 0) {
            row.classList.add("estragado"); // Produto vencido
        }
   



    });

    // Atualizar os contadores
    document.getElementById("emFaltaCount").textContent = emFaltaCount;
    document.getElementById("validadeCount").textContent = validadeCount;
    document.getElementById("estragadosCount").textContent = estragadosCount;
}


let activeFilter = null; // Armazena o filtro ativo

// Função para alternar entre filtros
function toggleFilter(filter) {
    if (activeFilter === filter) {
        // Se o filtro já estiver ativo, reseta
        resetFilters();
        activeFilter = null;
    } else {
        // Aplica o filtro selecionado
        if (filter === "emFalta") filterEmFalta();
        else if (filter === "validade") filterValidade();
        else if (filter === "estragados") filterEstragados();
        activeFilter = filter;
    }
}




// Abre o menu ao clicar no ícone de hambúrguer
document.getElementById('mobile-menu-icon').addEventListener('click', function() {
    var menu = document.getElementById('Menu');
    menu.classList.add('active');
});

// Fecha o menu ao clicar no botão de "X"
document.getElementById('close-menu-icon').addEventListener('click', function() {
    var menu = document.getElementById('Menu');
    menu.classList.remove('active');
});



// Função para exibir a mensagem
function Message(message,color) {
    if(color=='green'){
    var messageBox = document.getElementById('message-box');
    messageBox.style.backgroundColor='green'
    messageBox.textContent = message;
    messageBox.style.display = 'block';
}else{
    var messageBox = document.getElementById('message-box');
    messageBox.style.backgroundColor='red'
    messageBox.textContent = message;
    messageBox.style.display = 'block';
}
    // Faz a mensagem subir e depois desaparecer
    setTimeout(function() {
        messageBox.style.transition = "bottom 3s";
        messageBox.style.bottom = "400px";
    }, 100);

    setTimeout(function() {
        messageBox.style.display = 'none';
        location.reload();
    }, 5000); // A mensagem desaparece após 5 segundos

}


// Atualiza os contadores ao carregar a página
var checkReadyState = setInterval(function() {
    if (document.readyState === "complete") {
        atualizarContadores();
      
        clearInterval(checkReadyState); // Para de verificar após a execução da função
    }
}, 1000); // Verifica a cada 1000ms (1 segundo)