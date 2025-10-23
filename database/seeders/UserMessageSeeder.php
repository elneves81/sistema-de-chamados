<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserMessage;
use App\Models\User;

class UserMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buscar usuários para criar mensagens de exemplo
        $admin = User::where('role', 'admin')->first();
        $users = User::where('role', '!=', 'admin')->limit(3)->get();
        
        if (!$admin || $users->count() == 0) {
            $this->command->info('Não há usuários suficientes para criar mensagens de exemplo.');
            return;
        }

        $messages = [
            [
                'subject' => 'Bem-vindo ao Sistema de Chamados!',
                'message' => 'Olá! Gostaríamos de dar as boas-vindas ao novo sistema de chamados. Este sistema de mensagens permite que você se comunique diretamente conosco. Qualquer dúvida, não hesite em entrar em contato!',
                'priority' => 'medium',
            ],
            [
                'subject' => 'Manutenção Programada - Sábado',
                'message' => 'Informamos que haverá uma manutenção programada no sistema neste sábado das 02:00 às 06:00. Durante este período, alguns serviços podem ficar indisponíveis. Agradecemos a compreensão.',
                'priority' => 'high',
            ],
            [
                'subject' => 'Nova Funcionalidade: Chat com IA',
                'message' => 'Temos o prazer de anunciar uma nova funcionalidade em nosso sistema: o Chat com Inteligência Artificial! Agora você pode obter respostas rápidas para suas dúvidas através do assistente IA disponível em todas as páginas.',
                'priority' => 'medium',
            ],
            [
                'subject' => 'URGENTE: Falha no Sistema de Email',
                'message' => 'Detectamos uma falha temporária no sistema de envio de emails. Nossa equipe técnica está trabalhando para resolver o problema. Estimamos que a normalização ocorra em até 2 horas.',
                'priority' => 'urgent',
            ],
            [
                'subject' => 'Treinamento: Como Usar o Sistema',
                'message' => 'Convidamos você para participar do treinamento sobre como usar eficientemente nosso sistema de chamados. O treinamento será realizado na próxima terça-feira, às 14:00, na sala de reuniões principal.',
                'priority' => 'low',
            ]
        ];

        foreach ($users as $index => $user) {
            // Selecionar algumas mensagens para cada usuário
            $userMessages = array_slice($messages, 0, rand(2, 4));
            
            foreach ($userMessages as $messageData) {
                UserMessage::create([
                    'from_user_id' => $admin->id,
                    'to_user_id' => $user->id,
                    'subject' => $messageData['subject'],
                    'message' => $messageData['message'],
                    'priority' => $messageData['priority'],
                    'is_read' => rand(0, 1), // Algumas lidas, algumas não
                    'email_sent' => true,
                ]);
            }
        }

        // Criar algumas respostas dos usuários para o admin
        $responses = [
            'Obrigado pela informação! Vou participar do treinamento.',
            'Entendi sobre a manutenção. Obrigado por avisar.',
            'A nova funcionalidade de IA está excelente! Parabéns!',
            'Tudo bem sobre o email. Aguardo a normalização.',
        ];

        foreach ($users->take(2) as $user) {
            UserMessage::create([
                'from_user_id' => $user->id,
                'to_user_id' => $admin->id,
                'subject' => 'Re: ' . $messages[rand(0, count($messages) - 1)]['subject'],
                'message' => $responses[rand(0, count($responses) - 1)],
                'priority' => 'medium',
                'is_read' => rand(0, 1),
                'email_sent' => true,
            ]);
        }

        $this->command->info('Mensagens de exemplo criadas com sucesso!');
    }
}
