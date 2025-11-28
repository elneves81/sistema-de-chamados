<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resposta - {{ $contactMessage->subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .response-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .response-box h3 {
            margin-top: 0;
            color: #0066cc;
        }
        .original-message {
            background: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            border-left: 4px solid #6c757d;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        .watermark {
            margin-top: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .watermark .hubi {
            color: #10b981;
        }
        .watermark .software {
            color: #f97316;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Resposta da sua Mensagem</h1>
        </div>
        
        <div class="content">
            <p>Olá <strong>{{ $contactMessage->name }}</strong>,</p>
            
            <p>Recebemos sua mensagem e estamos respondendo:</p>
            
            <div class="info-box">
                <p><strong>📋 Assunto:</strong> {{ $contactMessage->subject }}</p>
                <p><strong>🔖 Tipo:</strong> {{ $contactMessage->getTypeLabel() }}</p>
                <p><strong>📅 Data:</strong> {{ $contactMessage->created_at->format('d/m/Y H:i') }}</p>
                @if($contactMessage->respondedBy)
                <p><strong>👤 Respondido por:</strong> {{ $contactMessage->respondedBy->name }}</p>
                @endif
            </div>

            <div class="response-box">
                <h3>💬 Nossa Resposta:</h3>
                <p style="white-space: pre-wrap; margin: 0;">{{ $contactMessage->admin_response }}</p>
            </div>

            <div class="original-message">
                <h4 style="margin-top: 0; color: #6c757d;">📝 Sua Mensagem Original:</h4>
                <p style="white-space: pre-wrap; margin: 0;">{{ $contactMessage->message }}</p>
            </div>

            <p>Se você tiver mais dúvidas ou precisar de esclarecimentos adicionais, não hesite em entrar em contato conosco novamente.</p>

            <center>
                <a href="{{ url('/contact') }}" class="btn">📮 Enviar Nova Mensagem</a>
            </center>

            <p style="margin-top: 30px; font-size: 14px; color: #6c757d;">
                <em>Esta é uma mensagem automática. Por favor, não responda diretamente a este email.</em>
            </p>
        </div>
        
        <div class="footer">
            <p>Sistema de Chamados - Secretaria Municipal de Saúde</p>
            <p>Guarapuava/PR</p>
            <div class="watermark">
                <span class="hubi">HUBI</span> <span class="software">SOFTWARE</span>
            </div>
            <p style="margin-top: 10px;">© {{ date('Y') }} - Todos os direitos reservados</p>
        </div>
    </div>
</body>
</html>
