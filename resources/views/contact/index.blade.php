@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-headset"></i> Fale Conosco
    </h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Formulário de Contato -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-envelope"></i> Envie uma Mensagem
                </h5>
            </div>
            <div class="card-body">
                <form id="contactForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipo de Contato <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="suporte">🔧 Suporte Técnico</option>
                                    <option value="duvida">❓ Dúvida Geral</option>
                                    <option value="sugestao">💡 Sugestão/Melhoria</option>
                                    <option value="emergencia">🚨 Emergência</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subject" class="form-label">Assunto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Mensagem <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="6" required 
                                  placeholder="Descreva detalhadamente sua solicitação, dúvida ou problema..."></textarea>
                        <div class="form-text">Mínimo 10 caracteres, máximo 1000 caracteres.</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="sendMessageBtn">
                            <i class="bi bi-send"></i> Enviar Mensagem
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- FAQ -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-question-circle"></i> Perguntas Frequentes
                </h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                Como criar um chamado?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Para criar um chamado, acesse o menu <strong>"Chamados"</strong> > <strong>"Novo Chamado"</strong>. 
                                Preencha o título, selecione a categoria e prioridade, descreva o problema detalhadamente e clique em "Criar Chamado".
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                Qual o tempo de resposta para chamados?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <ul>
                                    <li><strong>Urgente:</strong> Até 1 hora</li>
                                    <li><strong>Alta:</strong> Até 4 horas</li>
                                    <li><strong>Média:</strong> Até 24 horas</li>
                                    <li><strong>Baixa:</strong> Até 72 horas</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                Como acompanhar meus chamados?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Acesse o menu <strong>"Meus Chamados"</strong> para ver todos os chamados que você criou, 
                                com status atual, comentários da equipe técnica e histórico de atualizações.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                Como usar o Assistente Virtual IA?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                O Assistente Virtual IA está disponível no ícone flutuante (🤖) no canto inferior direito de todas as páginas. 
                                Ele pode ajudar com dúvidas rápidas, sugerir soluções e até classificar automaticamente seus chamados.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Contatos Diretos -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-telephone"></i> Contatos Diretos
                </h5>
            </div>
            <div class="card-body">
                <div class="contact-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="contact-icon bg-success text-white rounded-circle me-3">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div>
                            <strong>Suporte Geral</strong><br>
                            <a href="tel:+554231421527" class="text-decoration-none">(42) 3142-1527</a>
                        </div>
                    </div>
                </div>

                <div class="contact-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="contact-icon bg-danger text-white rounded-circle me-3">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <strong>Emergência 24h</strong><br>
                            <a href="tel:+554231421527" class="text-decoration-none">(42) 3142-1527</a>
                        </div>
                    </div>
                </div>

                <div class="contact-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="contact-icon bg-primary text-white rounded-circle me-3">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <strong>E-mail</strong><br>
                            <a href="mailto:dtisaude@guarapuava.pr.gov.br" class="text-decoration-none">dtisaude@guarapuava.pr.gov.br</a>
                        </div>
                    </div>
                </div>

                <div class="contact-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="contact-icon bg-info text-white rounded-circle me-3">
                            <i class="bi bi-globe"></i>
                        </div>
                        <div>
                            <strong>Site Oficial</strong><br>
                            <a href="https://suportesaudeguarapuava.com.br/" target="_blank" class="text-decoration-none">suportesaudeguarapuava.com.br</a>
                        </div>
                    </div>
                </div>

                <div class="contact-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="contact-icon bg-success text-white rounded-circle me-3">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <strong>WhatsApp</strong><br>
                            <a href="https://wa.me/554231421527" target="_blank" class="text-decoration-none">(42) 3142-1527</a>
                        </div>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="d-flex align-items-center">
                        <div class="contact-icon bg-warning text-white rounded-circle me-3">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <strong>WhatsApp Sobreaviso</strong><br>
                            <small class="text-muted">Fins de semana e feriados</small><br>
                            <a href="https://wa.me/5542991235068" target="_blank" class="text-decoration-none">(42) 99123-5068</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horários de Atendimento -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock"></i> Horários de Atendimento
                </h5>
            </div>
            <div class="card-body">
                <div class="schedule-item mb-2">
                    <strong>Segunda a Sexta-feira</strong><br>
                    <span class="text-muted">08:00 às 12:00 e 13:00 às 15:00</span>
                </div>
                <div class="schedule-item mb-2">
                    <strong>Sábados e Domingos</strong><br>
                    <span class="text-warning">Atendimento em Sobreaviso</span><br>
                    <small class="text-muted">WhatsApp: (42) 99123-5068</small>
                </div>
                <hr>
                <div class="schedule-item">
                    <strong>Emergências</strong><br>
                    <span class="text-success">24 horas por dia</span>
                </div>
            </div>
        </div>

        <!-- Localização -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-building"></i> Departamento
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-gear-fill"></i> 
                        DITIS
                    </h5>
                    <p class="lead mb-0">
                        <strong>Departamento de Informação,<br>
                        Tecnologia e Inovação em Saúde</strong>
                    </p>
                    <hr>
                    <small class="text-muted">
                        Atendimento especializado em tecnologia para a área da saúde
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.contact-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.contact-item {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.contact-item:last-child {
    border-bottom: none;
}

.schedule-item {
    padding: 5px 0;
}

#contactForm .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.accordion-button:not(.collapsed) {
    background-color: #e7f3ff;
    color: #0c63e4;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const sendMessageBtn = document.getElementById('sendMessageBtn');

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Desabilitar botão durante envio
        sendMessageBtn.disabled = true;
        sendMessageBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';

        // Coletar dados do formulário
        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData.entries());

        // Validação básica
        if (data.message.length < 10) {
            showAlert('A mensagem deve ter pelo menos 10 caracteres.', 'warning');
            resetButton();
            return;
        }

        if (data.message.length > 1000) {
            showAlert('A mensagem deve ter no máximo 1000 caracteres.', 'warning');
            resetButton();
            return;
        }

        // Enviar via AJAX
        fetch('{{ route("contact.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                contactForm.reset();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('Erro ao enviar mensagem. Tente novamente.', 'danger');
        })
        .finally(() => {
            resetButton();
        });
    });

    function resetButton() {
        sendMessageBtn.disabled = false;
        sendMessageBtn.innerHTML = '<i class="bi bi-send"></i> Enviar Mensagem';
    }

    function showAlert(message, type) {
        // Remover alertas existentes
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        // Criar novo alerta
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Inserir antes do formulário
        contactForm.parentNode.insertBefore(alertDiv, contactForm);

        // Auto-remover após 5 segundos
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Contador de caracteres para mensagem
    const messageTextarea = document.getElementById('message');
    const charCountDiv = document.createElement('div');
    charCountDiv.className = 'form-text text-end';
    charCountDiv.style.marginTop = '5px';
    messageTextarea.parentNode.appendChild(charCountDiv);

    function updateCharCount() {
        const current = messageTextarea.value.length;
        const max = 1000;
        charCountDiv.textContent = `${current}/${max} caracteres`;
        
        if (current > max) {
            charCountDiv.className = 'form-text text-end text-danger';
        } else if (current > max * 0.9) {
            charCountDiv.className = 'form-text text-end text-warning';
        } else {
            charCountDiv.className = 'form-text text-end text-muted';
        }
    }

    messageTextarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Inicializar
});
</script>
@endpush
