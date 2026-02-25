

        function carregarTotal() {

            // Buscar o total de itens
            fetch('../../ControleDeBens/php/moveis/contar.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-itens').textContent =
                        `${data.total_itens}`;
                })
                .catch(error => {
                    console.error('Erro ao contar móveis:', error);
                });
        }

        carregarTotal();





        // Simulação de dados dinâmicos
        document.addEventListener('DOMContentLoaded', function () {
            // Atualizar data e hora
            function updateDateTime() {
                const now = new Date();
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.querySelector('.welcome p').textContent = now.toLocaleDateString('pt-BR', options);
            }

            updateDateTime();

            // Interação com os cards
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('click', function () {
                    cards.forEach(c => c.style.border = 'none');
                    this.style.border = '2px solid #5c6bc0';
                });
            });

            // Efeito de loading ao clicar nos botões
            const buttons = document.querySelectorAll('.btn');

            buttons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault(); // bloqueia o href padrão

                    const tipo = this.getAttribute('data-tipo').toLowerCase();
                    let href = '';

                    switch (tipo) {
                        case 'estoque':
                            href = '../../ControleDeEstoque/html/index.html';
                            break;
                        case 'bens':
                            href = '../../ControleDeBens/html/index.html';
                            break;
                        case 'diario':
                            href = '../../DiarioEscolar/html/index.php';
                            break;
                        default:
                            alert('Tipo desconhecido: ' + tipo);
                            return;
                    }

                    const originalText = this.textContent;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Carregando...';

                    setTimeout(() => {
                        this.textContent = originalText;
                       
                        window.open(href,"_blank");
                    }, 1500);
                });
            });


        });