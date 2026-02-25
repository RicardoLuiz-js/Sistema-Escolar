<!DOCTYPE html>
<html lang="pt-br">

<head>
  <!-- Configurações básicas do documento -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Controle de Estoque</title>
  
  <!-- Link para o arquivo CSS e ícone da página -->
  <link rel="stylesheet" href="../css/style.css">
<link rel="icon" type="image/svg+xml" href="../imagens/icon.svg">
  
  <!-- Estilos inline para a caixa de mensagem -->
  <style>
    #message-box {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        border-radius: 5px;
        display: none;
        z-index: 1000;
    }
  </style>
</head>

<body>

  <!-- Cabeçalho da página -->
  <header>
    <a href="index.html">
      <p>Controle De Estoque</p>
    </a>
  </header>

  <!-- Conteúdo principal da página -->
  <main>

    <!-- Ícone do menu móvel -->
    <div id="mobile-menu-icon">&#9776;</div> <!-- Ícone de hambúrguer -->
    
    <!-- Menu de navegação -->
    <div id="Menu">    
      <div id="close-menu-icon">&#10005;</div> <!-- Botão de "X" para fechar o menu -->
      <a href="index.html">  
        <div class="circle-container">  
          <div class="circle"><img class="icon" src="../imagens/home.png" alt=""></div>
          <p>Home</p>
        </div>
      </a>
      <a href="adicionarhtml.php">  
        <div class="circle-container"> 
          <div class="circle"><img class="icon" src="../imagens/adicionar.png" alt=""></div>
          <p>Adicionar</p>
        </div>
      </a>
      <a href="utilizar.html"> 
        <div class="circle-container"> 
          <div class="circle"><img class="icon" src="../imagens/botao-de-menos.png" alt=""></div>
          <p>Utilizar</p>
        </div>
      </a>
      <a href="LimparDados.html"> 
        <div class="circle-container">
          <div class="circle"><img class="icon1" src="../imagens/Limpar.png" alt=""></div>
          <p>Limpar Dados</p>
        </div>
      </a>
     
       <a href="imprimir-estoque.html"> 
        <div class="circle-container"> 
          <div class="circle"><img class="icon1" src="../imagens/pdf.png" alt=""></div>
          <p>Imprimir</p>
        </div>
      </a>
    </div>

    <!-- Corpo principal do conteúdo -->
    <div id="corpo">
      <div id="cabecalho">
        <p>Cadastro de Produtos</p>
      </div>

      <!-- Container para o formulário de cadastro de produtos -->
      <div id="container-pai">
        <div class="cab"> 
          <h1>Adicione Produtos ao Estoque</h1>
        </div>
        <div id="container-filho">
          <form class="form" action="../php/adicionar.php" method="POST">
            <div class="form-caixa">
              <label for="nome">Nome do Produto:</label>
              <input type="text" id="nome" name="nome" required>
            </div> 
            <div class="form-caixa"> 
              <label for="descricao">Descrição:</label>
              <input type="text" id="descricao" name="descricao">
            </div> 
            <div class="form-caixa">
              <label for="categoria">Categoria:</label>
              <select id="categoria" name="categoria" class="selectw">
                <option value="Alimentos não perecíveis">Alimentos não perecíveis</option>
                <option value="Alimentos perecíveis">Alimentos perecíveis</option>
                <option value="Frios e carnes">Frios e carnes</option>
                <option value="Limpeza">Limpeza</option>
                <!-- Adicione mais opções conforme necessário -->
              </select>
            </div>  
            <div class="form-caixa"> 
              <label for="quantidade">Quantidade:</label>
              <input type="number" id="quantidade" name="quantidade" required>
            </div> 
            <div class="form-caixa">
              <label for="unidade">Unidade:</label>
              <select id="unidade" name="unidade" class="selectw">
                <option value="kg">Kg</option>
                <option value="litro">Litro</option>
                <option value="unidade">Unidade</option>
                <option value="ml">ml</option> 
                <option value="g">g</option> 
                <!-- Adicione mais unidades conforme necessário -->
              </select>
            </div> 
            <div class="form-caixa">
              <label for="fornecedor">Entrada:</label>
              <input type="date" id="entrada" name="entrada" required>
            </div> 
            <div class="form-caixa">
              <label for="validade">Data de Validade:</label>
              <input type="date" id="validade" name="validade">
            </div> 
            <div class="button-container">
              <button class="button-cadastro" type="submit">Cadastrar Produto</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </main>

  <!-- Caixa de mensagem para feedback ao usuário -->
  <div id="message-box"></div>
  
  <!-- Scripts JavaScript -->
  <script>
    // Função para exibir a mensagem
    function showMessage(message) {
        var messageBox = document.getElementById('message-box');
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

    // Verifica se há uma mensagem na URL (enviada pelo PHP)
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    if (message) {
        showMessage(message);
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
  </script>
</body>

</html>