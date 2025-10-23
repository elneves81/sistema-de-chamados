<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeBase;
use App\Models\User;
use App\Models\Category;

class AiKnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar usuário admin ou primeiro usuário
        $admin = User::where('email', 'admin@admin.com')->first() ?? User::first();
        
        if (!$admin) {
            $this->command->error('Nenhum usuário encontrado para criar os artigos da base de conhecimento.');
            return;
        }

        // Buscar categorias existentes
        $hardwareCategory = Category::where('name', 'like', '%hardware%')->first();
        $softwareCategory = Category::where('name', 'like', '%software%')->first();
        $redeCategory = Category::where('name', 'like', '%rede%')->first();
        $suporteCategory = Category::where('name', 'like', '%suporte%')->first();

        $articles = [
            [
                'title' => 'Como resolver problemas de computador que não liga',
                'content' => '<h2>Problemas de Hardware - Computador não liga</h2>
                <p>Este guia ajuda a resolver problemas quando o computador não liga ou não dá sinal de energia.</p>
                
                <h3>Verificações iniciais</h3>
                <ol>
                    <li><strong>Cabo de energia:</strong> Verifique se o cabo está bem conectado na fonte e na tomada</li>
                    <li><strong>Botão liga/desliga:</strong> Certifique-se que está pressionando o botão correto</li>
                    <li><strong>Filtro de linha:</strong> Teste se o filtro está funcionando</li>
                    <li><strong>Tomada:</strong> Teste a tomada com outro equipamento</li>
                </ol>
                
                <h3>Verificações avançadas</h3>
                <ul>
                    <li>Verifique se a fonte está com a chave 110V/220V na posição correta</li>
                    <li>Teste com outro cabo de energia</li>
                    <li>Verifique se há componentes soltos internamente</li>
                    <li>Teste a fonte de alimentação</li>
                </ul>
                
                <h3>Quando chamar o técnico</h3>
                <p>Se após essas verificações o problema persistir, abra um chamado informando todas as verificações já realizadas.</p>',
                'excerpt' => 'Guia para resolver problemas quando o computador não liga ou não dá sinal de energia.',
                'category_id' => $hardwareCategory?->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['hardware', 'computador', 'energia', 'fonte', 'cabo'],
                'views' => 156,
                'published_at' => now()
            ],
            [
                'title' => 'Problemas com mouse e teclado não funcionando',
                'content' => '<h2>Solução para problemas de mouse e teclado</h2>
                <p>Passos para resolver problemas quando mouse ou teclado param de funcionar.</p>
                
                <h3>Mouse não funciona</h3>
                <ol>
                    <li>Verifique se o cabo USB está bem conectado</li>
                    <li>Teste em outra porta USB</li>
                    <li>Limpe o sensor ótico na parte inferior do mouse</li>
                    <li>Se for wireless, verifique as pilhas</li>
                    <li>Teste o mouse em outro computador</li>
                </ol>
                
                <h3>Teclado não funciona</h3>
                <ol>
                    <li>Verifique a conexão USB ou PS/2</li>
                    <li>Teste o Caps Lock e Num Lock para ver se acendem</li>
                    <li>Reinicie o computador com o teclado conectado</li>
                    <li>Se for wireless, verifique as pilhas</li>
                    <li>Teste em outro computador</li>
                </ol>
                
                <h3>Dicas importantes</h3>
                <p>Sempre teste os periféricos em outro computador para confirmar se o problema é no equipamento ou no computador.</p>',
                'excerpt' => 'Como resolver problemas de mouse e teclado que param de funcionar.',
                'category_id' => $hardwareCategory?->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => false,
                'tags' => ['hardware', 'mouse', 'teclado', 'usb', 'wireless'],
                'views' => 89,
                'published_at' => now()
            ],
            [
                'title' => 'Como resolver erro de programa que não abre',
                'content' => '<h2>Programas que não abrem - Soluções</h2>
                <p>Guia para resolver problemas quando programas não inicializam ou apresentam erros.</p>
                
                <h3>Verificações básicas</h3>
                <ol>
                    <li><strong>Reiniciar o programa:</strong> Feche completamente e abra novamente</li>
                    <li><strong>Reiniciar o computador:</strong> Resolve muitos problemas temporários</li>
                    <li><strong>Executar como administrador:</strong> Clique com botão direito > "Executar como administrador"</li>
                    <li><strong>Verificar atualizações:</strong> Windows Update e atualizações do programa</li>
                </ol>
                
                <h3>Soluções avançadas</h3>
                <ul>
                    <li>Verificar se o antivírus está bloqueando</li>
                    <li>Desinstalar e reinstalar o programa</li>
                    <li>Verificar compatibilidade com a versão do Windows</li>
                    <li>Executar verificação de integridade do sistema (sfc /scannow)</li>
                </ul>
                
                <h3>Mensagens de erro comuns</h3>
                <p><strong>"Arquivo não encontrado":</strong> Programa pode estar corrompido, reinstale</p>
                <p><strong>"Acesso negado":</strong> Execute como administrador</p>
                <p><strong>"Falta de memória":</strong> Feche outros programas e reinicie</p>',
                'excerpt' => 'Soluções para programas que não abrem ou apresentam erros de inicialização.',
                'category_id' => $softwareCategory?->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['software', 'programa', 'erro', 'aplicativo', 'administrador'],
                'views' => 234,
                'published_at' => now()
            ],
            [
                'title' => 'Problemas de internet lenta ou sem conexão',
                'content' => '<h2>Solução para problemas de internet</h2>
                <p>Como resolver problemas de internet lenta, intermitente ou sem conexão.</p>
                
                <h3>Verificações de conexão</h3>
                <ol>
                    <li><strong>Cabo de rede:</strong> Verifique se está bem conectado</li>
                    <li><strong>Luzes do modem:</strong> Devem estar verdes ou azuis</li>
                    <li><strong>WiFi:</strong> Verifique se está conectado à rede correta</li>
                    <li><strong>Outros dispositivos:</strong> Teste se outros aparelhos têm internet</li>
                </ol>
                
                <h3>Soluções básicas</h3>
                <ul>
                    <li>Reiniciar o modem/roteador (desligar por 30 segundos)</li>
                    <li>Reiniciar o computador</li>
                    <li>Verificar se a conta está em dia com o provedor</li>
                    <li>Testar velocidade da internet</li>
                </ul>
                
                <h3>Configurações de rede</h3>
                <p><strong>Windows:</strong> Configurações > Rede e Internet > Status</p>
                <p><strong>Comando útil:</strong> ipconfig /release && ipconfig /renew</p>
                
                <h3>Quando chamar o provedor</h3>
                <p>Se o problema persistir após estas verificações, entre em contato com o provedor de internet.</p>',
                'excerpt' => 'Como resolver problemas de internet lenta, intermitente ou sem conexão.',
                'category_id' => $redeCategory?->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => true,
                'tags' => ['rede', 'internet', 'conexão', 'wifi', 'modem', 'roteador'],
                'views' => 312,
                'published_at' => now()
            ],
            [
                'title' => 'Impressora não imprime - Guia de solução',
                'content' => '<h2>Problemas com impressora</h2>
                <p>Como resolver os problemas mais comuns de impressão.</p>
                
                <h3>Verificações básicas</h3>
                <ol>
                    <li><strong>Energia:</strong> Impressora está ligada?</li>
                    <li><strong>Papel:</strong> Há papel na bandeja?</li>
                    <li><strong>Tinta/Toner:</strong> Cartuchos não estão vazios?</li>
                    <li><strong>Cabos:</strong> USB conectado no computador e impressora?</li>
                </ol>
                
                <h3>Problemas comuns</h3>
                <p><strong>Impressora offline:</strong></p>
                <ul>
                    <li>Painel de Controle > Dispositivos e Impressoras</li>
                    <li>Clique direito na impressora > "Usar impressora online"</li>
                    <li>Remova impressões pendentes na fila</li>
                </ul>
                
                <p><strong>Qualidade ruim:</strong></p>
                <ul>
                    <li>Execute limpeza dos cabeçotes</li>
                    <li>Verifique nível de tinta</li>
                    <li>Calibre a impressora</li>
                </ul>
                
                <h3>Papel atolado</h3>
                <ol>
                    <li>Desligue a impressora</li>
                    <li>Abra todas as tampas</li>
                    <li>Retire o papel cuidadosamente</li>
                    <li>Verifique se não sobraram pedaços</li>
                    <li>Ligue a impressora novamente</li>
                </ol>',
                'excerpt' => 'Soluções para os problemas mais comuns de impressão.',
                'category_id' => $hardwareCategory?->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'is_public' => true,
                'is_featured' => false,
                'tags' => ['hardware', 'impressora', 'papel', 'tinta', 'offline'],
                'views' => 145,
                'published_at' => now()
            ]
        ];

        foreach ($articles as $articleData) {
            // Verificar se o artigo já existe
            $exists = KnowledgeBase::where('title', $articleData['title'])->exists();
            
            if (!$exists) {
                KnowledgeBase::create($articleData);
                $this->command->info("Artigo criado: {$articleData['title']}");
            } else {
                $this->command->warn("Artigo já existe: {$articleData['title']}");
            }
        }

        $this->command->info('Base de conhecimento para IA populada com sucesso!');
    }
}
