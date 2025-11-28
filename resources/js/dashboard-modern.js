/**
 * Dashboard Moderno - Sistema de Chamados
 * Funcionalidades avançadas e interativas
 */

class ModernDashboard {
    constructor() {
        this.darkMode = localStorage.getItem('dashboard-dark-mode') === 'true';
        this.filtersVisible = localStorage.getItem('filters-visible') !== 'false';
        this.refreshInterval = null;
        this.charts = {};
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeTheme();
        this.initializeFilters();
        this.initializeCharts();
        this.setupAutoRefresh();
        this.createMiniCharts();
        // this.setupRealTimeUpdates(); - REMOVIDO: gerava notificações falsas
    }

    setupEventListeners() {
        // Toggle Dark Mode
        document.getElementById('toggle-dark-mode')?.addEventListener('click', () => {
            this.toggleDarkMode();
        });

        // Toggle Filters
        document.getElementById('toggle-filters')?.addEventListener('click', () => {
            this.toggleFilters();
        });

        // Fullscreen Dashboard
        document.getElementById('fullscreen-dashboard')?.addEventListener('click', () => {
            this.toggleFullscreen();
        });

        // Refresh Dashboard
        document.getElementById('refresh-dashboard')?.addEventListener('click', () => {
            this.refreshDashboard();
        });

        // Export PDF (com preview)
        document.getElementById('export-pdf')?.addEventListener('click', () => {
            this.openPreview();
        });

        // Filter changes
        this.setupFilterListeners();

        // Chart period selector
        document.getElementById('chart-period')?.addEventListener('change', (e) => {
            this.updateChartsWithPeriod(e.target.value);
        });

        // KPI refresh
        document.getElementById('refresh-kpis')?.addEventListener('click', () => {
            this.refreshKPIs();
        });
    }

    setupFilterListeners() {
        const filters = ['status', 'priority', 'category', 'technician', 'date-start', 'date-end', 'search'];
        
        filters.forEach(filter => {
            const element = document.getElementById(`filter-${filter}`);
            if (element) {
                element.addEventListener('change', () => {
                    this.applyFilters();
                });
            }
        });

        // Search com debounce
        const searchInput = document.getElementById('filter-search');
        if (searchInput) {
            let timeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.applyFilters();
                }, 500);
            });
        }
    }

    initializeTheme() {
        if (this.darkMode) {
            document.body.classList.add('dark-mode');
            const icon = document.querySelector('#toggle-dark-mode i');
            if (icon) {
                icon.className = 'bi bi-sun';
            }
        }
    }

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        document.body.classList.toggle('dark-mode');
        
        const icon = document.querySelector('#toggle-dark-mode i');
        if (icon) {
            icon.className = this.darkMode ? 'bi bi-sun' : 'bi bi-moon';
        }
        
        localStorage.setItem('dashboard-dark-mode', this.darkMode);
        
        // Atualiza charts para o tema
        setTimeout(() => {
            this.updateChartsTheme();
        }, 300);
        
        this.showNotification('Tema alterado com sucesso!', 'success');
    }

    initializeFilters() {
        const filterContent = document.getElementById('filter-content');
        const toggleBtn = document.getElementById('toggle-filters');
        
        if (filterContent && toggleBtn) {
            if (!this.filtersVisible) {
                filterContent.style.display = 'none';
                toggleBtn.querySelector('i').style.transform = 'rotate(-90deg)';
            }
        }
    }

    toggleFilters() {
        const filterContent = document.getElementById('filter-content');
        const toggleBtn = document.getElementById('toggle-filters');
        
        if (filterContent && toggleBtn) {
            this.filtersVisible = !this.filtersVisible;
            
            if (this.filtersVisible) {
                filterContent.style.display = 'block';
                toggleBtn.querySelector('i').style.transform = 'rotate(0deg)';
            } else {
                filterContent.style.display = 'none';
                toggleBtn.querySelector('i').style.transform = 'rotate(-90deg)';
            }
            
            localStorage.setItem('filters-visible', this.filtersVisible);
        }
    }

    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            this.showNotification('Modo tela cheia ativado', 'info');
        } else {
            document.exitFullscreen();
            this.showNotification('Modo tela cheia desativado', 'info');
        }
    }

    refreshDashboard() {
        this.showNotification('Atualizando dashboard...', 'info');
        
        // Simula refresh dos dados
        setTimeout(() => {
            this.refreshKPIs();
            this.refreshCharts();
            this.refreshWidgets();
            this.showNotification('Dashboard atualizado!', 'success');
        }, 1000);
    }

    refreshKPIs() {
        const kpiCards = document.querySelectorAll('.kpi-card-modern, .secondary-kpi-card');
        
        kpiCards.forEach(card => {
            card.style.opacity = '0.5';
            card.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
                
                // Simula atualização do valor
                const valueElement = card.querySelector('.kpi-value, .secondary-kpi-value');
                if (valueElement) {
                    this.animateNumber(valueElement);
                }
            }, Math.random() * 500 + 200);
        });
    }

    animateNumber(element) {
        const current = parseInt(element.textContent) || 0;
        const variation = Math.floor(Math.random() * 10) - 5; // -5 a +5
        const newValue = Math.max(0, current + variation);
        
        let start = current;
        const increment = (newValue - current) / 20;
        
        const animate = () => {
            start += increment;
            if ((increment > 0 && start >= newValue) || (increment < 0 && start <= newValue)) {
                element.textContent = newValue;
                return;
            }
            element.textContent = Math.floor(start);
            requestAnimationFrame(animate);
        };
        
        animate();
    }

    initializeCharts() {
        this.initCategoryChart();
        this.initPriorityChart();
        this.initEvolutionChart();
        this.initNPSChart();
    }

    initCategoryChart() {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;

        this.charts.category = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hardware', 'Software', 'Rede', 'Suporte', 'Outros'],
                datasets: [{
                    data: [45, 25, 15, 10, 5],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    initPriorityChart() {
        const ctx = document.getElementById('priorityChart');
        if (!ctx) return;

        this.charts.priority = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Urgente', 'Alta', 'Média', 'Baixa'],
                datasets: [{
                    label: 'Chamados',
                    data: [8, 23, 45, 24],
                    backgroundColor: [
                        '#ef4444',
                        '#f59e0b',
                        '#3b82f6',
                        '#10b981'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    initEvolutionChart() {
        const ctx = document.getElementById('evolutionChart');
        if (!ctx) return;

        const days = [];
        const openData = [];
        const resolvedData = [];

        // Gera dados dos últimos 30 dias
        for (let i = 29; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            days.push(date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' }));
            openData.push(Math.floor(Math.random() * 20) + 5);
            resolvedData.push(Math.floor(Math.random() * 25) + 3);
        }

        this.charts.evolution = new Chart(ctx, {
            type: 'line',
            data: {
                labels: days,
                datasets: [
                    {
                        label: 'Abertos',
                        data: openData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Resolvidos',
                        data: resolvedData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    initNPSChart() {
        const ctx = document.getElementById('npsChart');
        if (!ctx) return;

        this.charts.nps = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [92, 8],
                    backgroundColor: ['#10b981', '#e5e7eb'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });
    }

    createMiniCharts() {
        // Mini charts para os KPIs
        this.createSparkline('totalChart', [12, 19, 15, 25, 22, 30, 28]);
        this.createSparkline('openChart', [8, 12, 10, 15, 18, 22, 20]);
        this.createSparkline('progressChart', [15, 12, 8, 6, 4, 3, 5]);
        this.createSparkline('resolvedChart', [5, 8, 12, 18, 22, 28, 35]);
    }

    createSparkline(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: new Array(data.length).fill(''),
                datasets: [{
                    data: data,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                },
                elements: {
                    point: { radius: 0 }
                }
            }
        });
    }

    refreshCharts() {
        Object.values(this.charts).forEach(chart => {
            if (chart && chart.update) {
                chart.update('active');
            }
        });
    }

    updateChartsWithPeriod(period) {
        this.showNotification(`Atualizando gráficos para ${period} dias...`, 'info');
        
        // Simula atualização com novo período
        setTimeout(() => {
            this.refreshCharts();
            this.showNotification('Gráficos atualizados!', 'success');
        }, 800);
    }

    updateChartsTheme() {
        const textColor = this.darkMode ? '#cbd5e1' : '#1f2937';
        const gridColor = this.darkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';

        Object.values(this.charts).forEach(chart => {
            if (chart && chart.options) {
                // Atualiza cores do texto
                if (chart.options.scales) {
                    if (chart.options.scales.x) {
                        chart.options.scales.x.ticks = { color: textColor };
                        chart.options.scales.x.grid = { color: gridColor };
                    }
                    if (chart.options.scales.y) {
                        chart.options.scales.y.ticks = { color: textColor };
                        chart.options.scales.y.grid = { color: gridColor };
                    }
                }
                
                if (chart.options.plugins && chart.options.plugins.legend) {
                    chart.options.plugins.legend.labels.color = textColor;
                }
                
                chart.update();
            }
        });
    }

    setupAutoRefresh() {
        // Auto-refresh a cada 5 minutos
        this.refreshInterval = setInterval(() => {
            this.refreshKPIs();
        }, 300000);
    }

    // setupRealTimeUpdates() - REMOVIDO: estava gerando notificações falsas
    // simulateRealTimeUpdate() - REMOVIDO: era apenas simulação para testes

    applyFilters() {
        const filters = {
            status: document.getElementById('filter-status')?.value || '',
            priority: document.getElementById('filter-priority')?.value || '',
            category: document.getElementById('filter-category')?.value || '',
            dateStart: document.getElementById('filter-date-start')?.value || '',
            dateEnd: document.getElementById('filter-date-end')?.value || '',
            search: document.getElementById('filter-search')?.value || ''
        };

        // Remove filtros vazios
        Object.keys(filters).forEach(key => {
            if (!filters[key]) delete filters[key];
        });

        if (Object.keys(filters).length > 0) {
            this.showNotification('Filtros aplicados!', 'success');
            // Aqui você faria a requisição AJAX para aplicar os filtros
            console.log('Aplicando filtros:', filters);
        }
    }

    buildFilterParams() {
        const params = new URLSearchParams();
        const status = document.getElementById('filter-status')?.value || '';
        const priority = document.getElementById('filter-priority')?.value || '';
        const category = document.getElementById('filter-category')?.value || '';
        const technician = document.getElementById('filter-technician')?.value || '';
        const dateStart = document.getElementById('filter-date-start')?.value || '';
        const dateEnd = document.getElementById('filter-date-end')?.value || '';
        const search = document.getElementById('filter-search')?.value || '';

        if (status) params.set('status', status);
        if (priority) params.set('priority', priority);
        if (category) params.set('category_id', category);
        if (technician) params.set('assigned_to', technician);
        if (dateStart) params.set('date_from', dateStart);
        if (dateEnd) params.set('date_to', dateEnd);
        if (search) params.set('search', search);
        return params;
    }

    openPreview() {
        const modal = document.getElementById('export-preview-modal');
        const content = document.getElementById('export-preview-content');
        
        // Debug: verifica se o modal existe
        if (!modal || !content) {
            console.error('Modal não encontrado no DOM. IDs:', {modal, content});
            this.showNotification('Erro: Modal de pré-visualização não encontrado!', 'error');
            return;
        }
        
        const params = this.buildFilterParams();
        
        const previewUrl = (typeof window.DASHBOARD_EXPORT_PREVIEW_URL !== 'undefined' && window.DASHBOARD_EXPORT_PREVIEW_URL)
            ? window.DASHBOARD_EXPORT_PREVIEW_URL
            : `${window.location.origin}/dashboard/export/preview`;
        
        const downloadUrl = (typeof window.DASHBOARD_EXPORT_URL !== 'undefined' && window.DASHBOARD_EXPORT_URL)
            ? window.DASHBOARD_EXPORT_URL
            : `${window.location.origin}/dashboard/export`;

        console.log('Abrindo modal preview. URL:', previewUrl + '?' + params.toString());

        // Mostra o modal
        modal.style.display = 'block';
        content.innerHTML = '<div style="text-align:center; padding:60px 20px; color:#64748b;"><i class="bi bi-hourglass-split" style="font-size:48px; margin-bottom:16px;"></i><p>Carregando pré-visualização...</p></div>';

        // Carrega conteúdo via fetch
        fetch(`${previewUrl}?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                // Extrai apenas o conteúdo do .report-container da resposta
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const reportContainer = doc.querySelector('.report-container');
                if (reportContainer) {
                    content.innerHTML = reportContainer.outerHTML;
                } else {
                    content.innerHTML = html;
                }
                
                // Configura botão de download PDF
                const downloadBtn = document.getElementById('export-modal-download-pdf');
                if (downloadBtn) {
                    downloadBtn.onclick = () => {
                        window.open(`${downloadUrl}?${params.toString()}`, '_blank');
                        this.showNotification('Download do PDF iniciado!', 'success');
                    };
                }
            })
            .catch(err => {
                content.innerHTML = '<div style="text-align:center; padding:60px 20px; color:#ef4444;"><i class="bi bi-exclamation-triangle" style="font-size:48px; margin-bottom:16px;"></i><p>Erro ao carregar pré-visualização.</p></div>';
                console.error('Erro ao carregar preview:', err);
            });

        // Fecha modal
        document.getElementById('close-export-modal').onclick = () => {
            modal.style.display = 'none';
        };
        
        // Fecha ao clicar fora
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        };

        this.showNotification('Abrindo pré-visualização...', 'info');
    }

    refreshWidgets() {
        // Refresh dos widgets
        const widgets = document.querySelectorAll('.widget-card-modern');
        widgets.forEach(widget => {
            widget.style.opacity = '0.7';
            setTimeout(() => {
                widget.style.opacity = '1';
            }, Math.random() * 300 + 100);
        });
    }

    showNotification(message, type = 'info') {
        // Remove notificações existentes
        const existing = document.querySelector('.dashboard-notification');
        if (existing) {
            existing.remove();
        }

        const notification = document.createElement('div');
        notification.className = `dashboard-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="bi bi-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;

        // Estilos inline
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: this.getNotificationColor(type),
            color: 'white',
            padding: '16px 24px',
            borderRadius: '12px',
            boxShadow: '0 8px 32px rgba(0,0,0,0.2)',
            zIndex: '10000',
            transform: 'translateX(400px)',
            transition: 'transform 0.3s ease',
            fontWeight: '600',
            fontSize: '14px'
        });

        document.body.appendChild(notification);

        // Animação de entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Remove após 4 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 4000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    getNotificationColor(type) {
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        return colors[type] || '#3b82f6';
    }

    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
        
        Object.values(this.charts).forEach(chart => {
            if (chart && chart.destroy) {
                chart.destroy();
            }
        });
    }
}

// Inicializa o dashboard quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.modernDashboard = new ModernDashboard();
});

// Adiciona estilos CSS dinâmicos para animações
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .dashboard-notification .notification-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .dashboard-notification i {
        font-size: 16px;
    }
`;
document.head.appendChild(style);
