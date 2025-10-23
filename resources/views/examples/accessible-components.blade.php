{{--
    Exemplo de Uso dos Componentes Acessíveis
    
    Esta view demonstra como usar todos os componentes
    de acessibilidade e responsividade criados.
--}}

@extends('layouts.app')

@section('content')
<div class="container-responsive py-responsive">
    <!-- Breadcrumb -->
    <nav aria-label="Navegação estrutural (breadcrumb)">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Exemplo de Componentes</li>
        </ol>
    </nav>

    <!-- Cabeçalho da Página -->
    <div class="mb-responsive">
        <h1 class="heading-responsive">Exemplo de Componentes Acessíveis</h1>
        <p class="text-responsive">
            Esta página demonstra o uso dos componentes acessíveis e responsivos implementados no sistema.
        </p>
    </div>

    <!-- Alertas -->
    <section aria-labelledby="section-alerts" class="mb-responsive">
        <h2 id="section-alerts">Alertas</h2>
        
        @include('components.accessible-alert', [
            'type' => 'success',
            'title' => 'Operação bem-sucedida!',
            'message' => 'O chamado foi criado com sucesso.',
            'dismissible' => true,
            'autoDismiss' => 5000
        ])
        
        @include('components.accessible-alert', [
            'type' => 'warning',
            'title' => 'Atenção!',
            'message' => 'Este chamado precisa de atenção urgente.',
            'dismissible' => true
        ])
        
        @include('components.accessible-alert', [
            'type' => 'danger',
            'title' => 'Erro!',
            'message' => 'Não foi possível processar a solicitação.',
            'dismissible' => true
        ])
        
        @include('components.accessible-alert', [
            'type' => 'info',
            'title' => 'Informação',
            'message' => 'Você tem 3 chamados pendentes.',
            'dismissible' => true
        ])
    </section>

    <!-- Botões -->
    <section aria-labelledby="section-buttons" class="mb-responsive">
        <h2 id="section-buttons">Botões</h2>
        
        <div class="btn-group-responsive mb-3">
            @include('components.accessible-button', [
                'text' => 'Botão Primário',
                'variant' => 'primary',
                'icon' => 'bi-check',
                'ariaLabel' => 'Salvar alterações'
            ])
            
            @include('components.accessible-button', [
                'text' => 'Botão Secundário',
                'variant' => 'secondary',
                'icon' => 'bi-pencil',
                'ariaLabel' => 'Editar item'
            ])
            
            @include('components.accessible-button', [
                'text' => 'Botão de Sucesso',
                'variant' => 'success',
                'icon' => 'bi-check-circle',
                'ariaLabel' => 'Confirmar ação'
            ])
            
            @include('components.accessible-button', [
                'text' => 'Botão de Perigo',
                'variant' => 'danger',
                'icon' => 'bi-trash',
                'ariaLabel' => 'Excluir item'
            ])
        </div>

        <div class="btn-group-responsive mb-3">
            @include('components.accessible-button', [
                'text' => 'Botão com Ícone à Direita',
                'variant' => 'primary',
                'icon' => 'bi-arrow-right',
                'iconPosition' => 'right',
                'ariaLabel' => 'Próxima etapa'
            ])
            
            @include('components.accessible-button', [
                'text' => 'Botão Carregando',
                'variant' => 'primary',
                'loading' => true,
                'disabled' => true,
                'ariaLabel' => 'Processando'
            ])
        </div>
    </section>

    <!-- Formulário -->
    <section aria-labelledby="section-form" class="mb-responsive">
        <h2 id="section-form">Formulário Acessível</h2>
        
        <div class="card-responsive">
            <form data-validate>
                <div class="form-row-responsive cols-2">
                    @include('components.accessible-input', [
                        'name' => 'name',
                        'label' => 'Nome Completo',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Digite seu nome',
                        'helpText' => 'Digite seu nome completo conforme documento'
                    ])
                    
                    @include('components.accessible-input', [
                        'name' => 'email',
                        'label' => 'E-mail',
                        'type' => 'email',
                        'required' => true,
                        'placeholder' => 'usuario@empresa.com',
                        'helpText' => 'Digite seu e-mail corporativo',
                        'autocomplete' => 'email'
                    ])
                </div>
                
                <div class="form-row-responsive cols-2">
                    @include('components.accessible-input', [
                        'name' => 'phone',
                        'label' => 'Telefone',
                        'type' => 'tel',
                        'placeholder' => '(00) 00000-0000',
                        'helpText' => 'Formato: (00) 00000-0000'
                    ])
                    
                    @include('components.accessible-input', [
                        'name' => 'department',
                        'label' => 'Departamento',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Ex: TI, RH, Financeiro'
                    ])
                </div>
                
                <div class="form-group">
                    <label for="message" class="form-label form-label-required">
                        Mensagem
                        <span class="text-danger" aria-label="obrigatório">*</span>
                    </label>
                    <textarea 
                        id="message" 
                        name="message" 
                        class="form-control" 
                        rows="4"
                        required
                        aria-required="true"
                        placeholder="Digite sua mensagem..."
                    ></textarea>
                    <small class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        Descreva detalhadamente sua solicitação
                    </small>
                </div>
                
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="terms" 
                        name="terms"
                        required
                        aria-required="true"
                    >
                    <label class="form-check-label" for="terms">
                        Aceito os termos e condições
                    </label>
                </div>
                
                <div class="btn-group-responsive mt-responsive">
                    @include('components.accessible-button', [
                        'text' => 'Enviar Formulário',
                        'type' => 'submit',
                        'variant' => 'primary',
                        'icon' => 'bi-send',
                        'ariaLabel' => 'Enviar formulário'
                    ])
                    
                    @include('components.accessible-button', [
                        'text' => 'Cancelar',
                        'type' => 'button',
                        'variant' => 'outline',
                        'ariaLabel' => 'Cancelar e voltar'
                    ])
                </div>
            </form>
        </div>
    </section>

    <!-- Cards Responsivos -->
    <section aria-labelledby="section-cards" class="mb-responsive">
        <h2 id="section-cards">Cards em Grid Responsivo</h2>
        
        <div class="grid-auto">
            <div class="card-responsive">
                <div class="card-header-responsive">
                    <h3 class="h5 mb-0">Card 1</h3>
                </div>
                <div class="card-body-responsive">
                    <p>Este é um card responsivo que se adapta automaticamente ao tamanho da tela.</p>
                </div>
                <div class="card-footer-responsive">
                    @include('components.accessible-button', [
                        'text' => 'Ver Mais',
                        'variant' => 'primary',
                        'size' => 'sm'
                    ])
                </div>
            </div>
            
            <div class="card-responsive">
                <div class="card-header-responsive">
                    <h3 class="h5 mb-0">Card 2</h3>
                </div>
                <div class="card-body-responsive">
                    <p>Em mobile, os cards ficam empilhados. Em desktop, lado a lado.</p>
                </div>
                <div class="card-footer-responsive">
                    @include('components.accessible-button', [
                        'text' => 'Ver Mais',
                        'variant' => 'secondary',
                        'size' => 'sm'
                    ])
                </div>
            </div>
            
            <div class="card-responsive">
                <div class="card-header-responsive">
                    <h3 class="h5 mb-0">Card 3</h3>
                </div>
                <div class="card-body-responsive">
                    <p>O grid se ajusta automaticamente baseado no espaço disponível.</p>
                </div>
                <div class="card-footer-responsive">
                    @include('components.accessible-button', [
                        'text' => 'Ver Mais',
                        'variant' => 'success',
                        'size' => 'sm'
                    ])
                </div>
            </div>
        </div>
    </section>

    <!-- Tabela Responsiva -->
    <section aria-labelledby="section-table" class="mb-responsive">
        <h2 id="section-table">Tabela Responsiva</h2>
        
        <div class="table-wrapper-responsive">
            <table class="table table-mobile-card" role="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Status</th>
                        <th scope="col">Prioridade</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="ID">#001</td>
                        <td data-label="Nome">Chamado de Exemplo</td>
                        <td data-label="Status">
                            <span class="badge-responsive badge-success">Aberto</span>
                        </td>
                        <td data-label="Prioridade">
                            <span class="badge-responsive badge-danger">Alta</span>
                        </td>
                        <td data-label="Ações">
                            @include('components.accessible-button', [
                                'text' => 'Ver',
                                'variant' => 'primary',
                                'size' => 'sm',
                                'icon' => 'bi-eye'
                            ])
                        </td>
                    </tr>
                    <tr>
                        <td data-label="ID">#002</td>
                        <td data-label="Nome">Outro Chamado</td>
                        <td data-label="Status">
                            <span class="badge-responsive badge-warning">Em Andamento</span>
                        </td>
                        <td data-label="Prioridade">
                            <span class="badge-responsive badge-info">Média</span>
                        </td>
                        <td data-label="Ações">
                            @include('components.accessible-button', [
                                'text' => 'Ver',
                                'variant' => 'primary',
                                'size' => 'sm',
                                'icon' => 'bi-eye'
                            ])
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal -->
    <section aria-labelledby="section-modal" class="mb-responsive">
        <h2 id="section-modal">Modal Acessível</h2>
        
        @include('components.accessible-button', [
            'text' => 'Abrir Modal',
            'variant' => 'primary',
            'attributes' => ['data-modal-target' => 'exampleModal']
        ])
    </section>
</div>

<!-- Modal Example -->
@include('components.accessible-modal', [
    'id' => 'exampleModal',
    'title' => 'Título do Modal',
    'size' => 'lg'
])
    <p>Este é um modal totalmente acessível com:</p>
    <ul>
        <li>Armadilha de foco (Tab fica dentro do modal)</li>
        <li>Fechamento com ESC</li>
        <li>Foco retorna ao botão que abriu</li>
        <li>ARIA roles apropriados</li>
        <li>Backdrop clicável</li>
    </ul>
    
    @slot('footerSlot')
        <div class="d-flex gap-2">
            @include('components.accessible-button', [
                'text' => 'Cancelar',
                'variant' => 'outline',
                'attributes' => ['data-modal-close' => true]
            ])
            
            @include('components.accessible-button', [
                'text' => 'Confirmar',
                'variant' => 'primary',
                'icon' => 'bi-check'
            ])
        </div>
    @endslot
@endsection

@section('styles')
<style>
    /* Estilos adicionais para a demo */
    .demo-section {
        background: var(--color-gray-50);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-lg);
    }
</style>
@endsection

@section('scripts')
<script>
    // Script de exemplo para demonstração
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Página de exemplos carregada!');
        
        // Exemplo de uso da API de acessibilidade
        const form = document.querySelector('form[data-validate]');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Anunciar sucesso
                window.AccessibilityHelper.announceToScreenReader(
                    'Formulário enviado com sucesso!'
                );
                
                // Mostrar alerta
                alert('Formulário enviado! (Em produção, faria o envio real)');
            });
        }
    });
</script>
@endsection
