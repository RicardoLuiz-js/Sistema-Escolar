// Função para mostrar notificações
 // Função para exibir a mensagem
function mostrarNotificacao(message, color) {
    const messageBox = document.getElementById('message-box');

    // Lista de cores permitidas
    const coresValidas = ['green', 'red', 'yellow'];

    // Se a cor não for válida, usar 'yellow' como padrão
    const corFinal = coresValidas.includes(color) ? color : 'yellow';

    messageBox.style.backgroundColor = corFinal;
    messageBox.textContent = message;
    messageBox.style.display = 'block';


    // Faz a mensagem subir e depois desaparecer
    setTimeout(function() {
        messageBox.style.transition = "bottom 3s";
        messageBox.style.bottom = "400px";
    }, 100);

    setTimeout(function() {
        messageBox.style.display = 'none';
        
    }, 5000); // A mensagem desaparece após 5 segundos

}




// Função para notificação de sucesso
function mostrarSucesso(mensagem) {
    return mostrarNotificacao(mensagem, 'green');
}

// Função para notificação de erro
function mostrarErro(mensagem) {
    return mostrarNotificacao(mensagem, 'red');
}

// Função para notificação informativa
function mostrarInfo(mensagem) {
    return mostrarNotificacao(mensagem, 'yellow');
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






window.mostrarSucesso = mostrarSucesso;
window.mostrarErro = mostrarErro;
window.mostrarInfo = mostrarInfo;
window.mostrarNotificacao = mostrarNotificacao;