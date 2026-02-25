


// Inicializar eventos do PHP
let dataAtualCalendario = new Date();
let eventoEditando = null;

// Inicializar calendário
document.addEventListener('DOMContentLoaded', function () {
    renderizarCalendario();
    renderizarListaEventos();

    // Configurar data padrão no formulário
    document.getElementById('eventoData').value = new Date().toISOString().split('T')[0];

    // Fechar modal ao clicar fora
    document.getElementById('modalEvento').addEventListener('click', function (e) {
        if (e.target === this) {
            fecharModal();
        }
    });

    // Configurar lógica condicional do formulário
    configurarFormularioCondicional();
});

// Função para configurar a lógica condicional do formulário
function configurarFormularioCondicional() {
    const teveAulaRadio = document.getElementById('teveAula');
    const naoTeveAulaRadio = document.getElementById('naoTeveAula');
    const teveAulaSection = document.getElementById('teveAulaSection');
    const naoTeveAulaSection = document.getElementById('naoTeveAulaSection');

    const teveEventoRadio = document.getElementById('teveEvento');
    const naoTeveEventoRadio = document.getElementById('naoTeveEvento');
    const teveEventoSection = document.getElementById('teveEventoSection');
    const naoTeveEventoSection = document.getElementById('naoTeveEventoSection');

    // Função para atualizar seções baseado na situação de aula
    function atualizarSecoesAula() {
        if (teveAulaRadio.checked) {
            teveAulaSection.classList.remove('hidden');
            naoTeveAulaSection.classList.add('hidden');
            atualizarSecoesEvento();
        } else {
            teveAulaSection.classList.add('hidden');
            naoTeveAulaSection.classList.remove('hidden');
            atualizarSecoesEvento();
        }
    }

    function atualizarSecoesEvento() {
        const teveAulaRadio = document.getElementById('teveAula');

        // Se NÃO TEM AULA, remove todos os required independente do evento
        if (teveAulaRadio && !teveAulaRadio.checked) {
            document.getElementById('nomeEvento').required = false;
            document.getElementById('descricaoEvento').required = false;
            document.getElementById('descricaoAula').required = false;
            return; // Sai da função, não precisa mostrar/esconder nada
        }

        // Se TEM AULA, aí sim gerencia as seções de evento
        if (teveEventoRadio.checked) {
            teveEventoSection.classList.remove('hidden');
            naoTeveEventoSection.classList.add('hidden');

            document.getElementById('nomeEvento').required = true;
            document.getElementById('descricaoEvento').required = true;
            document.getElementById('descricaoAula').required = false;
        } else {
            teveEventoSection.classList.add('hidden');
            naoTeveEventoSection.classList.remove('hidden');

            document.getElementById('nomeEvento').required = false;
            document.getElementById('descricaoEvento').required = false;
            document.getElementById('descricaoAula').required = true;
        }
    }

    // Adicionar event listeners
    teveAulaRadio.addEventListener('change', atualizarSecoesAula);
    naoTeveAulaRadio.addEventListener('change', atualizarSecoesAula);
    teveEventoRadio.addEventListener('change', atualizarSecoesEvento);
    naoTeveEventoRadio.addEventListener('change', atualizarSecoesEvento);

    // Inicializar o estado correto
    atualizarSecoesAula();
}

// Funções do calendário (mantidas exatamente como estavam)
function hoje() {
    dataAtualCalendario = new Date();
    renderizarCalendario();
}

function mudarMes(direcao) {
    dataAtualCalendario.setMonth(dataAtualCalendario.getMonth() + direcao);
    renderizarCalendario();
}

function renderizarCalendario() {
    const calendarioGrid = document.getElementById('calendarioGrid');
    const mesAno = document.getElementById('mesAno');

    // Limpar calendário
    calendarioGrid.innerHTML = '';

    // Adicionar cabeçalho dos dias
    const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    diasSemana.forEach(dia => {
        const divDia = document.createElement('div');
        divDia.className = 'dia-cabecalho';
        divDia.textContent = dia;
        calendarioGrid.appendChild(divDia);
    });

    // Atualizar título do mês/ano
    const opcoes = { month: 'long', year: 'numeric' };
    mesAno.textContent = dataAtualCalendario.toLocaleDateString('pt-BR', opcoes);

    const ano = dataAtualCalendario.getFullYear();
    const mes = dataAtualCalendario.getMonth();
    const hoje = new Date();

    // Primeiro dia do mês
    const primeiroDia = new Date(ano, mes, 1);
    const ultimoDia = new Date(ano, mes + 1, 0);
    const diasNoMes = ultimoDia.getDate();
    const primeiroDiaSemana = primeiroDia.getDay();

    // Dias do mês anterior
    for (let i = 0; i < primeiroDiaSemana; i++) {
        const data = new Date(ano, mes, -i);
        criarDiaCalendario(data.getDate(), true);
    }

    // Dias do mês atual
    for (let dia = 1; dia <= diasNoMes; dia++) {
        const data = new Date(ano, mes, dia);
        const ehHoje = data.toDateString() === hoje.toDateString();
        criarDiaCalendario(dia, false, ehHoje, data);
    }

    // Dias do próximo mês
    const totalCells = 42; // 6 semanas
    const diasRestantes = totalCells - (primeiroDiaSemana + diasNoMes);

    for (let i = 1; i <= diasRestantes; i++) {
        criarDiaCalendario(i, true);
    }

    // Adicionar eventos aos dias
    adicionarEventosAosDias();
}

function criarDiaCalendario(dia, outroMes, ehHoje = false, data = null) {
    const div = document.createElement('div');
    div.className = `dia ${outroMes ? 'outro-mes' : ''} ${ehHoje ? 'hoje' : ''}`;

    const divNumero = document.createElement('div');
    divNumero.className = 'dia-numero';
    divNumero.textContent = dia;
    div.appendChild(divNumero);

    if (data) {
        const dataISO = data.toISOString().split('T')[0];
        div.dataset.data = dataISO;

        div.addEventListener('click', function (e) {
            if (!e.target.classList.contains('evento')) {
                mostrarEventosDoDia(dataISO);
            }
        });
    }

    document.getElementById('calendarioGrid').appendChild(div);
}

function adicionarEventosAosDias() {
    // Remover eventos anteriores
    document.querySelectorAll('.evento').forEach(evento => evento.remove());

    // Adicionar novos eventos
    eventos.forEach(evento => {
        const diaElement = document.querySelector(`[data-data="${evento.data}"]`);
        if (diaElement) {
            const eventoDiv = document.createElement('div');

            // Determinar ícone baseado no tipo usando Font Awesome
            let icone = '';
            let corClasse = '';

            switch (evento.tipo) {
                case 'evento':
                    icone = '<i class="fas fa-star"></i>'; // Evento especial
                    corClasse = 'evento-evento';
                    break;
                case 'aula':
                    icone = '<i class="fas fa-chalkboard-teacher"></i>'; // Aula normal
                    corClasse = 'evento-aula';
                    break;
                case 'suspensao':
                    icone = '<i class="fas fa-ban"></i>'; // Suspensão de aula
                    corClasse = 'evento-suspensao';
                    break;
                default:
                    icone = '<i class="fas fa-calendar-check"></i>'; // Padrão
                    corClasse = 'evento-padrao';
            }

            eventoDiv.className = `evento ${corClasse}`;
            eventoDiv.innerHTML = `${icone} ${evento.titulo}`;
            eventoDiv.title = `${evento.titulo}\n${evento.descricao}`;

            eventoDiv.addEventListener('click', function (e) {
                e.stopPropagation();
                editarEvento(evento.id);
            });

            diaElement.appendChild(eventoDiv);
        }
    });
}

function renderizarListaEventos() {
    const listaEventos = document.getElementById('listaEventos');
    listaEventos.innerHTML = '';

    // Ordenar eventos por data
    const eventosOrdenados = [...eventos].sort((a, b) => new Date(a.data) - new Date(b.data));

    // Pegar próximos 10 eventos
    const proximosEventos = eventosOrdenados.slice(0, 10);

    if (proximosEventos.length === 0) {
        listaEventos.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #666;">
                    <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 10px; color: #ddd;"></i>
                    <p>Nenhum Relatório</p>
                    <button class="btn-calendario" onclick="mostrarModalEvento()" style="margin-top: 10px;">
                        <i class="fas fa-plus"></i> Adicionar Primeiro Relatório
                    </button>
                </div>
            `;
        return;
    }

    proximosEventos.forEach(evento => {
        const data = new Date(evento.data);
        const dataFormatada = data.toLocaleDateString('pt-BR', {
            weekday: 'short',
            day: '2-digit',
            month: 'short'
        });

        const cores = {
            'escolar': '#2e7d32',
            'estoque': '#1565c0',
            'bens': '#ef6c00'
        };

        const div = document.createElement('div');
        div.className = 'evento-item';
        div.innerHTML = `
                <div class="evento-info">
                    <h4>${evento.titulo}</h4>
                    <p>${evento.descricao}</p>
                    <div class="evento-data">
                        <i class="far fa-calendar"></i> ${dataFormatada}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="evento-tipo" style="background: ${cores[evento.tipo] || '#5c6bc0'}">
                        ${evento.tipo.charAt(0).toUpperCase() + evento.tipo.slice(1)}
                    </div>
                    <div class="evento-acoes">
                        <button class="btn-icone" onclick="editarEvento(${evento.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icone excluir" onclick="excluirEvento(${evento.id})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

        listaEventos.appendChild(div);
    });
}

function mostrarModalEvento(evento = null) {
    eventoEditando = evento;
    const modal = document.getElementById('modalEvento');
    const formulario = document.getElementById('formEvento');
    const tituloModal = modal.querySelector('h3');

    if (evento) {
        tituloModal.innerHTML = '<i class="fas fa-edit"></i> Editar Relatório Diário';
        document.getElementById('eventoId').value = evento.id;
        document.getElementById('eventoData').value = evento.data;

        // Aqui você precisará popular os campos específicos do relatório
        // baseado nos dados que vierem do banco
        if (evento.situacao === 'teveAula') {
            document.getElementById('teveAula').checked = true;
            if (evento.teveEvento) {
                document.getElementById('teveEvento').checked = true;
                document.getElementById('nomeEvento').value = evento.nomeEvento || '';
                document.getElementById('descricaoEvento').value = evento.descricao || '';
            } else {
                document.getElementById('naoTeveEvento').checked = true;
                document.getElementById('descricaoAula').value = evento.descricao || '';
            }
        } else {
            document.getElementById('naoTeveAula').checked = true;
            document.getElementById('motivo').value = evento.motivo || '';
            document.getElementById('descricaoSemAula').value = evento.descricao || '';
        }

        // Atualizar seções condicionais
        const teveAulaRadio = document.getElementById('teveAula');
        const naoTeveAulaRadio = document.getElementById('naoTeveAula');
        const teveEventoRadio = document.getElementById('teveEvento');

        // Disparar eventos para atualizar as seções
        if (teveAulaRadio.checked) {
            teveAulaRadio.dispatchEvent(new Event('change'));
            if (teveEventoRadio.checked) {
                teveEventoRadio.dispatchEvent(new Event('change'));
            } else {
                document.getElementById('naoTeveEvento').dispatchEvent(new Event('change'));
            }
        } else {
            naoTeveAulaRadio.dispatchEvent(new Event('change'));
        }

    } else {
        tituloModal.innerHTML = '<i class="fas fa-calendar-plus"></i> Novo Relatório Diário';
         const dataSalva = document.getElementById('eventoData').value;
        formulario.reset();
           document.getElementById('eventoData').value = dataSalva;
        // SÓ DEFINE A DATA SE ELA NÃO TIVER SIDO DEFINIDA ANTES
        const dataInput = document.getElementById('eventoData');
        if (!dataInput.value) {
            dataInput.value = new Date().toISOString().split('T')[0];}

            // Resetar seções condicionais
            document.getElementById('teveAula').checked = true;
            document.getElementById('teveEvento').checked = true;
            document.getElementById('teveAula').dispatchEvent(new Event('change'));
            document.getElementById('teveEvento').dispatchEvent(new Event('change'));
        }

        modal.style.display = 'flex';
    }

    function fecharModal() {
        document.getElementById('modalEvento').style.display = 'none';
        eventoEditando = null;
        document.getElementById('formEvento').reset();
    }

    async function salvarEvento(e) {
        e.preventDefault();

        const data = document.getElementById('eventoData').value;
        const id = document.getElementById('eventoId').value;
        const situacao = document.querySelector('input[name="situacao"]:checked').value;

        // VALIDAÇÃO DA DATA
        if (!data) {
            alert('Por favor, selecione a data do relatório.');
            return;
        }

        // VERIFICAR SE JÁ EXISTE EVENTO NESTA DATA (exceto se for edição do mesmo)
        if (!id) { // Só verifica se for novo relatório (não edição)
            const eventoExistente = eventos.find(evento => evento.data === data);

            if (eventoExistente) {
                const dataFormatada = new Date(data).toLocaleDateString('pt-BR');
                alert(`❌ Já existe um relatório para o dia ${dataFormatada}.\n\n` +
                    `Título: ${eventoExistente.titulo}\n` +
                    `Por favor, edite o relatório existente ou escolha outra data.`);

                // Reabilitar botão
                const botaoSalvar = e.target.querySelector('button[type="submit"]');
                botaoSalvar.disabled = false;
                return;
            }
        }

        // Determinar título baseado na situação
        let titulo = '';
        let descricao = '';
        let tipo = 'escolar';
        let teveEvento = false;
        let nomeEvento = '';
        let motivo = '';

        if (situacao === 'teveAula') {
            teveEvento = document.getElementById('teveEvento').checked;

            if (teveEvento) {
                nomeEvento = document.getElementById('nomeEvento').value;
                descricao = document.getElementById('descricaoEvento').value;
                titulo = `Evento: ${nomeEvento}`;
                tipo = 'evento';

                if (!nomeEvento || !descricao) {
                    alert('Por favor, preencha o nome e descrição do evento.');
                    return;
                }
            } else {
                descricao = document.getElementById('descricaoAula').value;
                titulo = `Aula: ${data}`;
                tipo = 'aula';

                if (!descricao) {
                    alert('Por favor, preencha a descrição da aula.');
                    return;
                }
            }
        } else {
            motivo = document.getElementById('motivo').value;
            descricao = document.getElementById('descricaoSemAula').value;
            titulo = `Sem Aula: ${motivo}`;
            tipo = 'suspensao';

            if (!motivo || !descricao) {
                alert('Por favor, selecione o motivo e preencha a justificativa.');
                return;
            }
        }

        const botaoSalvar = e.target.querySelector('button[type="submit"]');
        const textoOriginal = botaoSalvar.innerHTML;
        botaoSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
        botaoSalvar.disabled = true;

        try {
            // Criar FormData com todos os campos
            const formData = new FormData();
            formData.append('action', id ? 'update' : 'add');
            if (id) formData.append('id', id);
            formData.append('titulo', titulo);
            formData.append('descricao', descricao);
            formData.append('data', data);
            formData.append('tipo', tipo);
            formData.append('situacao', situacao);
            formData.append('teveEvento', teveEvento ? '1' : '0');
            formData.append('nomeEvento', nomeEvento);
            formData.append('motivo', motivo);

            // Adicionar arquivos
            const anexosEvento = document.getElementById('anexosEvento').files;
            const anexosAula = document.getElementById('anexosAula').files;
            const anexosSemAula = document.getElementById('anexosSemAula').files;

            for (let i = 0; i < anexosEvento.length; i++) {
                formData.append('anexosEvento[]', anexosEvento[i]);
            }
            for (let i = 0; i < anexosAula.length; i++) {
                formData.append('anexosAula[]', anexosAula[i]);
            }
            for (let i = 0; i < anexosSemAula.length; i++) {
                formData.append('anexosSemAula[]', anexosSemAula[i]);
            }

            const response = await fetch('../php/calendario_ajax.php', {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (resultado.success) {
                // Recarregar eventos do banco
                const responseEventos = await fetch('../php/carregar_eventos.php');
                eventos = await responseEventos.json();

                // Atualizar interface
                renderizarCalendario();
                renderizarListaEventos();
                fecharModal();

                // Feedback visual
                botaoSalvar.innerHTML = '<i class="fas fa-check"></i> Salvo!';
                botaoSalvar.style.background = '#2e7d32';

                setTimeout(() => {
                    botaoSalvar.innerHTML = textoOriginal;
                    botaoSalvar.style.background = '';
                    botaoSalvar.disabled = false;
                }, 1500);
            } else {
                throw new Error(resultado.message || 'Erro ao salvar');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao salvar relatório: ' + error.message);
            botaoSalvar.innerHTML = textoOriginal;
            botaoSalvar.disabled = false;
        }
    }
    async function editarEvento(id) {
        const evento = eventos.find(e => e.id == id);
        if (evento) {
            mostrarModalEvento(evento);
        }
    }

    async function excluirEvento(id) {
        if (!confirm('Tem certeza que deseja excluir este relatório?')) {
            return;
        }

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
                // Remover da lista local
                eventos = eventos.filter(e => e.id != id);

                // Atualizar interface
                renderizarCalendario();
                renderizarListaEventos();

                alert('Relatório excluído com sucesso!');
            } else {
                throw new Error(resultado.message || 'Erro ao excluir');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao excluir relatório: ' + error.message);
        }
    }

    function mostrarEventosDoDia(dataISO) {
        const eventosDoDia = eventos.filter(e => e.data === dataISO);
        const data = new Date(dataISO);
        const dataFormatada = data.toLocaleDateString('pt-BR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        if (eventosDoDia.length === 0) {
            // Mostrar modal para adicionar evento neste dia
            document.getElementById('eventoData').value = dataISO;
            mostrarModalEvento();
            return;
        }

        let mensagem = `Relatórios para ${dataFormatada}:\n\n`;
        eventosDoDia.forEach(evento => {
            mensagem += `• ${evento.titulo}\n`;
            if (evento.descricao) {
                mensagem += `  ${evento.descricao}\n`;
            }
            mensagem += '\n';
        });

        mensagem += '\nClique em um relatório no calendário para editá-lo.';
        alert(mensagem);
    }
