// js/arquivos.js

// Variáveis globais
let modalAberto = false;
let categoriaAtiva = 'todos';

// Função para mudar de pasta
function mudarPasta() {
    const select = document.getElementById('pastaSelect');
    const pasta = select.value;
    const busca = document.querySelector('input[name="busca"]').value;
    
    let url = 'arquivos.php?';
    if (pasta) url += 'pasta=' + encodeURIComponent(pasta);
    if (busca) url += (pasta ? '&' : '') + 'busca=' + encodeURIComponent(busca);
    
    window.location.href = url;
}

// Função para filtrar por categoria
function filtrarCategoria(categoria) {
    categoriaAtiva = categoria;
    
    // Atualizar abas
    document.querySelectorAll('.categoria-tab').forEach(tab => {
        if (tab.dataset.categoria === categoria) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Filtrar arquivos
    const arquivos = document.querySelectorAll('.arquivo-item');
    arquivos.forEach(arquivo => {
        if (categoria === 'todos' || arquivo.dataset.categoria === categoria) {
            arquivo.style.display = 'flex';
        } else {
            arquivo.style.display = 'none';
        }
    });
    
    // Mostrar/ocultar seção de categorias
    const categoriasGrid = document.querySelector('.categorias-grid');
    const arquivosLista = document.querySelector('.arquivos-lista');
    
    if (categoria === 'todos') {
        if (categoriasGrid) categoriasGrid.style.display = 'grid';
        if (arquivosLista) arquivosLista.style.display = 'none';
    } else {
        if (categoriasGrid) categoriasGrid.style.display = 'none';
        if (arquivosLista) arquivosLista.style.display = 'block';
    }
}

// Função para abrir detalhes do arquivo
function abrirDetalhesArquivo(arquivo) {
    const modal = document.getElementById('arquivoModal');
    const modalBody = document.getElementById('modalBody');
    
    // Construir HTML do modal
    const html = `
        <div class="info-group">
            <label><i class="fas fa-file"></i> Nome do arquivo</label>
            <p>${arquivo.nome}</p>
        </div>
        
        <div class="info-group">
            <label><i class="fas fa-tag"></i> Tipo</label>
            <p>${arquivo.extensao.toUpperCase()}</p>
        </div>
        
        <div class="info-group">
            <label><i class="fas fa-weight-hanging"></i> Tamanho</label>
            <p>${arquivo.tamanho}</p>
        </div>
        
        <div class="info-group">
            <label><i class="fas fa-calendar"></i> Data de modificação</label>
            <p>${arquivo.data}</p>
        </div>
        
        <div class="info-group">
            <label><i class="fas fa-folder"></i> Caminho completo</label>
            <p class="caminho">${arquivo.caminho}</p>
        </div>
        
        <div class="modal-actions">
            <a href="../${arquivo.caminho}" download class="btn-modal btn-modal-download">
                <i class="fas fa-download"></i> Download
            </a>
            <button class="btn-modal btn-modal-fechar" onclick="fecharModal()">
                <i class="fas fa-times"></i> Fechar
            </button>
        </div>
    `;
    
    modalBody.innerHTML = html;
    modal.style.display = 'flex';
    modalAberto = true;
    document.body.style.overflow = 'hidden';
}

// Função para fechar modal
function fecharModal() {
    document.getElementById('arquivoModal').style.display = 'none';
    modalAberto = false;
    document.body.style.overflow = 'auto';
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

// Função para copiar caminho
function copiarCaminho(caminho) {
    navigator.clipboard.writeText(caminho).then(() => {
        mostrarMensagem('📋 Caminho copiado!', 'sucesso');
    }).catch(() => {
        alert('Erro ao copiar caminho');
    });
}

// Função para formatar tamanho (cliente-side)
function formatarTamanho(bytes) {
    if (bytes > 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes > 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes > 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' B';
    }
}

// Função para obter ícone da extensão
function getIconeExtensao(ext) {
    const icones = {
        'pdf': 'fa-file-pdf',
        'doc': 'fa-file-word',
        'docx': 'fa-file-word',
        'xls': 'fa-file-excel',
        'xlsx': 'fa-file-excel',
        'ppt': 'fa-file-powerpoint',
        'pptx': 'fa-file-powerpoint',
        'txt': 'fa-file-lines',
        'csv': 'fa-file-csv',
        'zip': 'fa-file-zipper',
        'rar': 'fa-file-zipper',
        'mp3': 'fa-file-audio',
        'mp4': 'fa-file-video'
    };
    return icones[ext] || 'fa-file';
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('📁 Página de arquivos carregada!');
    
    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalAberto) {
            fecharModal();
        }
    });
    
    // Fechar modal clicando fora
    const modal = document.getElementById('arquivoModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModal();
            }
        });
    }
    
    // Inicializar categoria 'todos'
    filtrarCategoria('todos');
});

// Exportar funções para uso global
window.mudarPasta = mudarPasta;
window.filtrarCategoria = filtrarCategoria;
window.abrirDetalhesArquivo = abrirDetalhesArquivo;
window.fecharModal = fecharModal;
window.copiarCaminho = copiarCaminho;