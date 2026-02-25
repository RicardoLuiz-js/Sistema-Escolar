<?php
require_once '../php/calendario_funcoes.php'; // Seu arquivo existente

// Buscar eventos do banco de dados
$eventosDB = getEventosCalendario();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <!-- Configurações básicas do documento -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diário Escolar</title>

  <!-- Link para o arquivo CSS e ícone da página -->
  <link rel="stylesheet" href="../css/style.css">
  <link rel="icon" type="image/svg+xml" href="../imagens/calendario.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/calendario.css">
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
    <a href="index.php">
      <p>Diário Escolar</p>
    </a>
  </header>

  <!-- Conteúdo principal da página -->
  <main>
    <!-- Ícone do menu móvel -->
    <div id="mobile-menu-icon">&#9776;</div> <!-- Ícone de hambúrguer -->

    <!-- Menu de navegação -->
  <div id="Menu">
    <div class="MenuContainer">
        <div id="close-menu-icon">&#10005;</div> <!-- Botão de "X" para fechar o menu -->
        
        <a href="index.php">
            <div class="circle-container">
                   <div class="circle"><img class="icon" src="../imagens/home.png" alt=""></div>
                <p>Home</p>
            </div>
        </a>
        
        <a href="relatorios.php">
            <div class="circle-container">
                <div class="circle">
                    <i class="fas fa-file-alt fa-2x" style="color: #27ae60;"></i>
                </div>
                <p>Relatórios</p>
            </div>
        </a>
        
        <a href="imagens.php">
            <div class="circle-container">
                <div class="circle">
                    <i class="fas fa-images fa-2x" style="color: #e67e22;"></i>
                </div>
                <p>Imagens</p>
            </div>
        </a>
        
        <a href="arquivos.php">
            <div class="circle-container">
                <div class="circle">
                    <i class="fas fa-folder-open fa-2x" style="color: #f1c40f;"></i>
                </div>
                <p>Arquivos</p>
            </div>
        </a>

        <a href="limpar.php">
            <div class="circle-container">
                <div class="circle">
                    <i class="fas fa-trash-alt fa-2x" style="color: #e74c3c;"></i>
                </div>
                <p>Limpar Dados</p>
            </div>
        </a>
    </div>
</div>

    <!-- Corpo principal do conteúdo -->
    <div id="corpo">
      <!-- Cabeçalho da seção -->
      <div id="cabecalho">
        <p>Visão Geral Do Diário Escolar</p>
      </div>

      <!-- Calendário -->
      <div class="calendario-container">
        <div class="calendario-header">
          <h2 class="calendario-title"><i class="fas fa-calendar"></i> Calendário</h2>

          <div class="calendario-controls">
            <button class="btn-calendario" onclick="mudarMes(-1)">
              <i class="fas fa-chevron-left"></i> Anterior
            </button>

            <div class="mes-ano" id="mesAno">Maio 2024</div>

            <button class="btn-calendario" onclick="mudarMes(1)">
              Próximo <i class="fas fa-chevron-right"></i>
            </button>

            <button class="btn-calendario" onclick="hoje()">
              <i class="fas fa-calendar-day"></i> Hoje
            </button>

            <button class="btn-calendario btn-secondary" onclick="mostrarModalEvento()">
              <i class="fas fa-plus"></i> Novo Relatório
            </button>
          </div>
        </div>

        <!-- Cabeçalho dos dias -->
        <div class="calendario-grid" id="calendarioGrid">
          <!-- Os dias serão gerados por JavaScript -->
        </div>

        <!-- Lista de Eventos -->
        <div class="eventos-lista">
          <h3><i class="fas fa-bell"></i> Relatórios recentes</h3>
          <div id="listaEventos">
            <!-- Eventos serão carregados aqui -->
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para Adicionar/Editar Relatório -->
    <div class="eventos-modal" id="modalEvento">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-calendar-plus"></i> Novo Relatório Diário</h3>
          <button class="close-modal" onclick="fecharModal()">&times;</button>
        </div>

        <div class="modal-body">
          <form id="formEvento" onsubmit="salvarEvento(event)" enctype="multipart/form-data">
            <input type="hidden" id="eventoId" value="">
            
            <!-- Data do Relatório -->
            <div class="form-group">
              <label for="eventoData"><i class="far fa-calendar"></i> Data do Relatório</label>
              <input type="date" id="eventoData" class="form-control" required>
            </div>

            <!-- Situação do Dia -->
            <div class="form-group">
              <label><i class="fas fa-school"></i> Situação do Dia</label>
              <div class="radio-group">
                <div class="radio-option">
                  <input type="radio" id="teveAula" name="situacao" value="teveAula" checked>
                  <label for="teveAula">Teve aula</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="naoTeveAula" name="situacao" value="naoTeveAula">
                  <label for="naoTeveAula">Não teve aula</label>
                </div>
              </div>
            </div>

            <!-- Seção para quando TEM aula -->
            <div id="teveAulaSection" class="conditional-section">
              <h4><i class="fas fa-chalkboard-teacher"></i> Informações da Aula</h4>
              
              <!-- Evento (sim/não) -->
              <div class="form-group">
                <label><i class="fas fa-star"></i> Houve evento especial?</label>
                <div class="radio-group">
                  <div class="radio-option">
                    <input type="radio" id="teveEvento" name="evento" value="sim" checked>
                    <label for="teveEvento">Sim, teve evento</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="naoTeveEvento" name="evento" value="nao">
                    <label for="naoTeveEvento">Não, aula normal</label>
                  </div>
                </div>
              </div>

              <!-- Seção para QUANDO TEM EVENTO -->
              <div id="teveEventoSection">
                <div class="form-group evento-nome">
                  <label for="nomeEvento">📌 Nome do Evento:</label>
                  <input type="text" id="nomeEvento" name="nomeEvento" class="form-control" placeholder="Digite o nome do evento...">
                </div>
                
                <div class="form-group">
                  <label for="descricaoEvento">📖 Descrição do Evento:</label>
                  <textarea id="descricaoEvento" name="descricaoEvento" class="form-control" rows="3" placeholder="Descreva os detalhes do evento..."></textarea>
                </div>
                
                <div class="form-group">
                  <label for="anexosEvento">📎 Anexos (fotos, documentos):</label>
                  <div class="file-input">
                    <input type="file" id="anexosEvento" name="anexosEvento[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                  </div>
                  <small>Arquivos permitidos: JPG, PNG, PDF, DOC (múltiplos arquivos)</small>
                </div>
              </div>

              <!-- Seção para QUANDO NÃO TEM EVENTO (aula normal) -->
              <div id="naoTeveEventoSection" class="hidden">
                <div class="form-group">
                  <label for="descricaoAula">📖 Descrição da Aula:</label>
                  <textarea id="descricaoAula" name="descricaoAula" class="form-control" rows="3" placeholder="Descreva o conteúdo da aula, atividades realizadas..."></textarea>
                </div>
                
                <div class="form-group">
                  <label for="anexosAula">📎 Anexos (materiais, atividades):</label>
                  <div class="file-input">
                    <input type="file" id="anexosAula" name="anexosAula[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                  </div>
                  <small>Arquivos permitidos: JPG, PNG, PDF, DOC (múltiplos arquivos)</small>
                </div>
              </div>
            </div>

            <!-- Seção para quando NÃO TEM aula -->
            <div id="naoTeveAulaSection" class="conditional-section hidden">
              <h4><i class="fas fa-exclamation-triangle"></i> Justificativa - Sem Aula</h4>
              
              <div class="form-group">
                <label for="motivo">❓ Motivo:</label>
                <select style="height: 50px; color: black;" id="motivo" name="motivo" class="form-control">
                  <option value="">Selecione um motivo...</option>
                  <option value="feriado">Feriado</option>
                  <option value="reuniao">Reunião de professores</option>
                  <option value="formacao">Formação continuada</option>
                  <option value="greve">Greve</option>
                  <option value="problemas_estruturais">Problemas estruturais</option>
                  <option value="outros">Outros</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="descricaoSemAula">📖 Descrição/Justificativa:</label>
                <textarea id="descricaoSemAula" name="descricaoSemAula" class="form-control" rows="3" placeholder="Explique o motivo da suspensão das aulas..."></textarea>
              </div>
              
              <div class="form-group">
                <label for="anexosSemAula">📎 Anexos (comunicados, documentos):</label>
                <div class="file-input">
                  <input type="file" id="anexosSemAula" name="anexosSemAula[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                </div>
                <small>Arquivos permitidos: JPG, PNG, PDF, DOC (múltiplos arquivos)</small>
              </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 25px;">
              <button type="submit" class="btn-calendario" style="flex: 1;">
                <i class="fas fa-save"></i> Salvar Relatório
              </button>
              <button type="button" class="btn-calendario btn-secondary" onclick="fecharModal()" style="flex: 1;">
                <i class="fas fa-times"></i> Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>

  <!-- Caixa de mensagem para feedback ao usuário -->
  <div id="message-box"></div>
  <script> let eventos = <?php echo json_encode($eventosDB); ?>;</script>
  <script src="../js/calendario.js"></script>
 
</body>
</html>