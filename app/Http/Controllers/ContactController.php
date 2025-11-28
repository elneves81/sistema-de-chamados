<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\ContactMessage;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Exibe a página de contato
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Envia mensagem de contato
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:suporte,duvida,sugestao,emergencia'
        ]);

        try {
            // 1. Salvar mensagem no banco de dados
            $contactMessage = ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'type' => $request->type,
                'user_id' => Auth::id(), // Se usuário estiver logado
                'status' => 'pendente'
            ]);

            // 2. Notificar administradores via sistema interno de mensagens
            $this->notifyAdministrators($contactMessage);

            // 3. Enviar email para administradores (se configurado)
            $this->sendEmailNotification($contactMessage);

            $response = [
                'success' => true,
                'message' => 'Mensagem enviada com sucesso! Nossa equipe entrará em contato em breve.',
                'contact_id' => $contactMessage->id
            ];

            // Resposta diferente para emergências
            if ($request->type === 'emergencia') {
                $response['message'] = '🚨 EMERGÊNCIA registrada! Nossa equipe foi notificada e entrará em contato IMEDIATAMENTE.';
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Erro ao processar mensagem de contato: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem. Tente novamente ou entre em contato por telefone.'
            ], 500);
        }
    }

    /**
     * Listar mensagens de contato (apenas admins)
     */
    public function listMessages()
    {
        if (!in_array(Auth::user()->role, ['admin', 'technician'])) {
            abort(403, 'Acesso negado');
        }

        $messages = ContactMessage::with(['user', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('contact.admin-list', compact('messages'));
    }

    /**
     * Ver detalhes de uma mensagem (apenas admins)
     */
    public function showMessage(ContactMessage $contactMessage)
    {
        if (!in_array(Auth::user()->role, ['admin', 'technician'])) {
            abort(403, 'Acesso negado');
        }

        return view('contact.admin-show', compact('contactMessage'));
    }

    /**
     * Responder mensagem de contato
     */
    public function respond(Request $request, ContactMessage $contactMessage)
    {
        if (!in_array(Auth::user()->role, ['admin', 'technician'])) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'admin_response' => 'required|string|max:5000',
            'status' => 'required|in:em_andamento,resolvido'
        ]);

        $contactMessage->update([
            'admin_response' => $request->admin_response,
            'status' => $request->status,
            'responded_by' => Auth::id(),
            'responded_at' => now()
        ]);

        // Enviar email com a resposta
        try {
            Mail::to($contactMessage->email)->send(new \App\Mail\ContactResponseMail($contactMessage));
        } catch (\Exception $e) {
            Log::warning('Erro ao enviar email de resposta de contato', [
                'contact_message_id' => $contactMessage->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->back()->with('success', 'Resposta enviada com sucesso!');
    }

    /**
     * Atualizar status de mensagem
     */
    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        if (!in_array(Auth::user()->role, ['admin', 'technician'])) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'status' => 'required|in:pendente,em_andamento,resolvido,arquivado',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $contactMessage->update([
            'status' => $request->status,
            'assigned_to' => Auth::id(),
            'admin_notes' => $request->admin_notes,
            'responded_at' => now()
        ]);

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    /**
     * Notificar administradores via sistema interno
     */
    private function notifyAdministrators(ContactMessage $contactMessage)
    {
        // Buscar todos os administradores
        $admins = User::whereIn('role', ['admin', 'technician'])->get();

        $priority = $contactMessage->type === 'emergencia' ? 'urgent' : 'medium';
        $subject = "🔔 {$contactMessage->getTypeLabel()}: {$contactMessage->subject}";
        
        $message = "Nova mensagem via Fale Conosco:\n\n";
        $message .= "👤 Remetente: {$contactMessage->name} ({$contactMessage->email})\n";
        $message .= "📋 Tipo: {$contactMessage->getTypeLabel()}\n";
        $message .= "📝 Assunto: {$contactMessage->subject}\n\n";
        $message .= "💬 Mensagem:\n{$contactMessage->message}\n\n";
        $message .= "🔗 Para responder, acesse: /admin/contact-messages/{$contactMessage->id}";

        // Enviar mensagem interna para cada admin
        foreach ($admins as $admin) {
            UserMessage::create([
                'from_user_id' => 1, // Sistema
                'to_user_id' => $admin->id,
                'subject' => $subject,
                'message' => $message,
                'priority' => $priority
            ]);
        }
    }

    /**
     * Enviar notificação por email (implementação futura)
     */
    private function sendEmailNotification(ContactMessage $contactMessage)
    {
        // TODO: Implementar envio de email quando configurado
        // Mail::to(config('contact.admin_email'))
        //     ->send(new ContactMessageNotification($contactMessage));
        
        Log::info("Mensagem de contato #{$contactMessage->id} - Email seria enviado para admins");
    }
}
