document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Página de CADASTRO carregada');
    
    // Só executa se estiver na página de cadastro
    const formCadastro = document.getElementById('form-cadastro');
    if (!formCadastro) {
        console.log('⚠️  Não é página de cadastro, saindo...');
        return;
    }
    
    const btnCadastrar = formCadastro.querySelector('.button-cadastro');
    
    formCadastro.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btnOriginalText = btnCadastrar.textContent;
        
        try {
            btnCadastrar.disabled = true;
            btnCadastrar.textContent = 'Cadastrando...';
            
            const formData = {
                nome: document.getElementById('nome').value.trim(),
                descricao: document.getElementById('descricao').value.trim(),
                localizacao: document.getElementById('localizacao').value.trim(),
                codigo: document.getElementById('codigo').value.trim()
            };
            
            if (!formData.nome || !formData.localizacao || !formData.codigo) {
                mostrarErro('Preencha todos os campos obrigatórios');
                return;
            }
            
            const response = await fetch('../php/moveis/cadastro.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarSucesso(data.message);
                limparApenasCamposDestravados()
                
              
                
            } else {
                mostrarErro(data.message);
            }
            
        } catch (error) {
            console.error('Erro:', error);
            mostrarErro('Erro de conexão. Tente novamente.');
        } finally {
            btnCadastrar.disabled = false;
            btnCadastrar.textContent = btnOriginalText;
        }
    });
});

function inicializarCadeados() {
    const botoesCadeado = document.querySelectorAll('[data-cadeado-id]');
    
    botoesCadeado.forEach(botao => {
        const cadeadoId = botao.dataset.cadeadoId;
        const imagem = botao.querySelector('img');
        let imagemAtual = 1;
        
        const imagens = {
            1: "../imagens/image1.png", // fechado
            2: "../imagens/cadeado-aberto.png"  // aberto
        };
        
        botao.addEventListener('click', () => {
            imagemAtual = imagemAtual === 1 ? 2 : 1;
            imagem.src = imagens[imagemAtual];
            imagem.alt = `Imagem ${imagemAtual}`;
            
            console.log(`Cadeado ${cadeadoId} pressionado! Estado: ${imagemAtual === 1 ? 'fechado' : 'aberto'}`);
            
            // Você pode usar o ID para ações específicas
            executarAcaoCadeado(cadeadoId, imagemAtual);
        });
    });
}

// Modifique a função executarAcaoCadeado
function executarAcaoCadeado(id, estado) {
    const campoId = id.replace('cadeado', '');
    const input = document.getElementById(['nome', 'descricao', 'localizacao'][campoId - 1]);
    
    // Adicionar atributo personalizado para controlar
    if (estado === 1) { // FECHADO
        input.setAttribute('data-travado', 'true');
    } else { // ABERTO
        input.removeAttribute('data-travado');
    }
}

// E na limpeza:
function limparApenasCamposDestravados() {
    document.getElementById('codigo').value = '';
    
    // Só limpa se NÃO estiver travado
    if (!document.getElementById('nome').hasAttribute('data-travado')) {
        document.getElementById('nome').value = '';
    }
    if (!document.getElementById('descricao').hasAttribute('data-travado')) {
        document.getElementById('descricao').value = '';
    }
    if (!document.getElementById('localizacao').hasAttribute('data-travado')) {
        document.getElementById('localizacao').value = '';
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', inicializarCadeados);
