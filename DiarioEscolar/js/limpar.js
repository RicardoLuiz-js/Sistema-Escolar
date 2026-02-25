// js/limpar.js

// Variáveis globais
let tipoSelecionado = null;
let senhaCorreta = false;
let backupTipoSelecionado = 'tudo';

// Função para selecionar tipo de limpeza
function selecionarTipo(tipo) {
    tipoSelecionado = tipo;
    
    // Remover seleção anterior
    document.querySelectorAll('.opcao-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Adicionar seleção ao card atual
    document.querySelector(`[data-tipo="${tipo}"]`).classList.add('selected');
    
    // Habilitar/desabilitar botão baseado na senha
    verificarBotaoLimpar();
}

// Função para selecionar tipo de backup
function selecionarBackupTipo(tipo) {
    backupTipoSelecionado = tipo;
    
    // Remover seleção anterior
    document.querySelectorAll('.backup-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Adicionar seleção ao card atual
    document.querySelector(`[data-backup="${tipo}"]`).classList.add('selected');
}

// Função para verificar senha
function verificarSenha() {
    const senhaInput = document.getElementById('senhaInput');
    const senha = senhaInput.value;
    
    if (senha === 'apagar404') {
        senhaInput.classList.remove('error');
        senhaCorreta = true;
        document.getElementById('senhaStatus').innerHTML = '<i class="fas fa-check-circle" style="color: #27ae60;"></i> Senha correta';
    } else {
        senhaInput.classList.add('error');
        senhaCorreta = false;
        document.getElementById('senhaStatus').innerHTML = '<i class="fas fa-exclamation-circle" style="color: #e74c3c;"></i> Senha incorreta';
    }
    
    verificarBotaoLimpar();
}

// Função para verificar se botão de limpar deve estar habilitado
function verificarBotaoLimpar() {
    const btnLimpar = document.getElementById('btnLimpar');
    if (senhaCorreta && tipoSelecionado) {
        btnLimpar.disabled = false;
    } else {
        btnLimpar.disabled = true;
    }
}

// Função para mostrar modal de confirmação
function mostrarConfirmacao() {
    if (!senhaCorreta || !tipoSelecionado) return;
    
    const modal = document.getElementById('confirmModal');
    const texto = document.getElementById('confirmText');
    
    const textos = {
        'imagens': 'Todas as imagens serão excluídas!',
        'arquivos': 'Todos os arquivos (PDF, DOC, etc.) serão excluídos!',
        'relatorios': 'Todos os relatórios do banco de dados serão excluídos!',
        'tudo': 'TODOS os dados serão excluídos!'
    };
    
    texto.textContent = textos[tipoSelecionado] || 'Esta ação não poderá ser desfeita!';
    modal.style.display = 'flex';
}

// Função para fechar modal de confirmação
function fecharConfirmacao() {
    document.getElementById('confirmModal').style.display = 'none';
}

// Função para executar limpeza
async function executarLimpeza() {
    fecharConfirmacao();
    mostrarLoading('Limpando dados...');
    
    try {
        const formData = new FormData();
        formData.append('acao', 'limpar');
        formData.append('tipo', tipoSelecionado);
        
        const response = await fetch('../php/limpar_ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        esconderLoading();
        
        if (resultado.success) {
            mostrarResultado(resultado);
        } else {
            mostrarMensagem('Erro: ' + resultado.message, 'erro');
        }
    } catch (error) {
        esconderLoading();
        mostrarMensagem('Erro ao executar limpeza', 'erro');
        console.error(error);
    }
}

// Função para criar backup
async function criarBackup() {
    mostrarLoading('Criando backup...');
    
    try {
        const formData = new FormData();
        formData.append('acao', 'backup');
        formData.append('tipo', backupTipoSelecionado);
        
        const response = await fetch('../php/limpar_ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        esconderLoading();
        
        if (resultado.success) {
            mostrarMensagem(`✅ Backup criado: ${resultado.arquivo}`, 'sucesso');
            setTimeout(() => location.reload(), 2000);
        } else {
            mostrarMensagem('Erro: ' + resultado.message, 'erro');
        }
    } catch (error) {
        esconderLoading();
        mostrarMensagem('Erro ao criar backup', 'erro');
        console.error(error);
    }
}

// Função para mostrar resultado
function mostrarResultado(resultado) {
    const modal = document.getElementById('resultModal');
    const body = document.getElementById('resultBody');
    
    let html = '';
    
    if (resultado.tipo === 'imagens') {
        html = `
            <div class="result-item">
                <span class="result-label">Imagens removidas:</span>
                <span class="result-value destaque">${resultado.removidos}</span>
            </div>
            <div class="result-item">
                <span class="result-label">Espaço liberado:</span>
                <span class="result-value">${resultado.espaco}</span>
            </div>
        `;
    } else if (resultado.tipo === 'arquivos') {
        html = `
            <div class="result-item">
                <span class="result-label">Arquivos removidos:</span>
                <span class="result-value destaque">${resultado.removidos}</span>
            </div>
            <div class="result-item">
                <span class="result-label">Espaço liberado:</span>
                <span class="result-value">${resultado.espaco}</span>
            </div>
        `;
    } else if (resultado.tipo === 'relatorios') {
        html = `
            <div class="result-item">
                <span class="result-label">Relatórios removidos:</span>
                <span class="result-value destaque">${resultado.removidos}</span>
            </div>
        `;
    } else if (resultado.tipo === 'tudo') {
        html = `
            <div class="result-item">
                <span class="result-label">Relatórios removidos:</span>
                <span class="result-value">${resultado.relatorios}</span>
            </div>
            <div class="result-item">
                <span class="result-label">Imagens removidas:</span>
                <span class="result-value">${resultado.imagens}</span>
            </div>
            <div class="result-item">
                <span class="result-label">Arquivos removidos:</span>
                <span class="result-value">${resultado.arquivos}</span>
            </div>
            <div class="result-item">
                <span class="result-label">Espaço liberado:</span>
                <span class="result-value destaque">${resultado.espaco}</span>
            </div>
        `;
    }
    
    body.innerHTML = html;
    
    // Atualizar header
    const header = document.querySelector('.result-modal-header');
    header.className = 'result-modal-header sucesso';
    header.innerHTML = '<i class="fas fa-check-circle"></i> Limpeza Concluída!';
    
    modal.style.display = 'flex';
}

// Função para fechar modal de resultado
function fecharResultado() {
    document.getElementById('resultModal').style.display = 'none';
    location.reload(); // Recarregar para atualizar estatísticas
}

// Função para baixar backup
function baixarBackup(nome) {
    window.location.href = `../backups/${nome}`;
}

// Função para excluir backup
async function excluirBackup(nome) {
    if (!confirm(`Tem certeza que deseja excluir o backup "${nome}"?`)) return;
    
    try {
        const formData = new FormData();
        formData.append('acao', 'excluir_backup');
        formData.append('arquivo', nome);
        
        const response = await fetch('../php/limpar_ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            mostrarMensagem('✅ Backup excluído!', 'sucesso');
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarMensagem('Erro: ' + resultado.message, 'erro');
        }
    } catch (error) {
        mostrarMensagem('Erro ao excluir backup', 'erro');
    }
}

// Função para mostrar loading
function mostrarLoading(texto = 'Processando...') {
    document.getElementById('loadingText').textContent = texto;
    document.getElementById('loadingOverlay').style.display = 'flex';
}

// Função para esconder loading
function esconderLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

// Função para mostrar mensagem
function mostrarMensagem(texto, tipo = 'sucesso') {
    const msgBox = document.getElementById('message-box');
    msgBox.textContent = texto;
    msgBox.style.backgroundColor = tipo === 'sucesso' ? '#4CAF50' : '#f44336';
    msgBox.style.display = 'block';
    
    setTimeout(() => {
        msgBox.style.display = 'none';
    }, 3000);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('🗑️ Página de limpeza carregada!');
    
    // Verificar senha ao digitar
    document.getElementById('senhaInput').addEventListener('input', verificarSenha);
    
    // Fechar modais com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharConfirmacao();
            fecharResultado();
        }
    });
    
    // Fechar modais clicando fora
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) fecharConfirmacao();
    });
    
    document.getElementById('resultModal').addEventListener('click', function(e) {
        if (e.target === this) fecharResultado();
    });
});

// Exportar funções para uso global
window.selecionarTipo = selecionarTipo;
window.selecionarBackupTipo = selecionarBackupTipo;
window.mostrarConfirmacao = mostrarConfirmacao;
window.fecharConfirmacao = fecharConfirmacao;
window.executarLimpeza = executarLimpeza;
window.criarBackup = criarBackup;
window.fecharResultado = fecharResultado;
window.baixarBackup = baixarBackup;
window.excluirBackup = excluirBackup;