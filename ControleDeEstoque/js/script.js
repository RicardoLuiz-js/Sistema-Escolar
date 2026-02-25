
 

// Atualiza os contadores ao carregar a página
var checkReadyState = setInterval(function() {
    if (document.readyState === "complete") {
        atualizarContadores();
      
        clearInterval(checkReadyState); // Para de verificar após a execução da função
    }
}, 1000); // Verifica a cada 1000ms (1 segundo)



function carregarProdutos() {
    fetch('../php/buscar_produtos.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabela-produtos');
            tbody.innerHTML = ''; // Limpa o conteúdo atual

            data.forEach(produto => {
                // Inverter as datas de entrada e validade
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
                        <button class="btn-editar" data-id="${produto.id}">
                            <img class="icon2" src="../imagens/editar.png" alt="Editar">
                        </button>
                    </td>
                    <td>
                        <button class="btn-excluir" data-id="${produto.id}">
                            <img class="icon2" src="../imagens/excluir.png" alt="Excluir">
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Adiciona os event listeners aos botões de exclusão
            adicionarEventListenersExclusao();

            // Adiciona os event listeners aos botões de edição
            adicionarEventListenersEdicao();
        })
        .catch(error => {
            console.error('Erro ao carregar produtos:', error);
        });
}






// Função para adicionar event listeners aos botões de edição
function adicionarEventListenersEdicao() {
    const botoesEdicao = document.querySelectorAll('.btn-editar');

    botoesEdicao.forEach(botao => {
        botao.addEventListener('click', function(event) {
            event.preventDefault(); // Impede o comportamento padrão do botão

            const produtoId = this.getAttribute('data-id');

            // Busca os dados do produto via AJAX
            fetch(`../php/buscar_produto.php?id=${produtoId}`)
                .then(response => response.json())
                .then(produto => {
                    // Exibe o formulário de edição
                    abrirFormularioEdicao(produto);
                })
                .catch(error => {
                    console.error('Erro ao buscar produto:', error);
                });
        });
    });
}

function abrirFormularioEdicao(produto) {
    // Formata as datas para o formato YYYY-MM-DD (necessário para o input type="date")
    const entradaFormatada = produto.entrada.split('/').reverse().join('-'); // Converte DD/MM/YYYY para YYYY-MM-DD
    const validadeFormatada = produto.validade.split('/').reverse().join('-'); // Converte DD/MM/YYYY para YYYY-MM-DD

    // Cria o formulário de edição
    const formulario = `
        <div id="formulario-edicao" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color:white;  background-color: #080D1C;  border-radius:10px; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); z-index: 1000; ">
            <h2>Editar Produto</h2>
            <form id="form-editar" class="form-editar">
                <input type="hidden" name="id" value="${produto.id}">
                
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="${produto.nome}" required><br><br>

                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" required>${produto.descricao}</textarea><br><br>

                <label for="categoria">Categoria:</label>
             
                 <select id="categoria" name="categoria"  value="${produto.categoria} required>
             
              <option value="Alimentos não perecíveis">Alimentos não perecíveis</option>
              <option value="Alimentos perecíveis">Alimentos perecíveis</option>
              <option value="Frios e carnes">Frios e carnes</option>
              <option value="Limpeza">Limpeza</option>
               <option value="Alimentos não perecíveis">não perecível</option>
              <!-- Adicione mais opções conforme necessário -->
            </select><br><br>

                <label for="quantidade">Quantidade:</label>
                <input type="number" id="quantidade" name="quantidade" value="${produto.quantidade}" required><br><br>

                <label for="unidade">Unidade:</label>
               
                 <select id="unidade" name="unidade" value="${produto.unidade}" required>
            <option value="kg">Kg</option>
            <option value="litro">Litro</option>
            <option value="unidade">Unidade</option>
            <option value="ml">ml</option> 
            <option value="g">g</option> 
              <!-- Adicione mais unidades conforme necessário -->
            </select><br><br>

                <label for="entrada">Data de Entrada:</label>
                <input type="date" id="entrada" name="entrada" value="${entradaFormatada}" required><br><br>

                <label for="validade">Data de Validade:</label>
                <input type="date" id="validade" name="validade" value="${validadeFormatada}" required><br><br>

                <button type="submit">Salvar Alterações</button>
                <button type="button" id="cancelar-edicao">Cancelar</button>
            </form>
        </div>
        <div id="overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;"></div>
    `;

    // Adiciona o formulário à página
    document.body.insertAdjacentHTML('beforeend', formulario);

    // Adiciona o evento de submit ao formulário
    document.getElementById('form-editar').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('../php/atualizar_produto.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Message('Produto atualizado com sucesso','green')
               
                fecharFormularioEdicao();
            } else {
                Message('Erro ao atualizar o produto.','red');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
    });

    // Adiciona o evento de cancelar
    document.getElementById('cancelar-edicao').addEventListener('click', function() {
        fecharFormularioEdicao();
    });
}

// Função para fechar o formulário de edição
function fecharFormularioEdicao() {
    const formulario = document.getElementById('formulario-edicao');
    const overlay = document.getElementById('overlay');
    if (formulario) formulario.remove();
    if (overlay) overlay.remove();
}



  
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






// Função para adicionar event listeners aos botões de exclusão
function adicionarEventListenersExclusao() {
    const botoesExclusao = document.querySelectorAll('.btn-excluir');

    botoesExclusao.forEach(botao => {
        botao.addEventListener('click', function() {
            const produtoId = this.getAttribute('data-id');
            const confirmacao = confirm('Deseja realmente excluir este item?');

            if (confirmacao) {
                fetch('../php/excluir_produto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: produtoId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Message('Item excluído com sucesso!','green')
                        
                    } else {
                        Message('Erro ao excluir o item.','red');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
            }
        });
    });
}



// Função para inverter a data de YYYY-MM-DD para DD/MM/YYYY
function inverterData(data) {
    const partes = data.split('-');
    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}


    document.addEventListener('DOMContentLoaded', carregarProdutos);







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
  
  







