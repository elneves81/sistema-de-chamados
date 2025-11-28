<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .ticket-info { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #667eea; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; color: #667eea; }
        .button { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 Novo Chamado Criado</h1>
        </div>
        <div class="content">
            <p>Olá, <strong>{{ $user_name }}</strong>!</p>
            
            <p>Um novo chamado foi criado no sistema:</p>
            
            <div class="ticket-info">
                <div class="info-row">
                    <span class="label">Chamado:</span> #{{ $ticket_id }}
                </div>
                <div class="info-row">
                    <span class="label">Título:</span> {{ $title }}
                </div>
                <div class="info-row">
                    <span class="label">Prioridade:</span> 
                    <span style="color: {{ $priority === 'urgent' ? '#dc2626' : ($priority === 'high' ? '#f59e0b' : '#10b981') }}">
                        {{ ucfirst($priority) }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Status:</span> {{ $status }}
                </div>
                @if(isset($description))
                <div class="info-row" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                    <span class="label">Descrição:</span><br>
                    {{ $description }}
                </div>
                @endif
            </div>
            
            <center>
                <a href="{{ $url }}" class="button">Ver Chamado</a>
            </center>
            
            <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
                Esta é uma notificação automática do Sistema de Chamados.
            </p>
        </div>
        <div class="footer">
            <p><strong style="color: #10b981;">HUBI</strong> <strong style="color: #f97316;">SOFTWARE</strong></p>
            <p>© {{ date('Y') }} Sistema de Chamados. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
