// js/imagens.js

// Variáveis globais
let modalAberto = false;

// Função para mudar de pasta
function mudarPasta() {
    const select = document.getElementById('pastaSelect');
    const pasta = select.value;
    const busca = document.querySelector('input[name="busca"]').value;
    
    let url = 'imagens.php?';
    if (pasta) url += 'pasta=' + encodeURIComponent(pasta);
    if (busca) url += (pasta ? '&' : '') + 'busca=' + encodeURIComponent(busca);
    
    window.location.href = url;
}

// Função para abrir imagem no modal
function abrirImagem(caminho, nome) {
    const modal = document.getElementById('imagemModal');
    const modalImg = document.getElementById('modalImagem');
    const modalNome = document.getElementById('modalNome');
    const modalDownload = document.getElementById('modalDownload');
    
    modalImg.src = '../' + caminho;
    modalNome.textContent = nome;
    modalDownload.href = '../' + caminho;
    modal.style.display = 'flex';
    modalAberto = true;
    
    // Prevenir scroll do body
    document.body.style.overflow = 'hidden';
}

// Função para fechar modal
function fecharModal() {
    const modal = document.getElementById('imagemModal');
    modal.style.display = 'none';
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

// Função para verificar se é imagem
function isImagem(arquivo) {
    const extensoes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const ext = arquivo.split('.').pop().toLowerCase();
    return extensoes.includes(ext);
}

// Função para formatar data
function formatarData(dataString) {
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR');
}

// Função para formatar tamanho
function formatarTamanho(bytes) {
    if (bytes > 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes > 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' B';
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Página de imagens carregada!');
    
    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalAberto) {
            fecharModal();
        }
    });
    
    // Fechar modal clicando fora
    const modal = document.getElementById('imagemModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModal();
            }
        });
    }
    
    // Mostrar mensagem se houver parâmetro na URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('upload') === 'success') {
        mostrarMensagem('✅ Imagem enviada com sucesso!', 'sucesso');
    }
});

// Exportar funções para uso global
window.mudarPasta = mudarPasta;
window.abrirImagem = abrirImagem;
window.fecharModal = fecharModal;
window.copiarCaminho = copiarCaminho;