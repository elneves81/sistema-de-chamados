<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiController extends Controller
{
    protected $aiService;
    
    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }
    
    /**
     * Chatbot endpoint para conversas
     */
    public function chatbot(Request $request)
    {
        $message = $request->input('message');
        
        if (empty($message)) {
            return response()->json([
                'error' => 'Mensagem não pode estar vazia'
            ], 400);
        }
        
        $response = $this->aiService->generateChatbotResponse($message);
        
        return response()->json([
            'response' => $response['response'],
            'suggestions' => $response['suggestions'],
            'action' => $response['action'],
            'needs_ticket_info' => $response['needs_ticket_info'] ?? false,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Criar chamado via IA com validação
     */
    public function createTicketViaAi(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'local' => 'required|string|max:255',
                'user_name' => 'required|string|max:255',
                'original_message' => 'string|nullable'
            ]);

            // Classificação automática via IA
            $classification = $this->aiService->classifyTicket($request->title, $request->description);
            $urgency = $this->aiService->detectUrgency($request->title, $request->description);

            // Buscar categoria padrão se a IA não conseguir classificar
            $categoryId = $classification['suggested_category_id'] ?? Category::first()?->id;
            
            if (!$categoryId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro: Nenhuma categoria disponível no sistema.'
                ], 400);
            }

            // Criar o chamado
            $ticket = Ticket::create([
                'title' => $request->title,
                'description' => $request->description,
                'local' => $request->local,
                'status' => 'open',
                'priority' => $urgency['priority'] ?? 'medium',
                'user_id' => Auth::id(),
                'category_id' => $categoryId,
                'impact' => 2,
                'urgency' => 2
            ]);

            // Log da criação automática
            $ticket->comments()->create([
                'user_id' => Auth::id(),
                'comment' => "📋 Chamado criado automaticamente pela IA\n\n" .
                           "👤 Solicitante: {$request->user_name}\n" .
                           "🏢 Local: {$request->local}\n" .
                           "🤖 Mensagem original: " . ($request->original_message ?? 'N/A') . "\n\n" .
                           "🎯 Classificação IA: Categoria {$categoryId}, Prioridade {$urgency['priority']}\n" .
                           "🔍 Confiança: " . round($classification['confidence'] ?? 0, 1) . "%"
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Chamado criado com sucesso!',
                'ticket' => [
                    'id' => $ticket->id,
                    'title' => $ticket->title,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'created_at' => $ticket->created_at->format('d/m/Y H:i')
                ],
                'ai_analysis' => [
                    'classification' => $classification,
                    'urgency' => $urgency
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Dados incompletos',
                'validation_errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Classificação inteligente de chamados
     */
    public function classifyTicket(Request $request)
    {
        $title = $request->input('title', '');
        $description = $request->input('description', '');
        
        if (empty($title) && empty($description)) {
            return response()->json([
                'error' => 'Título ou descrição devem ser fornecidos'
            ], 400);
        }
        
        $classification = $this->aiService->classifyTicket($title, $description);
        $urgency = $this->aiService->detectUrgency($title, $description);
        $suggestions = $this->aiService->suggestSolutions($title, $description);
        
        return response()->json([
            'classification' => $classification,
            'urgency' => $urgency,
            'suggestions' => $suggestions,
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Análise preditiva de demanda
     */
    public function predictDemand(Request $request)
    {
        $days = $request->input('days', 7);
        $days = min(max($days, 1), 30); // Limita entre 1 e 30 dias
        
        $predictions = $this->aiService->predictDemand($days);
        
        return response()->json([
            'predictions' => $predictions,
            'summary' => [
                'total_predicted' => array_sum(array_column($predictions, 'predicted_tickets')),
                'avg_per_day' => round(array_sum(array_column($predictions, 'predicted_tickets')) / count($predictions), 1),
                'peak_day' => collect($predictions)->sortByDesc('predicted_tickets')->first(),
                'lowest_day' => collect($predictions)->sortBy('predicted_tickets')->first()
            ],
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Sugestões automáticas durante criação de chamado
     */
    public function autoSuggest(Request $request)
    {
        $query = $request->input('query', '');
        
        if (strlen($query) < 3) {
            return response()->json([
                'suggestions' => [],
                'message' => 'Digite pelo menos 3 caracteres para buscar sugestões'
            ]);
        }
        
        $suggestions = $this->aiService->suggestSolutions($query, '');
        
        return response()->json([
            'suggestions' => $suggestions,
            'count' => count($suggestions),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Dashboard de IA com métricas e insights
     */
    public function dashboard()
    {
        // Se for uma requisição AJAX, retorna JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return $this->getDashboardData();
        }
        
        // Senão, retorna a view
        return view('ai.dashboard');
    }
    
    /**
     * Dados do dashboard em formato JSON
     */
    public function getDashboardData()
    {
        // Estatísticas gerais
        $totalTickets = Ticket::count();
        $ticketsToday = Ticket::whereDate('created_at', today())->count();
        $ticketsThisWeek = Ticket::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        
        // Análise de categorias mais comuns
        $topCategories = Ticket::join('categories', 'tickets.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, COUNT(*) as count')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
            
        // Previsões para próximos 7 dias
        $predictions = $this->aiService->predictDemand(7);
        
        // Tickets por prioridade
        $priorityStats = Ticket::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get()
            ->keyBy('priority');
            
        return response()->json([
            'stats' => [
                'total_tickets' => $totalTickets,
                'tickets_today' => $ticketsToday,
                'tickets_this_week' => $ticketsThisWeek,
                'growth_rate' => $this->calculateGrowthRate()
            ],
            'top_categories' => $topCategories,
            'predictions' => $predictions,
            'priority_distribution' => [
                'high' => $priorityStats->get('high')->count ?? 0,
                'medium' => $priorityStats->get('medium')->count ?? 0,
                'low' => $priorityStats->get('low')->count ?? 0
            ],
            'insights' => $this->generateInsights(),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Calcula taxa de crescimento semanal
     */
    private function calculateGrowthRate()
    {
        $thisWeek = Ticket::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $lastWeek = Ticket::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        
        if ($lastWeek == 0) return 0;
        
        return round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1);
    }
    
    /**
     * Gera insights automáticos
     */
    private function generateInsights()
    {
        $insights = [];
        
        // Insight sobre pico de demanda
        $predictions = $this->aiService->predictDemand(7);
        $peakDay = collect($predictions)->sortByDesc('predicted_tickets')->first();
        
        if ($peakDay && $peakDay['predicted_tickets'] > 0) {
            $insights[] = [
                'type' => 'prediction',
                'title' => 'Pico de Demanda Previsto',
                'message' => "Esperamos {$peakDay['predicted_tickets']} chamados em " . ($peakDay['day_name'] ?? ($peakDay['day_of_week'] ?? 'dia')), 
                'priority' => 'medium',
                'action' => 'Considere alocar mais recursos técnicos para este dia'
            ];
        }
        
        // Insight sobre crescimento
        $growthRate = $this->calculateGrowthRate();
        if ($growthRate > 20) {
            $insights[] = [
                'type' => 'growth',
                'title' => 'Crescimento Acelerado',
                'message' => "Aumento de {$growthRate}% nos chamados esta semana",
                'priority' => 'high',
                'action' => 'Revisar capacidade da equipe e processos'
            ];
        } elseif ($growthRate < -20) {
            $insights[] = [
                'type' => 'decline',
                'title' => 'Redução Significativa',
                'message' => "Diminuição de " . abs($growthRate) . "% nos chamados esta semana",
                'priority' => 'low',
                'action' => 'Boa oportunidade para treinamentos e melhorias'
            ];
        }
        
        // Insight sobre tickets urgentes
        $urgentTickets = Ticket::where('priority', 'high')->where('status', '!=', 'closed')->count();
        if ($urgentTickets > 5) {
            $insights[] = [
                'type' => 'urgent',
                'title' => 'Muitos Tickets Urgentes',
                'message' => "{$urgentTickets} tickets de alta prioridade em aberto",
                'priority' => 'high',
                'action' => 'Foque na resolução dos tickets críticos'
            ];
        }
        
        return $insights;
    }
}
