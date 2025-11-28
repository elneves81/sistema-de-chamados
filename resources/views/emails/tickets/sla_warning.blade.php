<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .warning-box { background: #fef3c7; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #dc2626; }
        .ticket-info { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #dc2626; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; color: #dc2626; }
        .button { display: inline-block; padding: 12px 24px; background: #dc2626; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ ALERTA: SLA Próximo do Vencimento</h1>
        </div>
        <div class="content">
            <div class="warning-box">
                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #dc2626;">
                    ⏰ ATENÇÃO URGENTE!
                </p>
                <p style="margin: 10px 0 0 0;">
                    O prazo de atendimento (SLA) está próximo do vencimento!
                </p>
            </div>
            
            <p>Olá, <strong>{{ $assigned_to_name ?? $user_name }}</strong>!</p>
            
            <p>O seguinte chamado precisa de atenção imediata:</p>
            
            <div class="ticket-info">
                <div class="info-row">
                    <span class="label">Chamado:</span> #{{ $ticket_id }}
                </div>
                <div class="info-row">
                    <span class="label">Título:</span> {{ $title }}
                </div>
                <div class="info-row">
                    <span class="label">Prioridade:</span> 
                    <span style="color: #dc2626; font-weight: bold;">URGENTE</span>
                </div>
                @if(isset($sla_remaining))
                <div class="info-row">
                    <span class="label">Tempo Restante:</span> 
                    <span style="color: #dc2626; font-weight: bold;">{{ $sla_remaining }}</span>
                </div>
                @endif
                @if(isset($created_at))
                <div class="info-row">
                    <span class="label">Criado em:</span> {{ $created_at }}
                </div>
                @endif
            </div>
            
            <center>
                <a href="{{ $url }}" class="button">VER CHAMADO AGORA</a>
            </center>
            
            <p style="margin-top: 20px; font-size: 14px; color: #dc2626; font-weight: bold;">
                ⚠️ Este chamado requer ação imediata para não violar o SLA!
            </p>
        </div>
        <div class="footer">
            <p><strong style="color: #10b981;">HUBI</strong> <strong style="color: #f97316;">SOFTWARE</strong></p>
            <p>© {{ date('Y') }} Sistema de Chamados. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
