<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\KnowledgeArticle;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * Classifica automaticamente um ticket usando IA
     */
    public function classifyTicket($title, $description)
    {
        try {
            $categories = Category::all();
            $text = strtolower($title . ' ' . $description);
            
            $scores = [];
            
            foreach ($categories as $category) {
                $keywords = explode(',', strtolower($category->keywords ?? ''));
                $score = 0;
                
                foreach ($keywords as $keyword) {
                    $keyword = trim($keyword);
                    if (!empty($keyword) && stripos($text, $keyword) !== false) {
                        $score++;
                    }
                }
                
                $scores[$category->id] = $score;
            }
            
            $maxScore = max($scores);
            $category = null;
            
            if ($maxScore > 0) {
                $categoryId = array_search($maxScore, $scores);
                $category = $categories->find($categoryId);
            }
            
            $keywords = array_keys(array_filter($scores));
            
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
        } elseif ($mediumScore > 0) {
            return [
                'priority' => 'medium',
                'confidence' => min(($mediumScore / count($mediumKeywords)) * 100, 80),
                'reason' => 'Detectadas palavras indicativas de prioridade média'
            ];
        }
        
        return [
            'priority' => 'low',
            'confidence' => 60,
            'reason' => 'Nenhuma palavra de urgência detectada'
        ];
    }

    /**
     * Gera resposta automática para o chatbot - VERSÃO MELHORADA
     */
    public function generateChatbotResponse($message)
    {
        $message = strtolower(trim($message));
        
        // Saudações
        if (preg_match('/(oi|olá|ola|bom dia|boa tarde|boa noite|hi|hello)/', $message)) {
            return [
                'response' => 'Olá! 👋 Sou a IA do sistema DITIS. Como posso ajudá-lo hoje?',
                'suggestions' => [
                    ['text' => 'Criar chamado', 'action' => 'create_ticket'],
                    ['text' => 'Problema no computador', 'action' => 'help_guide', 'guide_type' => 'hardware'],
                    ['text' => 'Sistema não funciona', 'action' => 'help_guide', 'guide_type' => 'software'],
                    ['text' => 'Internet lenta', 'action' => 'help_guide', 'guide_type' => 'network']
                ],
                'action' => 'greeting'
            ];
        }
        
        // Problemas de hardware
        if (preg_match('/(computador|pc|mouse|teclado|monitor|impressora|hardware).*(não funciona|quebrou|problema|defeito|parou|travou|não liga)/', $message)) {
            return [
                'response' => '🔧 Identifiquei um problema de hardware! Vou te ajudar a resolver:\n\n1. Primeiro, posso mostrar algumas verificações básicas\n2. Se não resolver, podemos criar um chamado técnico\n\nO que você prefere?',
                'suggestions' => [
                    ['text' => 'Ver guia de verificação', 'action' => 'help_guide', 'guide_type' => 'hardware'],
                    ['text' => 'Criar chamado agora', 'action' => 'create_ticket']
                ],
                'action' => 'hardware_issue'
            ];
        }
        
        // Problemas de software
        if (preg_match('/(programa|sistema|aplicativo|software).*(erro|travou|não abre|falha|bug|lento|tela azul)/', $message)) {
            return [
                'response' => '💻 Problema de software detectado! Posso te orientar:\n\n1. Mostrar soluções rápidas para tentar\n2. Ou criar um chamado direto para nossa equipe\n\nQual opção prefere?',
                'suggestions' => [
                    ['text' => 'Tentar soluções rápidas', 'action' => 'help_guide', 'guide_type' => 'software'],
                    ['text' => 'Criar chamado técnico', 'action' => 'create_ticket']
                ],
                'action' => 'software_issue'
            ];
        }
        
        // Problemas de rede/internet
        if (preg_match('/(internet|rede|wifi|wi-fi|conexão).*(lenta|não funciona|caiu|problema|sem acesso)/', $message)) {
            return [
                'response' => '🌐 Problema de rede identificado! Vamos resolver:\n\n1. Posso mostrar verificações básicas de conectividade\n2. Ou abrir chamado para nossa equipe de infraestrutura\n\nComo prefere proceder?',
                'suggestions' => [
                    ['text' => 'Verificações básicas', 'action' => 'help_guide', 'guide_type' => 'network'],
                    ['text' => 'Chamar suporte de rede', 'action' => 'create_ticket']
                ],
                'action' => 'network_issue'
            ];
        }
        
        // Criar chamado diretamente
        if (preg_match('/(criar|abrir|fazer).*(chamado|ticket|solicitação)/', $message)) {
            return [
                'response' => '🎫 Perfeito! Vou abrir o formulário inteligente para criar seu chamado.\n\nO sistema vai analisar automaticamente sua solicitação e definir a prioridade adequada.',
                'suggestions' => [
                    ['text' => 'Abrir formulário', 'action' => 'create_ticket']
                ],
                'action' => 'create_ticket_request'
            ];
        }
        
        // Ajuda geral
        if (preg_match('/(help|ajuda|socorro|não sei|como|o que)/', $message)) {
            return [
                'response' => '🤝 Estou aqui para ajudar! Posso te auxiliar com:\n\n• Problemas técnicos (hardware/software)\n• Criar chamados\n• Orientações básicas\n• Contato com suporte\n\nO que precisa?',
                'suggestions' => [
                    ['text' => 'Problema técnico', 'action' => 'create_ticket'],
                    ['text' => 'Falar com suporte', 'action' => 'redirect_contact'],
                    ['text' => 'Ver guias de ajuda', 'action' => 'help_guide', 'guide_type' => 'general']
                ],
                'action' => 'help_request'
            ];
        }
        
        // Resposta padrão com sugestões inteligentes
        return [
            'response' => '🤔 Entendi que você precisa de ajuda. Para te atender melhor, escolha uma das opções abaixo ou descreva seu problema de forma mais específica:',
            'suggestions' => [
                ['text' => 'Criar chamado', 'action' => 'create_ticket'],
                ['text' => 'Problema no computador', 'action' => 'help_guide', 'guide_type' => 'hardware'],
                ['text' => 'Erro no sistema', 'action' => 'help_guide', 'guide_type' => 'software'],
                ['text' => 'Falar com atendente', 'action' => 'redirect_contact']
            ],
            'action' => 'default_response'
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
            
            // Média de tickets neste dia da semana
            $avgForDay = Ticket::whereRaw('DAYNAME(created_at) = ?', [$dayOfWeek])
                ->where('created_at', '>=', now()->subDays(30))
                ->count() / 4; // Aproximadamente 4 semanas
            
            $predictions[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $dayOfWeek,
                'predicted_tickets' => round($avgForDay),
                'confidence' => 0.7 // Confiança básica
            ];
        }
        
        return $predictions;
    }

    /**
     * Sugere artigos da base de conhecimento
     */
    public function suggestKnowledgeArticles($ticketTitle, $ticketDescription, $limit = 3)
    {
        $searchTerms = $this->extractKeywords($ticketTitle . ' ' . $ticketDescription);
        $articles = KnowledgeArticle::where('is_published', true)->get();
        
        $scoredArticles = [];
        
        foreach ($articles as $article) {
            $score = 0;
            
            foreach ($searchTerms as $term) {
                $score += $this->calculateRelevanceScore($article, $term);
            }
            
            if ($score > 0) {
                $scoredArticles[] = [
                    'article' => $article,
                    'score' => $score
                ];
            }
        }
        
        // Ordenar por relevância
        usort($scoredArticles, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return array_slice($scoredArticles, 0, $limit);
    }

    /**
     * Extrai palavras-chave relevantes
     */
    private function extractKeywords($text)
    {
        $stopWords = ['o', 'a', 'os', 'as', 'um', 'uma', 'de', 'do', 'da', 'dos', 'das', 'em', 'no', 'na', 'nos', 'nas', 'para', 'por', 'com', 'sem', 'que', 'não', 'é', 'são', 'foi', 'foram'];
        
        $words = preg_split('/[^\w]+/u', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });
        
        return array_unique($keywords);
    }

    /**
     * Calcula pontuação de relevância
     */
    private function calculateRelevanceScore($article, $keyword)
    {
        $titleMatches = substr_count(strtolower($article->title), strtolower($keyword));
        $contentMatches = substr_count(strtolower($article->content), strtolower($keyword));
        $excerptMatches = substr_count(strtolower($article->excerpt), strtolower($keyword));
        
        return ($titleMatches * 3) + ($excerptMatches * 2) + $contentMatches;
    }
}
