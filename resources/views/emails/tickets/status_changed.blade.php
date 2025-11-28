<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .ticket-info { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #f59e0b; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; color: #f59e0b; }
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 4px; font-weight: bold; }
        .button { display: inline-block; padding: 12px 24px; background: #f59e0b; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Status do Chamado Alterado</h1>
        </div>
        <div class="content">
            <p>Olá, <strong>{{ $user_name }}</strong>!</p>
            
            <p>O status do seu chamado foi atualizado:</p>
            
            <div class="ticket-info">
                <div class="info-row">
                    <span class="label">Chamado:</span> #{{ $ticket_id }}
                </div>
                <div class="info-row">
                    <span class="label">Título:</span> {{ $title }}
                </div>
                <div class="info-row">
                    <span class="label">Status Anterior:</span> 
                    <span class="status-badge" style="background: #e5e7eb; color: #6b7280;">{{ $old_status }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Novo Status:</span> 
                    <span class="status-badge" style="background: #10b981; color: white;">{{ $new_status }}</span>
                </div>
                @if(isset($assigned_to_name))
                <div class="info-row">
                    <span class="label">Responsável:</span> {{ $assigned_to_name }}
                </div>
                @endif
            </div>
            
            <center>
                <a href="{{ $url }}" class="button">Ver Chamado</a>
            </center>
            
            <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
                Acompanhe o andamento do seu chamado através do sistema.
            </p>
        </div>
        <div class="footer">
            <p><strong style="color: #10b981;">HUBI</strong> <strong style="color: #f97316;">SOFTWARE</strong></p>
            <p>© {{ date('Y') }} Sistema de Chamados. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
