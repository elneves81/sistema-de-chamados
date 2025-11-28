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
        // Verificar se categorias já existem, se não criar
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
            KnowledgeCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        // Obter usuário admin
        $admin = User::where('email', 'admin@admin.com')->first();
        if (!$admin) {
            $admin = User::where('email', 'superadmin@sistema.com')->first();
        }
        if (!$admin) {
            $admin = User::first();
        }
        
        if (!$admin) {
            $this->command->error('Nenhum usuário encontrado! Execute UserSeeder primeiro.');
            return;
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
                <p>Para configuração em smartphones e tablets, consulte o DITIS - Departamento de Informação, Tecnologia e Inovação em Saúde.</p>',
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
            ],
            [
                'title' => 'Computador não liga - Diagnóstico completo',
                'content' => '<h2>Soluções para computador que não liga</h2>
                <p>Guia de diagnóstico para resolver problemas de computador que não inicia.</p>
                
                <h3>1. Verificações Básicas</h3>
                <ul>
                    <li>✅ Verifique se o cabo de energia está conectado firmemente</li>
                    <li>✅ Teste a tomada com outro equipamento</li>
                    <li>✅ Confirme se o estabilizador/nobreak está ligado</li>
                    <li>✅ Verifique se o botão de energia do computador responde</li>
                </ul>
                
                <h3>2. Problemas com Fonte de Alimentação</h3>
                <ul>
                    <li>🔌 Verifique o botão liga/desliga na fonte (traseira do PC)</li>
                    <li>🔌 Teste com outra fonte, se disponível</li>
                    <li>🔌 Escute se há ruído de ventilador ao ligar</li>
                </ul>
                
                <h3>3. Monitor e Cabos</h3>
                <ul>
                    <li>🖥️ Verifique se o monitor está ligado separadamente</li>
                    <li>🖥️ Confirme se o cabo de vídeo está conectado (VGA/HDMI/DisplayPort)</li>
                    <li>🖥️ Teste com outro monitor, se possível</li>
                </ul>
                
                <h3>4. Hardware Interno</h3>
                <ul>
                    <li>💾 Remova e reconecte os módulos de memória RAM</li>
                    <li>💾 Verifique conexões de HD/SSD</li>
                    <li>💾 Escute por bips de erro ao ligar</li>
                </ul>
                
                <h3>5. Quando chamar suporte</h3>
                <p>Se após essas verificações o problema persistir:</p>
                <ul>
                    <li>📞 Anote qualquer código de bip ou LED aceso</li>
                    <li>📞 Informe o patrimônio do equipamento</li>
                    <li>📞 Descreva o que aconteceu antes do problema</li>
                    <li>📞 Abra um chamado com prioridade ALTA</li>
                </ul>',
                'excerpt' => 'Guia completo de diagnóstico e solução para computador que não liga.',
                'category_id' => 1, // Hardware
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['computador', 'hardware', 'boot', 'diagnóstico', 'pc'],
                'views' => 423,
                'published_at' => now()
            ],
            [
                'title' => 'Internet lenta - Como resolver',
                'content' => '<h2>Soluções para internet lenta</h2>
                <p>Passo a passo para diagnosticar e resolver problemas de lentidão na internet.</p>
                
                <h3>1. Teste a Velocidade</h3>
                <ul>
                    <li>🌐 Acesse <strong>fast.com</strong> ou <strong>speedtest.net</strong></li>
                    <li>🌐 Execute o teste 3 vezes e anote os resultados</li>
                    <li>🌐 Compare com a velocidade contratada</li>
                </ul>
                
                <h3>2. Verificações Básicas</h3>
                <ul>
                    <li>📡 Reinicie o roteador (aguarde 30 segundos desligado)</li>
                    <li>📡 Verifique se há muitos dispositivos conectados</li>
                    <li>📡 Teste com cabo de rede (Ethernet) ao invés de Wi-Fi</li>
                    <li>📡 Feche programas que consomem banda (downloads, streaming)</li>
                </ul>
                
                <h3>3. Wi-Fi Específico</h3>
                <ul>
                    <li>📶 Aproxime-se do roteador</li>
                    <li>📶 Verifique interferências (micro-ondas, telefones sem fio)</li>
                    <li>📶 Teste trocar o canal do Wi-Fi (2.4GHz para 5GHz)</li>
                    <li>📶 Reconecte à rede Wi-Fi</li>
                </ul>
                
                <h3>4. Problemas no Computador</h3>
                <ul>
                    <li>💻 Execute antivírus completo</li>
                    <li>💻 Verifique atualizações do Windows</li>
                    <li>💻 Limpe arquivos temporários</li>
                    <li>💻 Desabilite VPN se não estiver usando</li>
                </ul>
                
                <h3>5. Quando abrir chamado</h3>
                <p>Abra chamado se:</p>
                <ul>
                    <li>❌ Velocidade está abaixo de 30% do contratado</li>
                    <li>❌ Problema persiste há mais de 2 horas</li>
                    <li>❌ Afeta múltiplos usuários no mesmo local</li>
                    <li>❌ Quedas frequentes de conexão</li>
                </ul>',
                'excerpt' => 'Diagnóstico e soluções para problemas de internet lenta.',
                'category_id' => 3, // Rede
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['internet', 'rede', 'wifi', 'lentidão', 'velocidade'],
                'views' => 892,
                'published_at' => now()
            ],
            [
                'title' => 'Outlook não envia emails - Soluções',
                'content' => '<h2>Como resolver problemas de envio no Outlook</h2>
                <p>Guia completo para solucionar erros de envio de email no Microsoft Outlook.</p>
                
                <h3>1. Verificações Iniciais</h3>
                <ul>
                    <li>✉️ Confirme se está conectado à internet</li>
                    <li>✉️ Verifique se o email está na caixa de saída</li>
                    <li>✉️ Confira se o destinatário está correto</li>
                    <li>✉️ Verifique o tamanho dos anexos (máx 25MB)</li>
                </ul>
                
                <h3>2. Modo Offline</h3>
                <ul>
                    <li>📧 Clique em "Enviar/Receber" na barra superior</li>
                    <li>📧 Verifique se "Trabalhar Offline" NÃO está marcado</li>
                    <li>📧 Se estiver, clique para desativar</li>
                </ul>
                
                <h3>3. Reparar Perfil de Email</h3>
                <ol>
                    <li>Feche o Outlook completamente</li>
                    <li>Painel de Controle → Mail</li>
                    <li>Clique em "Contas de Email"</li>
                    <li>Selecione sua conta → "Reparar"</li>
                    <li>Siga o assistente de reparo</li>
                    <li>Reinicie o Outlook</li>
                </ol>
                
                <h3>4. Limpar Caixa de Saída</h3>
                <ul>
                    <li>🗑️ Vá até a pasta "Caixa de Saída"</li>
                    <li>🗑️ Exclua emails presos (podem ter anexos grandes)</li>
                    <li>🗑️ Tente enviar novamente</li>
                </ul>
                
                <h3>5. Códigos de Erro Comuns</h3>
                <ul>
                    <li><strong>0x800CCC0E:</strong> Problema de conexão - verifique internet</li>
                    <li><strong>0x80042109:</strong> Timeout - tente novamente mais tarde</li>
                    <li><strong>0x800CCC13:</strong> Problema no servidor - contate TI</li>
                </ul>
                
                <h3>6. Quando abrir chamado</h3>
                <p>Chame o suporte se:</p>
                <ul>
                    <li>❌ Erro persiste após 1 hora</li>
                    <li>❌ Outros usuários também não conseguem enviar</li>
                    <li>❌ Mensagem de erro sobre credenciais</li>
                </ul>',
                'excerpt' => 'Soluções para problemas de envio de email no Outlook.',
                'category_id' => 2, // Software
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['outlook', 'email', 'envio', 'erro', 'office'],
                'views' => 534,
                'published_at' => now()
            ],
            [
                'title' => 'Recuperar senha do Windows',
                'content' => '<h2>Como recuperar acesso ao Windows</h2>
                <p>Procedimentos para recuperação de senha e acesso ao sistema.</p>
                
                <h3>⚠️ IMPORTANTE</h3>
                <p class="alert alert-warning">
                Por questões de segurança, <strong>NÃO</strong> tente ferramentas de terceiros para resetar senha. 
                Contate sempre o DITIS - Departamento de Informação, Tecnologia e Inovação em Saúde.
                </p>
                
                <h3>1. Senha de Domínio Corporativo</h3>
                <ul>
                    <li>🔐 Use outro computador para acessar o portal de auto-atendimento</li>
                    <li>🔐 Entre com seu email corporativo</li>
                    <li>🔐 Clique em "Esqueci minha senha"</li>
                    <li>🔐 Responda as perguntas de segurança</li>
                    <li>🔐 Defina nova senha (mínimo 12 caracteres)</li>
                </ul>
                
                <h3>2. Senha Local (Computador não conectado ao domínio)</h3>
                <ul>
                    <li>💻 Na tela de login, clique em "Redefinir senha"</li>
                    <li>💻 Responda a pergunta de segurança</li>
                    <li>💻 Crie nova senha</li>
                </ul>
                
                <h3>3. Se não conseguir recuperar</h3>
                <ol>
                    <li>Anote o patrimônio do equipamento</li>
                    <li>Anote seu nome de usuário (login)</li>
                    <li>Abra chamado URGENTE para o DITIS</li>
                    <li>Informe local e telefone para contato</li>
                    <li>Aguarde atendimento (SLA: 2 horas)</li>
                </ol>
                
                <h3>4. Prevenção</h3>
                <ul>
                    <li>✅ Configure perguntas de segurança</li>
                    <li>✅ Mantenha email de recuperação atualizado</li>
                    <li>✅ Use gerenciador de senhas aprovado</li>
                    <li>✅ Anote senha em local seguro (cofre)</li>
                </ul>',
                'excerpt' => 'Procedimento seguro para recuperação de senha do Windows.',
                'category_id' => 4, // Segurança
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['senha', 'windows', 'login', 'recuperação', 'acesso'],
                'views' => 1203,
                'published_at' => now()
            ],
            [
                'title' => 'Computador lento - Otimização',
                'content' => '<h2>Como melhorar o desempenho do computador</h2>
                <p>Guia completo de otimização para computadores lentos.</p>
                
                <h3>1. Verificação Inicial</h3>
                <ul>
                    <li>🔍 Abra o Gerenciador de Tarefas (Ctrl+Shift+Esc)</li>
                    <li>🔍 Verifique uso de CPU, Memória e Disco</li>
                    <li>🔍 Identifique processos consumindo muitos recursos</li>
                </ul>
                
                <h3>2. Limpeza de Disco</h3>
                <ol>
                    <li>Pressione Win+R e digite: cleanmgr</li>
                    <li>Selecione o disco C:</li>
                    <li>Marque todas as opções</li>
                    <li>Clique em "Limpar arquivos do sistema"</li>
                    <li>Aguarde conclusão</li>
                </ol>
                
                <h3>3. Desabilitar Programas de Inicialização</h3>
                <ol>
                    <li>Ctrl+Shift+Esc → aba "Inicializar"</li>
                    <li>Desabilite programas desnecessários</li>
                    <li>Mantenha apenas: antivírus e ferramentas essenciais</li>
                    <li>Reinicie o computador</li>
                </ol>
                
                <h3>4. Atualizações</h3>
                <ul>
                    <li>⬆️ Windows Update → Verificar atualizações</li>
                    <li>⬆️ Instale todas as atualizações pendentes</li>
                    <li>⬆️ Reinicie quando solicitado</li>
                </ul>
                
                <h3>5. Antivírus e Malware</h3>
                <ul>
                    <li>🛡️ Execute verificação completa do antivírus</li>
                    <li>🛡️ Use Windows Defender se não tiver outro</li>
                    <li>🛡️ Remova programas desconhecidos</li>
                </ul>
                
                <h3>6. Hardware</h3>
                <ul>
                    <li>💾 Verifique espaço livre no disco (mínimo 20GB)</li>
                    <li>💾 Considere upgrade de RAM (mínimo 8GB)</li>
                    <li>💾 Substitua HD por SSD (ganho de 300% performance)</li>
                </ul>
                
                <h3>7. Quando abrir chamado</h3>
                <p>Contate o DITIS se:</p>
                <ul>
                    <li>❌ Lentidão persiste após otimizações</li>
                    <li>❌ Disco sempre em 100% de uso</li>
                    <li>❌ Travamentos frequentes</li>
                    <li>❌ Tela azul (BSOD)</li>
                </ul>',
                'excerpt' => 'Guia de otimização e melhoria de performance do computador.',
                'category_id' => 1, // Hardware
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['performance', 'lento', 'otimização', 'computador', 'windows'],
                'views' => 756,
                'published_at' => now()
            ],
            [
                'title' => 'VPN corporativa - Configuração',
                'content' => '<h2>Como configurar VPN para acesso remoto</h2>
                <p>Instruções para configuração da VPN corporativa para trabalho remoto.</p>
                
                <h3>1. Requisitos</h3>
                <ul>
                    <li>✅ Computador corporativo ou aprovado pela TI</li>
                    <li>✅ Credenciais de domínio ativas</li>
                    <li>✅ Conexão estável de internet (mínimo 5Mbps)</li>
                    <li>✅ Autorização do gestor para acesso remoto</li>
                </ul>
                
                <h3>2. Download do Cliente VPN</h3>
                <ol>
                    <li>Acesse o portal interno: <strong>intranet.empresa.local</strong></li>
                    <li>Faça login com suas credenciais</li>
                    <li>Vá em TI → Downloads → VPN Client</li>
                    <li>Baixe o instalador para Windows</li>
                </ol>
                
                <h3>3. Instalação</h3>
                <ol>
                    <li>Execute o instalador como Administrador</li>
                    <li>Aceite os termos de uso</li>
                    <li>Mantenha as configurações padrão</li>
                    <li>Aguarde a instalação completa</li>
                    <li>Reinicie o computador</li>
                </ol>
                
                <h3>4. Configuração da Conexão</h3>
                <ol>
                    <li>Abra o cliente VPN</li>
                    <li>Clique em "Nova Conexão"</li>
                    <li>Server: <strong>vpn.empresa.local</strong></li>
                    <li>Tipo: <strong>SSL-VPN</strong></li>
                    <li>Autenticação: <strong>Domínio</strong></li>
                    <li>Salve as configurações</li>
                </ol>
                
                <h3>5. Conectar à VPN</h3>
                <ol>
                    <li>Abra o cliente VPN</li>
                    <li>Selecione a conexão criada</li>
                    <li>Digite: usuário@empresa.local</li>
                    <li>Digite sua senha de domínio</li>
                    <li>Clique em "Conectar"</li>
                    <li>Aguarde mensagem de sucesso</li>
                </ol>
                
                <h3>6. Problemas Comuns</h3>
                <ul>
                    <li><strong>Erro de autenticação:</strong> Verifique usuário e senha</li>
                    <li><strong>Timeout:</strong> Verifique internet e firewall</li>
                    <li><strong>Certificado inválido:</strong> Contate TI para renovação</li>
                </ul>
                
                <h3>⚠️ Importante</h3>
                <ul>
                    <li>🔒 Nunca compartilhe credenciais VPN</li>
                    <li>🔒 Desconecte ao finalizar o trabalho</li>
                    <li>🔒 Use apenas redes confiáveis</li>
                    <li>🔒 Não baixe arquivos pessoais pela VPN</li>
                </ul>',
                'excerpt' => 'Guia completo para configuração e uso da VPN corporativa.',
                'category_id' => 3, // Rede
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => false,
                'tags' => ['vpn', 'remoto', 'acesso', 'rede', 'segurança'],
                'views' => 289,
                'published_at' => now()
            ]
        ];

        foreach ($articles as $articleData) {
            KnowledgeArticle::create($articleData);
        }

        $this->command->info('Base de conhecimento populada com sucesso!');
        $this->command->info('Total de artigos criados: ' . count($articles));
    }
}
