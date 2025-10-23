<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiService
{
    /**
     * Classifica automaticamente um chamado usando IA
     */
    public function classifyTicket($title, $description)
    {
        try {
            // Análise baseada em palavras-chave
            $keywords = $this->extractKeywords($title . ' ' . $description);
            
            // Mapeamento de palavras-chave para categorias
            $categoryMappings = [
                'hardware' => ['computador', 'mouse', 'teclado', 'monitor', 'impressora', 'cabo', 'hardware', 'equipamento'],
                'software' => ['programa', 'aplicativo', 'sistema', 'software', 'instalação', 'licença', 'update', 'erro'],
                'rede' => ['internet', 'wifi', 'rede', 'conexão', 'servidor', 'vpn', 'email', 'navegador'],
                'suporte' => ['dúvida', 'ajuda', 'tutorial', 'como', 'procedimento', 'orientação', 'suporte'],
                'seguranca' => ['senha', 'vírus', 'malware', 'segurança', 'acesso', 'bloqueio', 'hack', 'phishing']
            ];
            
            $scores = [];
            foreach ($categoryMappings as $category => $words) {
                $score = 0;
                foreach ($words as $word) {
                    if (stripos($title . ' ' . $description, $word) !== false) {
                        $score++;
                    }
                }
                $scores[$category] = $score;
            }
            
            // Retorna a categoria com maior score
            $suggestedCategory = array_search(max($scores), $scores);
            
            // Busca categoria real no banco
            $category = Category::where('name', 'like', '%' . $suggestedCategory . '%')->first();
            
            return [
                'suggested_category_id' => $category ? $category->id : null,
                'confidence' => max($scores) > 0 ? (max($scores) / count($keywords)) * 100 : 0,
                'keywords' => $keywords,
                'analysis' => $scores
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro na classificação IA: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Detecta urgência/prioridade usando IA
     */
    public function detectUrgency($title, $description)
    {
        $urgentKeywords = [
            'urgente', 'emergência', 'crítico', 'parado', 'travado', 'não funciona',
            'erro grave', 'sistema fora', 'produção parada', 'cliente reclamando',
            'prazo', 'deadline', 'hoje', 'agora', 'imediato'
        ];
        
        $mediumKeywords = [
            'problema', 'erro', 'falha', 'lento', 'dificuldade', 'não consegue',
            'intermitente', 'às vezes', 'algumas vezes'
        ];
        
        $text = strtolower($title . ' ' . $description);
        
        $urgentScore = 0;
        $mediumScore = 0;
        
        foreach ($urgentKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $urgentScore++;
            }
        }
        
        foreach ($mediumKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $mediumScore++;
            }
        }
        
        if ($urgentScore > 0) {
            return [
                'priority' => 'high',
                'confidence' => min(($urgentScore / count($urgentKeywords)) * 100, 95),
                'reason' => 'Detectadas palavras indicativas de urgência'
            ];
        } elseif ($mediumScore > 1) {
            return [
                'priority' => 'medium',
                'confidence' => min(($mediumScore / count($mediumKeywords)) * 100, 80),
                'reason' => 'Detectado problema de média complexidade'
            ];
        } else {
            return [
                'priority' => 'low',
                'confidence' => 60,
                'reason' => 'Não detectados indicadores de urgência'
            ];
        }
    }
    
    /**
     * Sugere soluções baseadas na base de conhecimento
     */
    public function suggestSolutions($title, $description)
    {
        $keywords = $this->extractKeywords($title . ' ' . $description);
        
        $suggestions = collect();
        
        foreach ($keywords as $keyword) {
            $articles = KnowledgeBase::where('status', 'published')
                ->where(function($query) use ($keyword) {
                    $query->where('title', 'like', '%' . $keyword . '%')
                          ->orWhere('content', 'like', '%' . $keyword . '%')
                          ->orWhere('excerpt', 'like', '%' . $keyword . '%');
                })
                ->limit(3)
                ->get();
                
            foreach ($articles as $article) {
                if (!$suggestions->contains('id', $article->id)) {
                    $suggestions->push([
                        'id' => $article->id,
                        'title' => $article->title,
                        'excerpt' => $article->excerpt,
                        'relevance_score' => $this->calculateRelevance($keyword, $article)
                    ]);
                }
            }
        }
        
        return $suggestions->sortByDesc('relevance_score')->take(5)->values();
    }
    
    /**
     * Gera resposta automática para o chatbot
     */
    public function generateChatbotResponse($message)
    {
        $message = strtolower(trim($message));
        
        // Classificar o tipo de problema
        if (preg_match('/(oi|olá|ola|bom dia|boa tarde|boa noite|hi|hello)/', $message)) {
            return [
                'response' => 'Olá! Sou a IA do sistema de chamados. Como posso ajudá-lo hoje?',
                'suggestions' => [
                    ['text' => 'Criar chamado', 'action' => 'redirect_ticket_form'],
                    ['text' => 'Problema no computador', 'action' => 'hardware'],
                    ['text' => 'Sistema não funciona', 'action' => 'software'],
                    ['text' => 'Internet lenta', 'action' => 'network']
                ],
                'action' => 'greeting'
            ];
        }
        
        if (preg_match('/(computador|pc|mouse|teclado|monitor|impressora).*(não funciona|quebrou|problema|defeito|parou|travou)/', $message)) {
            return [
                'response' => 'Identifiquei um problema de hardware. Para resolvermos rapidamente, você precisa abrir um chamado técnico através do nosso formulário oficial.',
                'suggestions' => [
                    ['text' => 'Abrir chamado técnico', 'action' => 'redirect_ticket_form']
                ],
                'action' => 'hardware_issue'
            ];
        }
        
        if (preg_match('/(programa|sistema|aplicativo|software).*(erro|travou|não abre|falha|bug|lento)/', $message)) {
            return [
                'response' => 'Problema de software detectado. Nosso time técnico precisa analisar isso através de um chamado formal onde você pode detalhar exatamente o que está acontecendo.',
                'suggestions' => [
                    ['text' => 'Criar chamado de software', 'action' => 'redirect_ticket_form']
                ],
                'action' => 'software_issue'
            ];
        }
        
        if (preg_match('/(internet|wifi|rede|conexao|email).*(lent|não funciona|caiu|instável|oscilando|sem acesso)/', $message)) {
            return [
                'response' => 'Problemas de rede são prioritários! Você precisa abrir um chamado urgente informando sua localização exata para que nossa equipe possa resolver rapidamente.',
                'suggestions' => [
                    ['text' => 'Reportar problema de rede', 'action' => 'redirect_ticket_form']
                ],
                'action' => 'network_issue'
            ];
        }
        
        if (preg_match('/(abrir|criar|novo|preciso).*(chamado|ticket|solicitação|atendimento)/', $message)) {
            return [
                'response' => 'Para criar um chamado, você precisa usar nosso formulário oficial onde poderá fornecer todas as informações necessárias (nome, local, título e descrição).',
                'suggestions' => [
                    ['text' => 'Ir para formulário', 'action' => 'redirect_ticket_form']
                ],
                'action' => 'need_ticket_creation'
            ];
        }
        
        if (preg_match('/(como|duvida|ajuda|pergunta|informação)/', $message)) {
            return [
                'response' => 'Para dúvidas gerais, sugestões ou questões que não são problemas técnicos, recomendo usar nosso canal "Fale Conosco".',
                'suggestions' => [
                    ['text' => 'Fale Conosco', 'action' => 'redirect_contact']
                ],
                'action' => 'general_question'
            ];
        }
        
        // Resposta padrão
        return [
            'response' => 'Para melhor atendimento, escolha uma das opções abaixo ou use nossos canais oficiais:',
            'suggestions' => [
                ['text' => 'Formulário de Chamados', 'action' => 'redirect_ticket_form'],
                ['text' => 'Fale Conosco', 'action' => 'redirect_contact'],
                ['text' => 'Problema técnico', 'action' => 'hardware'],
                ['text' => 'Erro de sistema', 'action' => 'software']
            ],
            'action' => 'general_help'
        ];
    }
    
    /**
     * Análise preditiva de demanda
     */
    public function predictDemand($days = 7)
    {
        // Coleta dados históricos
        $historicalData = Ticket::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Análise simples de tendência (média móvel)
        $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $predictions = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = now()->addDays($i);
            $dayOfWeek = $date->format('l');
            
            // Calcula média para este dia da semana
            $avg = $historicalData->filter(function($item) use ($dayOfWeek) {
                return \Carbon\Carbon::parse($item->date)->format('l') === $dayOfWeek;
            })->avg('count') ?? 0;
            
            $predictions[] = [
                'date' => $date->format('Y-m-d'),
                'day_of_week' => $dayOfWeek,
                'predicted_tickets' => round($avg),
                'confidence' => min(70 + ($historicalData->count() * 2), 95)
            ];
        }
        
        return $predictions;
    }
    
    /**
     * Extrai palavras-chave relevantes
     */
    private function extractKeywords($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\w\sáéíóúâêîôûàèìòùäëïöüçñ]/', '', $text);
        
        $stopWords = [
            'o', 'a', 'os', 'as', 'um', 'uma', 'uns', 'umas', 'de', 'do', 'da', 'dos', 'das',
            'em', 'no', 'na', 'nos', 'nas', 'por', 'para', 'com', 'sem', 'e', 'ou', 'mas',
            'que', 'é', 'está', 'são', 'estão', 'foi', 'foram', 'será', 'serão', 'ter', 'tem',
            'teve', 'tendo', 'fazer', 'faz', 'fez', 'fazendo', 'ir', 'vai', 'foi', 'indo',
            'vir', 'vem', 'veio', 'vindo', 'dar', 'dá', 'deu', 'dando', 'ver', 'vê', 'viu', 'vendo'
        ];
        
        $words = explode(' ', $text);
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        return array_unique($keywords);
    }
    
    /**
     * Calcula relevância entre palavra-chave e artigo
     */
    private function calculateRelevance($keyword, $article)
    {
        $titleMatches = substr_count(strtolower($article->title), strtolower($keyword));
        $contentMatches = substr_count(strtolower($article->content), strtolower($keyword));
        $excerptMatches = substr_count(strtolower($article->excerpt), strtolower($keyword));
        
        return ($titleMatches * 3) + ($excerptMatches * 2) + $contentMatches;
    }
    
    /**
     * Gera sugestões inteligentes baseadas na mensagem
     */
    private function generateSmartSuggestions($message)
    {
        if (preg_match('/(hardware|computador|mouse|teclado|monitor|impressora)/', $message)) {
            return [
                '🎫 Abrir chamado de hardware',
                '� Soluções de equipamento', 
                '� Falar com técnico',
                '� Reiniciar equipamento'
            ];
        }
        
        if (preg_match('/(software|programa|sistema|aplicativo)/', $message)) {
            return [
                '🎫 Abrir chamado de software',
                '🔄 Reiniciar programa',
                '📚 Ver tutoriais',
                '⬆️ Verificar atualizações'
            ];
        }
        
        if (preg_match('/(internet|rede|wifi|conexão)/', $message)) {
            return [
                '� Abrir chamado de rede',
                '🌐 Testar conectividade',
                '📡 Verificar infraestrutura',
                '� Diagnóstico de rede'
            ];
        }

        if (preg_match('/(abrir|criar|novo).*(chamado|ticket)/', $message)) {
            return [
                '🎫 Formulário de chamado',
                '🚨 Chamado urgente',
                '📋 Chamado normal',
                '❓ Dúvidas sobre processo'
            ];
        }

        if (preg_match('/(duvida|como|ajuda|tutorial)/', $message)) {
            return [
                '📚 Base de conhecimento',
                '🎥 Tutoriais em vídeo',
                '� Procedimentos',
                '🎫 Solicitar orientação'
            ];
        }
        
        return [
            '🎫 Abrir chamado',
            '📚 Buscar soluções', 
            '❓ Fazer pergunta',
            '� Falar com atendente'
        ];
    }
    
    /**
     * Determina ação mais inteligente
     */
    private function determineAction($message)
    {
        // Problemas urgentes - sugere chamado direto
        if (preg_match('/(urgente|emergencia|critico|parado|não funciona|travou|caiu)/', $message)) {
            return 'suggest_urgent_ticket';
        }
        
        // Pedidos diretos de chamado - mostra formulário
        if (preg_match('/(abrir|criar|novo).*(chamado|ticket|solicitação)/', $message)) {
            return 'show_ticket_form';
        }
        
        // Problemas específicos que precisam de chamado
        if (preg_match('/(hardware|computador|mouse|teclado|monitor|impressora).*(problema|defeito|quebrou)/', $message)) {
            return 'suggest_hardware_ticket';
        }

        if (preg_match('/(software|programa|sistema|aplicativo).*(erro|falha|bug|travou)/', $message)) {
            return 'suggest_software_ticket';
        }

        if (preg_match('/(internet|rede|wifi|conexão).*(lent|caiu|instável|problema)/', $message)) {
            return 'suggest_network_ticket';
        }
        
        // Dúvidas e ajuda - busca conhecimento
        if (preg_match('/(duvida|como|tutorial|ajuda|procedimento)/', $message)) {
            return 'search_knowledge';
        }
        
        return 'general_help';
    }
}
