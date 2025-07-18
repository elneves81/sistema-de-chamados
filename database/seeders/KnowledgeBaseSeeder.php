<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeArticle;
use App\Models\User;

class KnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar categorias
        $categories = [
            [
                'name' => 'Hardware',
                'description' => 'Artigos sobre problemas e soluções de hardware',
                'icon' => 'fas fa-desktop',
                'color' => '#007bff',
                'sort_order' => 1
            ],
            [
                'name' => 'Software',
                'description' => 'Artigos sobre instalação e configuração de software',
                'icon' => 'fas fa-code',
                'color' => '#28a745',
                'sort_order' => 2
            ],
            [
                'name' => 'Rede',
                'description' => 'Artigos sobre problemas de conectividade e rede',
                'icon' => 'fas fa-network-wired',
                'color' => '#ffc107',
                'sort_order' => 3
            ],
            [
                'name' => 'Segurança',
                'description' => 'Artigos sobre segurança da informação',
                'icon' => 'fas fa-shield-alt',
                'color' => '#dc3545',
                'sort_order' => 4
            ],
            [
                'name' => 'Procedimentos',
                'description' => 'Procedimentos gerais da empresa',
                'icon' => 'fas fa-list-check',
                'color' => '#6f42c1',
                'sort_order' => 5
            ]
        ];

        foreach ($categories as $categoryData) {
            KnowledgeCategory::create($categoryData);
        }

        // Obter usuário admin
        $admin = User::where('email', 'admin@admin.com')->first();
        if (!$admin) {
            $admin = User::first();
        }

        // Criar artigos de exemplo
        $articles = [
            [
                'title' => 'Como resolver problemas de impressora',
                'content' => '<h2>Problemas comuns com impressoras</h2>
                <p>Este artigo aborda os problemas mais comuns encontrados com impressoras e suas soluções.</p>
                
                <h3>1. Impressora não liga</h3>
                <ul>
                    <li>Verifique se o cabo de energia está conectado</li>
                    <li>Teste a tomada com outro equipamento</li>
                    <li>Verifique se o botão power está funcionando</li>
                </ul>
                
                <h3>2. Impressora não imprime</h3>
                <ul>
                    <li>Verifique se há papel na bandeja</li>
                    <li>Confirme se os cartuchos têm tinta</li>
                    <li>Reinicie o spooler de impressão</li>
                </ul>
                
                <h3>3. Qualidade de impressão ruim</h3>
                <ul>
                    <li>Execute a limpeza dos cabeçotes</li>
                    <li>Verifique o alinhamento da impressora</li>
                    <li>Substitua cartuchos vazios</li>
                </ul>',
                'excerpt' => 'Guia completo para resolver os problemas mais comuns com impressoras.',
                'category_id' => 1, // Hardware
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['impressora', 'hardware', 'troubleshooting'],
                'views' => 150,
                'published_at' => now()
            ],
            [
                'title' => 'Instalação do Microsoft Office',
                'content' => '<h2>Como instalar o Microsoft Office</h2>
                <p>Guia passo a passo para instalação do Microsoft Office em computadores da empresa.</p>
                
                <h3>Pré-requisitos</h3>
                <ul>
                    <li>Windows 10 ou superior</li>
                    <li>4GB de RAM mínimo</li>
                    <li>10GB de espaço livre em disco</li>
                    <li>Conexão com internet</li>
                </ul>
                
                <h3>Passos para instalação</h3>
                <ol>
                    <li>Acesse o portal da Microsoft</li>
                    <li>Faça login com as credenciais da empresa</li>
                    <li>Baixe o instalador</li>
                    <li>Execute como administrador</li>
                    <li>Siga as instruções na tela</li>
                </ol>
                
                <h3>Ativação</h3>
                <p>O Office será ativado automaticamente com as credenciais corporativas.</p>',
                'excerpt' => 'Instruções detalhadas para instalação do Microsoft Office.',
                'category_id' => 2, // Software
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => false,
                'tags' => ['office', 'microsoft', 'instalação'],
                'views' => 89,
                'published_at' => now()
            ],
            [
                'title' => 'Configuração de Wi-Fi corporativo',
                'content' => '<h2>Como conectar ao Wi-Fi da empresa</h2>
                <p>Este artigo explica como configurar a conexão Wi-Fi nos dispositivos corporativos.</p>
                
                <h3>Informações necessárias</h3>
                <ul>
                    <li>Nome da rede: EMPRESA_WIFI</li>
                    <li>Tipo de segurança: WPA2-Enterprise</li>
                    <li>Método EAP: PEAP</li>
                </ul>
                
                <h3>Windows 10/11</h3>
                <ol>
                    <li>Clique no ícone Wi-Fi na barra de tarefas</li>
                    <li>Selecione EMPRESA_WIFI</li>
                    <li>Digite suas credenciais corporativas</li>
                    <li>Aceite o certificado de segurança</li>
                </ol>
                
                <h3>Dispositivos móveis</h3>
                <p>Para configuração em smartphones e tablets, consulte o setor de TI.</p>',
                'excerpt' => 'Guia para configuração da rede Wi-Fi corporativa.',
                'category_id' => 3, // Rede
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['wifi', 'rede', 'configuração'],
                'views' => 234,
                'published_at' => now()
            ],
            [
                'title' => 'Política de senhas seguras',
                'content' => '<h2>Diretrizes para criação de senhas seguras</h2>
                <p>A segurança da informação começa com senhas robustas. Siga estas diretrizes:</p>
                
                <h3>Características de uma senha segura</h3>
                <ul>
                    <li>Mínimo de 12 caracteres</li>
                    <li>Combine letras maiúsculas e minúsculas</li>
                    <li>Inclua números e símbolos especiais</li>
                    <li>Evite palavras do dicionário</li>
                    <li>Não use informações pessoais</li>
                </ul>
                
                <h3>Exemplos de senhas fracas</h3>
                <ul>
                    <li>123456</li>
                    <li>password</li>
                    <li>nome + data de nascimento</li>
                    <li>sequências de teclado (qwerty)</li>
                </ul>
                
                <h3>Uso de gerenciadores de senha</h3>
                <p>Recomendamos o uso de gerenciadores de senha aprovados pela empresa.</p>
                
                <h3>Autenticação de dois fatores</h3>
                <p>Sempre que possível, habilite a autenticação de dois fatores (2FA).</p>',
                'excerpt' => 'Diretrizes essenciais para criação e gerenciamento de senhas seguras.',
                'category_id' => 4, // Segurança
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['segurança', 'senhas', 'política'],
                'views' => 312,
                'published_at' => now()
            ],
            [
                'title' => 'Procedimento para abertura de chamados',
                'content' => '<h2>Como abrir um chamado no sistema</h2>
                <p>Guia passo a passo para abertura de chamados no sistema de help desk.</p>
                
                <h3>Acesso ao sistema</h3>
                <ol>
                    <li>Acesse o portal interno da empresa</li>
                    <li>Clique em "Abrir Chamado"</li>
                    <li>Faça login com suas credenciais</li>
                </ol>
                
                <h3>Preenchimento do chamado</h3>
                <ul>
                    <li><strong>Título:</strong> Descreva o problema resumidamente</li>
                    <li><strong>Categoria:</strong> Selecione a categoria apropriada</li>
                    <li><strong>Prioridade:</strong> Avalie a urgência do problema</li>
                    <li><strong>Descrição:</strong> Detalhe o problema e passos reproduzidos</li>
                </ul>
                
                <h3>Informações importantes</h3>
                <ul>
                    <li>Anexe prints ou logs quando necessário</li>
                    <li>Informe sua localização física</li>
                    <li>Mencione horário que o problema ocorreu</li>
                </ul>
                
                <h3>Acompanhamento</h3>
                <p>Você receberá atualizações por email sobre o andamento do chamado.</p>',
                'excerpt' => 'Instruções para abertura e acompanhamento de chamados.',
                'category_id' => 5, // Procedimentos
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => false,
                'tags' => ['chamado', 'procedimento', 'helpdesk'],
                'views' => 67,
                'published_at' => now()
            ]
        ];

        foreach ($articles as $articleData) {
            KnowledgeArticle::create($articleData);
        }

        $this->command->info('Base de conhecimento populada com sucesso!');
    }
}
