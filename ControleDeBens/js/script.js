



function carregarBens() {
    // Buscar a lista de móveis
    fetch('../php/moveis/buscar.php')
        .then(response => response.json())
        .then(moveis => {
            const tbody = document.getElementById('tabela-bens');
            tbody.innerHTML = '';

            moveis.forEach(bens => {
              const dataFormatada = formatarData(bens.data_cadastro);
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${bens.nome}</td>
                    <td>${bens.descricao}</td>
                    <td>${bens.localizacao}</td>
                    <td>${bens.numero_tombo}</td>
                    <td>${dataFormatada}</td>
                    <td>
                        <button class="btn-editar" data-id="${bens.id}">
                            <img class="icon2" src="../imagens/editar.png" alt="Editar">
                        </button>
                    </td>
                    <td>
                        <button class="btn-excluir" data-id="${bens.id}">
                            <img class="icon2" src="../imagens/excluir.png" alt="Excluir">
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            adicionarEventListenersExclusao();
            adicionarEventListenersEdicao();

            // Atualizar contador inicial (sem filtro)
            atualizarContador();
        })
        .catch(error => {
            console.error('Erro ao carregar móveis:', error);
        });

}

//função para formatar datas
function formatarData(data) {
    if (!data) return '';
    
    // Converte de "YYYY-MM-DD" para "DD/MM/YYYY"
    const partes = data.split('-');
    if (partes.length === 3) {
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }
    return data; // Retorna original se não conseguir formatar
}
function carregarTotal(){

    // Buscar o total de itens
    fetch('../php/moveis/contar.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-itens').textContent = 
                `Total de itens: ${data.total_itens}`;
        })
        .catch(error => {
            console.error('Erro ao contar móveis:', error);
        });
}
function carregarLocais() {
    fetch('../php/moveis/locais.php')
        .then(response => response.json())
        .then(locais => {
            const select = document.getElementById('filtro-local');
            select.innerHTML = '<option value="">Todos os locais</option>';
            
            locais.forEach(local => {
                const option = document.createElement('option');
                option.value = local;
                option.textContent = local;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar locais:', error);
        });
}

//função que atualiza contador de itens
function atualizarContador(localizacao = '') {
    let url = '../php/moveis/contar.php';
    
    // Se tiver localização, adicionar parâmetro
    if (localizacao) {
        url += `?localizacao=${encodeURIComponent(localizacao)}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            let texto = `Total de itens: ${data.total_itens}`;
            
            // Se tiver filtro ativo, mostrar qual
            if (localizacao) {
                texto += ` em ${localizacao}`;
            }
            
            document.getElementById('total-itens').textContent = texto;
        })
        .catch(error => {
            console.error('Erro ao atualizar contador:', error);
        });
}
// Chamar na inicialização
carregarTotal();
carregarLocais();
carregarBens();

//filtros da tabela
function filtrarTabela() {
    const localSelecionado = document.getElementById('filtro-local').value;
    const linhas = document.querySelectorAll('#tabela-bens tr');
    let itensVisiveis = 0;
    
    linhas.forEach(linha => {
        const localLinha = linha.cells[2].textContent; // Coluna de localização
        
        if (!localSelecionado || localLinha === localSelecionado) {
            linha.style.display = '';
            itensVisiveis++;
        } else {
            linha.style.display = 'none';
        }
    });
    
    // Atualizar contador com o filtro
    atualizarContador(localSelecionado);
    
    // Mostrar mensagem se não houver itens
    if (itensVisiveis === 0) {
        const tbody = document.getElementById('tabela-bens');
        tbody.innerHTML = '<tr><td colspan="7">Nenhum item encontrado para este local</td></tr>';
    }
}

// Adicionar evento de change no select
document.getElementById('filtro-local').addEventListener('change', filtrarTabela);

// Função para pesquisar na tabela por nome do produto
function searchTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toUpperCase();
    const table = document.getElementById("myTable");
    const tr = table.getElementsByTagName("tr");
    const searchCount = document.getElementById("searchCount");

    let searchNumber = 0;


    for (let i = 1; i < tr.length; i++) { // Começa em 1 para ignorar o cabeçalho
        const td = tr[i].getElementsByTagName("td")[0]; // Coluna "Nome do Produto"
        if (td) {
            const txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
                searchNumber++;
                
                searchCount.textContent = searchNumber;
               
               
                
            } else {
                tr[i].style.display = "none";
            }
        }
    }  
}

let movelIdParaExcluir = null;

// Função para abrir o modal de exclusão
function abrirModalExclusao(id) {
    movelIdParaExcluir = id;
    document.getElementById('modal-excluir').style.display = 'block';
    document.getElementById('motivo-exclusao').value = '';
}

// Função para fechar o modal
function fecharModalExclusao() {
    document.getElementById('modal-excluir').style.display = 'none';
    movelIdParaExcluir = null;
}

// Função para executar a exclusão
function executarExclusao() {
    if (!movelIdParaExcluir) return;

    const motivo = document.getElementById('motivo-exclusao').value.trim();
    
    if (!motivo) {
        alert('Por favor, digite o motivo da exclusão');
        return;
    }

    // Desabilitar botão durante a requisição
    const btnConfirmar = document.getElementById('btn-confirmar-exclusao');
    btnConfirmar.disabled = true;
    btnConfirmar.textContent = 'Excluindo...';

    fetch('../php/moveis/excluir.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id_movel: movelIdParaExcluir,
            motivo: motivo
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarSucesso('Item excluído com sucesso!');
            fecharModalExclusao();
            carregarBens(); // Recarregar a lista
        } else {
           
             mostrarErro('Erro ao excluir: ' + data.message);
            
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        
        mostrarErro('Erro ao excluir item');
    })
    .finally(() => {
        btnConfirmar.disabled = false;
        btnConfirmar.textContent = 'Confirmar Exclusão';
    });
}

// Adicionar event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botão fechar modal
  document.querySelectorAll('.close').forEach(botao => {
    botao.addEventListener('click', fecharModais);
});

    
    // Botão cancelar
    document.getElementById('btn-cancelar-exclusao').addEventListener('click', fecharModalExclusao);
    
    // Botão confirmar
    document.getElementById('btn-confirmar-exclusao').addEventListener('click', executarExclusao);
    
    // Fechar modal clicando fora
    document.getElementById('modal-excluir').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModalExclusao();
        }
    });
});

// Modificar a função adicionarEventListenersExclusao
function adicionarEventListenersExclusao() {
    document.querySelectorAll('.btn-excluir').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            abrirModalExclusao(id);
        });
    });
}

let movelIdParaEditar = null;
let movelDados = null;

// Função para abrir modal de opções
function abrirModalOpcoesEdicao(id) {
    movelIdParaEditar = id;
    
    // Buscar dados do item primeiro
    fetch(`../php/moveis/buscar_por_id.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            movelDados = data;
            document.getElementById('modal-opcoes-edicao').style.display = 'block';
        })
        .catch(error => {
            console.error('Erro ao buscar dados:', error);
            alert('Erro ao carregar dados do item');
        });
}

// Função para abrir modal de editar informações
function abrirModalEditarInformacoes() {
    if (!movelDados) return;
    
    // Preencher formulário com dados atuais
    document.getElementById('editar-info-id').value = movelDados.id;
    document.getElementById('editar-info-nome').value = movelDados.nome;
    document.getElementById('editar-info-descricao').value = movelDados.descricao;
    document.getElementById('editar-info-tombo').value = movelDados.numero_tombo;
    document.getElementById('editar-info-motivo').value = '';
    
    fecharModalOpcoes();
    document.getElementById('modal-editar-informacoes').style.display = 'block';
}

// Função para abrir modal de transferir local
function abrirModalTransferirLocal() {
    if (!movelDados) return;
    
    // Preencher formulário
    document.getElementById('transferir-id').value = movelDados.id;
    document.getElementById('transferir-local-atual').value = movelDados.localizacao;
    document.getElementById('transferir-local-novo').value = '';
    document.getElementById('transferir-motivo').value = '';
    
    fecharModalOpcoes();
    document.getElementById('modal-transferir-local').style.display = 'block';
}

// Funções para fechar modais
function fecharModalOpcoes() {
    document.getElementById('modal-opcoes-edicao').style.display = 'none';
}

function fecharModalEditarInfo() {
    document.getElementById('modal-editar-informacoes').style.display = 'none';
}

function fecharModalTransferir() {
    document.getElementById('modal-transferir-local').style.display = 'none';
}
function fecharModais(){
    console.log("modais fechados");
    fecharModalEditarInfo();
    fecharModalOpcoes();
    fecharModalExclusao();
    fecharModalTransferir();
}

// Adicionar event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botões de opção
    document.getElementById('btn-editar-informacoes').addEventListener('click', abrirModalEditarInformacoes);
    document.getElementById('btn-transferir-local').addEventListener('click', abrirModalTransferirLocal);
    
    // Botões cancelar
    document.getElementById('btn-cancelar-opcoes').addEventListener('click', fecharModalOpcoes);
    document.getElementById('btn-cancelar-edicao-info').addEventListener('click', fecharModalEditarInfo);
    document.getElementById('btn-cancelar-transferencia').addEventListener('click', fecharModalTransferir);
    
    // Forms submit
    document.getElementById('form-editar-info').addEventListener('submit', salvarEdicaoInformacoes);
    document.getElementById('form-transferir-local').addEventListener('submit', salvarTransferencia);
});

// Modificar a função adicionarEventListenersEdicao
function adicionarEventListenersEdicao() {
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            abrirModalOpcoesEdicao(id);
        });
    });
}

  
  

// Função para salvar edição de informações
async function salvarEdicaoInformacoes(e) {
    e.preventDefault();
    
    const formData = {
        id: document.getElementById('editar-info-id').value,
        nome: document.getElementById('editar-info-nome').value,
        descricao: document.getElementById('editar-info-descricao').value,
        numero_tombo: document.getElementById('editar-info-tombo').value,
        motivo: document.getElementById('editar-info-motivo').value
    };

    const btnConfirmar = document.getElementById('btn-confirmar-edicao-info');
    btnConfirmar.disabled = true;
    btnConfirmar.textContent = 'Salvando...';

    try {
        const response = await fetch('../php/moveis/editar_informacoes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
             mostrarSucesso('Informações atualizadas com sucesso!');
            fecharModalEditarInfo();
            carregarBens(); // Recarregar a lista
        } else {
             mostrarErro('Erro: ' + data.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        
         mostrarErro('Erro ao salvar alterações');
    } finally {
        btnConfirmar.disabled = false;
        btnConfirmar.textContent = 'Salvar Alterações';
    }
}

// Função para salvar transferência
async function salvarTransferencia(e) {
    e.preventDefault();
    
    const formData = {
        id: document.getElementById('transferir-id').value,
        novo_local: document.getElementById('transferir-local-novo').value,
        motivo: document.getElementById('transferir-motivo').value
    };

    const btnConfirmar = document.getElementById('btn-confirmar-transferencia');
    btnConfirmar.disabled = true;
    btnConfirmar.textContent = 'Transferindo...';

    try {
        const response = await fetch('../php/moveis/transferir_local.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
             mostrarSucesso('Item transferido com sucesso!');
            fecharModalTransferir();
            carregarBens(); // Recarregar a lista
            carregarLocais(); // Recarregar locais (pode ter mudado)
        } else {
           mostrarErro('Erro: ' + data.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        
         mostrarErro('Erro ao transferir item');
    } finally {
        btnConfirmar.disabled = false;
        btnConfirmar.textContent = 'Confirmar Transferência';
    }
}

