<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $msg->subject }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background: #f6f7fb; margin: 0; padding: 0; }
        .container { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
        .header { background: #0d6efd; color: #fff; padding: 20px 24px; }
        .content { padding: 24px; color: #333; }
        .footer { padding: 16px 24px; font-size: 12px; color: #777; text-align: center; }
        .btn { display: inline-block; background: #0d6efd; color: #fff !important; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .meta { font-size: 12px; color: #666; margin-top: 8px; }
        pre { white-space: pre-wrap; font-family: inherit; background: #f8f9fa; padding: 12px; border-left: 3px solid #0d6efd; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Nova mensagem de {{ $msg->fromUser->name }}</h2>
        </div>
        <div class="content">
            <p>Olá {{ $msg->toUser->name }},</p>
            <p>Você recebeu uma nova mensagem no sistema com o assunto:</p>
            <p><strong>{{ $msg->subject }}</strong></p>

            <p>Mensagem:</p>
            <pre>{{ $msg->message }}</pre>

            <p>
                <a class="btn" href="{{ url('/messages/'.$msg->id) }}">Abrir no sistema</a>
            </p>

            <p class="meta">
                Enviada em {{ $msg->created_at->format('d/m/Y H:i') }} · Prioridade: {{ $msg->priority_label }}
            </p>
        </div>
        <div class="footer">
            Esta é uma mensagem automática. Por favor, não responda este email.
        </div>
    </div>
</body>
</html>
