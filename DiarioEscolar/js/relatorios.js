// Funções para a página de relatórios

// Limpar todos os filtros
function limparFiltros() {
    window.location.href = 'relatorios.php';
}

// Visualizar relatório no modal
function visualizarRelatorio(id) {
    const evento = eventos.find(e => e.id == id);
    if (!evento) return;
    
    const data = new Date(evento.data + 'T12:00:00'); // Adiciona hora para evitar problema de fuso
    const dataFormatada = data.toLocaleDateString('pt-BR');
    
    let html = `
        <div class="info-group">
            <label><i class="fas fa-calendar"></i> Data</label>
            <p>${dataFormatada}</p>
        </div>
        
        <div class="info-group">
            <label><i class="fas fa-heading"></i> Título</label>
            <p>${evento.titulo || 'Sem título'}</p>
        </div>
        
        <div class="info-group">
            <label><i class="fas fa-tag"></i> Tipo</label>
            <p>${evento.tipo || 'Não especificado'}</p>
        </div>
    `;
    
    if (evento.situacao) {
        html += `
            <div class="info-group">
                <label><i class="fas fa-school"></i> Situação</label>
                <p>${evento.situacao === 'teveAula' ? 'Teve aula' : 'Não teve aula'}</p>
            </div>
        `;
    }
    
    if (evento.nomeEvento) {
        html += `
            <div class="info-group">
                <label><i class="fas fa-star"></i> Nome do Evento</label>
                <p>${evento.nomeEvento}</p>
            </div>
        `;
    }
    
    if (evento.motivo) {
        html += `
            <div class="info-group">
                <label><i class="fas fa-question-circle"></i> Motivo</label>
                <p>${evento.motivo}</p>
            </div>
        `;
    }
    
    html += `
        <div class="info-group">
            <label><i class="fas fa-align-left"></i> Descrição</label>
            <p>${evento.descricao || 'Sem descrição'}</p>
        </div>
    `;
    
    if (evento.anexos) {
        try {
            const anexos = JSON.parse(evento.anexos);
            if (anexos.length > 0) {
                html += `
                    <div class="info-group">
                        <label><i class="fas fa-paperclip"></i> Anexos</label>
                        <div class="anexos-list">
                `;
                
                anexos.forEach(anexo => {
                    const nomeArquivo = anexo.split('/').pop();
                    html += `
                        <a href="${anexo}" target="_blank" class="anexo-item">
                            <i class="fas fa-file"></i>
                            ${nomeArquivo}
                        </a>
                    `;
                });
                
                html += `</div></div>`;
            }
        } catch (e) {
            console.log('Erro ao carregar anexos:', e);
        }
    }
    
    document.getElementById('viewModalBody').innerHTML = html;
    document.getElementById('viewModal').style.display = 'flex';
}

// Fechar modal de visualização
function fecharVisualizacao() {
    document.getElementById('viewModal').style.display = 'none';
}

// Editar relatório
function editarRelatorio(id) {
    // Redirecionar para o calendário com o modal aberto
    window.location.href = `index.php?editar=${id}`;
}

// Excluir relatório
async function excluirRelatorio(id) {
    if (!confirm('Tem certeza que deseja excluir este relatório? Esta ação não pode ser desfeita.')) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        const response = await fetch('../php/calendario_ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            mostrarMensagem('Relatório excluído com sucesso!', 'sucesso');
            setTimeout(() => location.reload(), 1500);
        } else {
            throw new Error(resultado.message || 'Erro ao excluir');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao excluir: ' + error.message);
    }
}

// Mostrar mensagem temporária
function mostrarMensagem(texto, tipo = 'sucesso') {
    const msgBox = document.getElementById('message-box');
    msgBox.textContent = texto;
    msgBox.style.backgroundColor = tipo === 'sucesso' ? '#4CAF50' : '#f44336';
    msgBox.style.display = 'block';
    
    setTimeout(() => {
        msgBox.style.display = 'none';
    }, 3000);
}

// Filtrar tabela em tempo real (opcional)
function filtrarTabela() {
    const busca = document.getElementById('buscaRapida')?.value.toLowerCase();
    if (!busca) return;
    
    const linhas = document.querySelectorAll('#relatoriosTableBody tr');
    
    linhas.forEach(linha => {
        const texto = linha.textContent.toLowerCase();
        if (texto.includes(busca)) {
            linha.style.display = '';
        } else {
            linha.style.display = 'none';
        }
    });
}

// Inicialização quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    // Fechar modal ao clicar fora
    const modal = document.getElementById('viewModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharVisualizacao();
            }
        });
    }
    
    // Tecla ESC para fechar modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharVisualizacao();
        }
    });
    
    // Se houver parâmetro de edição na URL
    const urlParams = new URLSearchParams(window.location.search);
    const editarId = urlParams.get('editar');
    if (editarId) {
        editarRelatorio(editarId);
    }
});