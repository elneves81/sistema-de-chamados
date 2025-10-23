/**
 * =====================================================
 * MELHORIAS DE ACESSIBILIDADE E UX - JavaScript
 * =====================================================
 * 
 * Este arquivo implementa funcionalidades JavaScript para
 * melhorar a acessibilidade e experiência do usuário
 */

(function() {
    'use strict';

    // ========================================
    // 1. GERENCIAMENTO DE FOCO
    // ========================================

    /**
     * Armadilha de foco para modais (WCAG 2.1.2)
     * Mantém o foco dentro do modal quando aberto
     */
    class FocusTrap {
        constructor(element) {
            this.element = element;
            this.focusableElements = null;
            this.firstFocusable = null;
            this.lastFocusable = null;
            this.previousActiveElement = null;
        }

        activate() {
            this.previousActiveElement = document.activeElement;
            this.updateFocusableElements();
            
            if (this.firstFocusable) {
                this.firstFocusable.focus();
            }

            this.element.addEventListener('keydown', this.handleKeyDown.bind(this));
        }

        deactivate() {
            this.element.removeEventListener('keydown', this.handleKeyDown.bind(this));
            
            if (this.previousActiveElement && this.previousActiveElement.focus) {
                this.previousActiveElement.focus();
            }
        }

        updateFocusableElements() {
            const focusableSelectors = [
                'a[href]',
                'button:not([disabled])',
                'textarea:not([disabled])',
                'input:not([disabled])',
                'select:not([disabled])',
                '[tabindex]:not([tabindex="-1"])'
            ].join(',');

            this.focusableElements = Array.from(
                this.element.querySelectorAll(focusableSelectors)
            );
            this.firstFocusable = this.focusableElements[0];
            this.lastFocusable = this.focusableElements[this.focusableElements.length - 1];
        }

        handleKeyDown(e) {
            if (e.key !== 'Tab') return;

            if (e.shiftKey) {
                if (document.activeElement === this.firstFocusable) {
                    e.preventDefault();
                    this.lastFocusable.focus();
                }
            } else {
                if (document.activeElement === this.lastFocusable) {
                    e.preventDefault();
                    this.firstFocusable.focus();
                }
            }
        }
    }

    // ========================================
    // 2. NAVEGAÇÃO POR TECLADO
    // ========================================

    /**
     * Adiciona navegação por teclado a elementos customizados
     */
    function setupKeyboardNavigation() {
        // ESC para fechar modais
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    closeModal(openModal);
                }

                const openSidebar = document.querySelector('.sidebar-responsive.open');
                if (openSidebar) {
                    closeSidebar();
                }
            }
        });

        // Navegação em listas com setas
        document.querySelectorAll('[role="listbox"]').forEach(listbox => {
            listbox.addEventListener('keydown', function(e) {
                const items = Array.from(this.querySelectorAll('[role="option"]'));
                const currentIndex = items.indexOf(document.activeElement);

                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        if (currentIndex < items.length - 1) {
                            items[currentIndex + 1].focus();
                        }
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        if (currentIndex > 0) {
                            items[currentIndex - 1].focus();
                        }
                        break;
                    case 'Home':
                        e.preventDefault();
                        items[0].focus();
                        break;
                    case 'End':
                        e.preventDefault();
                        items[items.length - 1].focus();
                        break;
                }
            });
        });
    }

    // ========================================
    // 3. ANÚNCIOS PARA LEITORES DE TELA
    // ========================================

    /**
     * Cria região de anúncios ARIA live
     */
    function createAriaLiveRegion() {
        if (document.getElementById('aria-live-region')) return;

        const liveRegion = document.createElement('div');
        liveRegion.id = 'aria-live-region';
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        document.body.appendChild(liveRegion);
    }

    /**
     * Anuncia mensagem para leitores de tela
     */
    function announceToScreenReader(message, priority = 'polite') {
        createAriaLiveRegion();
        const liveRegion = document.getElementById('aria-live-region');
        liveRegion.setAttribute('aria-live', priority);
        liveRegion.textContent = message;

        // Limpar após 1 segundo
        setTimeout(() => {
            liveRegion.textContent = '';
        }, 1000);
    }

    // ========================================
    // 4. GERENCIAMENTO DE MODAIS
    // ========================================

    let currentFocusTrap = null;

    /**
     * Abre modal com acessibilidade
     */
    function openModal(modalElement) {
        if (!modalElement) return;

        // Prevenir scroll do body
        document.body.style.overflow = 'hidden';

        // Mostrar modal
        modalElement.classList.add('show');
        modalElement.setAttribute('aria-hidden', 'false');

        // Ativar armadilha de foco
        currentFocusTrap = new FocusTrap(modalElement);
        currentFocusTrap.activate();

        // Anunciar abertura
        const modalTitle = modalElement.querySelector('.modal-title');
        if (modalTitle) {
            announceToScreenReader(`Modal aberto: ${modalTitle.textContent}`);
        }
    }

    /**
     * Fecha modal com acessibilidade
     */
    function closeModal(modalElement) {
        if (!modalElement) return;

        // Restaurar scroll
        document.body.style.overflow = '';

        // Esconder modal
        modalElement.classList.remove('show');
        modalElement.setAttribute('aria-hidden', 'true');

        // Desativar armadilha de foco
        if (currentFocusTrap) {
            currentFocusTrap.deactivate();
            currentFocusTrap = null;
        }

        // Anunciar fechamento
        announceToScreenReader('Modal fechado');
    }

    /**
     * Inicializa modais
     */
    function setupModals() {
        // Botões que abrem modals
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-modal-target');
                const modal = document.getElementById(targetId);
                openModal(modal);
            });
        });

        // Botões de fechar modal
        document.querySelectorAll('[data-modal-close]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                closeModal(modal);
            });
        });

        // Fechar ao clicar no backdrop
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.addEventListener('click', function() {
                const modal = this.closest('.modal');
                closeModal(modal);
            });
        });
    }

    // ========================================
    // 5. SIDEBAR RESPONSIVA
    // ========================================

    /**
     * Abre sidebar
     */
    function openSidebar() {
        const sidebar = document.querySelector('.sidebar-responsive');
        const backdrop = document.querySelector('.sidebar-backdrop');

        if (!sidebar) return;

        sidebar.classList.add('open');
        sidebar.setAttribute('aria-hidden', 'false');

        if (backdrop) {
            backdrop.classList.add('show');
        }

        // Prevenir scroll
        document.body.style.overflow = 'hidden';

        announceToScreenReader('Menu lateral aberto');
    }

    /**
     * Fecha sidebar
     */
    function closeSidebar() {
        const sidebar = document.querySelector('.sidebar-responsive');
        const backdrop = document.querySelector('.sidebar-backdrop');

        if (!sidebar) return;

        sidebar.classList.remove('open');
        sidebar.setAttribute('aria-hidden', 'true');

        if (backdrop) {
            backdrop.classList.remove('show');
        }

        // Restaurar scroll
        document.body.style.overflow = '';

        announceToScreenReader('Menu lateral fechado');
    }

    /**
     * Inicializa sidebar
     */
    function setupSidebar() {
        const menuToggle = document.querySelector('.menu-toggle');
        const backdrop = document.querySelector('.sidebar-backdrop');

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar-responsive');
                if (sidebar && sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }
    }

    // ========================================
    // 6. TOOLTIPS ACESSÍVEIS
    // ========================================

    /**
     * Inicializa tooltips
     */
    function setupTooltips() {
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            const tooltipText = element.getAttribute('data-tooltip');
            
            // Adicionar aria-label
            element.setAttribute('aria-label', tooltipText);

            // Criar elemento tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.setAttribute('role', 'tooltip');
            tooltip.textContent = tooltipText;
            document.body.appendChild(tooltip);

            // Mostrar ao hover
            element.addEventListener('mouseenter', function(e) {
                const rect = element.getBoundingClientRect();
                tooltip.style.top = `${rect.top - tooltip.offsetHeight - 8}px`;
                tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
                tooltip.classList.add('show');
            });

            // Esconder ao sair
            element.addEventListener('mouseleave', function() {
                tooltip.classList.remove('show');
            });

            // Mostrar ao focar (acessibilidade)
            element.addEventListener('focus', function(e) {
                const rect = element.getBoundingClientRect();
                tooltip.style.top = `${rect.top - tooltip.offsetHeight - 8}px`;
                tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
                tooltip.classList.add('show');
            });

            element.addEventListener('blur', function() {
                tooltip.classList.remove('show');
            });
        });
    }

    // ========================================
    // 7. VALIDAÇÃO DE FORMULÁRIOS
    // ========================================

    /**
     * Validação em tempo real com feedback acessível
     */
    function setupFormValidation() {
        document.querySelectorAll('form[data-validate]').forEach(form => {
            const inputs = form.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                let isValid = true;

                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    announceToScreenReader('Formulário contém erros. Por favor, corrija os campos destacados.', 'assertive');
                    
                    // Focar no primeiro erro
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.focus();
                    }
                }
            });
        });
    }

    /**
     * Valida um campo individual
     */
    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        let isValid = true;
        let errorMessage = '';

        // Campo obrigatório
        if (isRequired && !value) {
            isValid = false;
            errorMessage = 'Este campo é obrigatório';
        }

        // Email
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Email inválido';
            }
        }

        // Telefone
        if (field.type === 'tel' && value) {
            const phoneRegex = /^[\d\s\-\(\)]+$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Telefone inválido';
            }
        }

        // Atualizar UI
        updateFieldValidation(field, isValid, errorMessage);

        return isValid;
    }

    /**
     * Atualiza visual de validação do campo
     */
    function updateFieldValidation(field, isValid, errorMessage) {
        const feedbackId = `${field.id || field.name}-feedback`;
        let feedback = document.getElementById(feedbackId);

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            field.setAttribute('aria-invalid', 'false');

            if (feedback) {
                feedback.remove();
            }
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', feedbackId);

            if (!feedback) {
                feedback = document.createElement('div');
                feedback.id = feedbackId;
                feedback.className = 'invalid-feedback';
                feedback.setAttribute('role', 'alert');
                field.parentNode.appendChild(feedback);
            }

            feedback.textContent = errorMessage;
        }
    }

    // ========================================
    // 8. ALERTAS DISMISSIBLE
    // ========================================

    /**
     * Configura alertas que podem ser fechados
     */
    function setupAlerts() {
        document.querySelectorAll('.alert-close').forEach(button => {
            button.addEventListener('click', function() {
                const alert = this.closest('.alert');
                if (alert) {
                    alert.style.transition = 'opacity 300ms, transform 300ms';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';

                    setTimeout(() => {
                        alert.remove();
                        announceToScreenReader('Alerta fechado');
                    }, 300);
                }
            });
        });

        // Auto-dismiss após tempo
        document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
            const timeout = parseInt(alert.getAttribute('data-auto-dismiss')) || 5000;
            
            setTimeout(() => {
                const closeButton = alert.querySelector('.alert-close');
                if (closeButton) {
                    closeButton.click();
                }
            }, timeout);
        });
    }

    // ========================================
    // 9. SKIP LINKS
    // ========================================

    /**
     * Implementa skip links para navegação rápida
     */
    function setupSkipLinks() {
        // Se não existe, criar
        if (!document.querySelector('.skip-link')) {
            const skipLink = document.createElement('a');
            skipLink.href = '#main-content';
            skipLink.className = 'skip-link';
            skipLink.textContent = 'Pular para o conteúdo principal';
            document.body.insertBefore(skipLink, document.body.firstChild);
        }

        // Adicionar ID ao conteúdo principal se não existir
        const mainContent = document.querySelector('main');
        if (mainContent && !mainContent.id) {
            mainContent.id = 'main-content';
            mainContent.setAttribute('tabindex', '-1');
        }
    }

    // ========================================
    // 10. PREFERÊNCIAS DO USUÁRIO
    // ========================================

    /**
     * Respeita preferência de movimento reduzido
     */
    function respectReducedMotion() {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
            document.documentElement.classList.add('reduced-motion');
        }
    }

    /**
     * Modo escuro automático
     */
    function setupDarkMode() {
        const darkModeToggle = document.getElementById('toggle-dark-mode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Aplicar preferência salva ou do sistema
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            document.documentElement.classList.add('dark-mode');
        }

        // Toggle
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                document.documentElement.classList.toggle('dark-mode');
                const isDark = document.documentElement.classList.contains('dark-mode');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                announceToScreenReader(`Modo ${isDark ? 'escuro' : 'claro'} ativado`);
            });
        }
    }

    // ========================================
    // 11. SCROLL SUAVE
    // ========================================

    /**
     * Implementa scroll suave para âncoras
     */
    function setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Focar no elemento de destino
                    target.setAttribute('tabindex', '-1');
                    target.focus();
                }
            });
        });
    }

    // ========================================
    // 12. TABELAS RESPONSIVAS
    // ========================================

    /**
     * Adiciona labels para tabelas em mobile
     */
    function setupResponsiveTables() {
        document.querySelectorAll('.table-mobile-card').forEach(table => {
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent);
            
            table.querySelectorAll('tbody tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (headers[index]) {
                        cell.setAttribute('data-label', headers[index]);
                    }
                });
            });
        });
    }

    // ========================================
    // INICIALIZAÇÃO
    // ========================================

    /**
     * Inicializa todas as funcionalidades quando o DOM estiver pronto
     */
    function init() {
        console.log('🎯 Inicializando melhorias de acessibilidade e UX...');

        createAriaLiveRegion();
        setupKeyboardNavigation();
        setupModals();
        setupSidebar();
        setupTooltips();
        setupFormValidation();
        setupAlerts();
        setupSkipLinks();
        respectReducedMotion();
        setupDarkMode();
        setupSmoothScroll();
        setupResponsiveTables();

        console.log('✅ Melhorias de acessibilidade inicializadas com sucesso!');
    }

    // Inicializar quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Exportar funções para uso global
    window.AccessibilityHelper = {
        announceToScreenReader,
        openModal,
        closeModal,
        openSidebar,
        closeSidebar,
        validateField
    };

})();
