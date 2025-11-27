<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciais de Acesso - OPNsense Web UI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4472C4;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4472C4;
            margin: 0 0 5px 0;
            font-size: 20pt;
        }
        .header p {
            color: #666;
            margin: 5px 0;
            font-size: 10pt;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 10pt;
        }
        .credential-card {
            border: 2px solid #4472C4;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            page-break-inside: avoid;
            background-color: #f8f9fa;
        }
        .credential-card h3 {
            color: #4472C4;
            margin: 0 0 15px 0;
            font-size: 14pt;
            border-bottom: 1px solid #4472C4;
            padding-bottom: 8px;
        }
        .credential-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .credential-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            color: #555;
            vertical-align: top;
        }
        .credential-value {
            display: table-cell;
            width: 70%;
            background-color: #fff;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 11pt;
        }
        .password-highlight {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            font-weight: bold;
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .warning-box strong {
            color: #856404;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
            color: #fff;
        }
        .badge-aluno {
            background-color: #28a745;
        }
        .badge-professor {
            background-color: #007bff;
        }
        .badge-funcionario {
            background-color: #6f42c1;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Credenciais de Acesso</h1>
        <p><strong>Sistema OPNsense Web UI - Controle de Acesso à Internet</strong></p>
        <p>Gerado em: {{ $generated_at }}</p>
    </div>

    <div class="warning-box">
        <strong>⚠️ ATENÇÃO - CONFIDENCIAL:</strong> Este documento contém informações sensíveis.
        Guarde em local seguro e não compartilhe suas credenciais com terceiros.
    </div>

    <div class="info-box">
        <p><strong>Total de usuários:</strong> {{ $total }}</p>
        <p><strong>Instruções:</strong> Use as credenciais abaixo para acessar o sistema. A senha é gerada automaticamente e deve ser alterada no primeiro acesso.</p>
    </div>

    @foreach($credentials as $index => $user)
    <div class="credential-card">
        <h3>
            Usuário {{ $index + 1 }} de {{ $total }}
            <span class="badge badge-{{ $user['user_type'] }}">{{ strtoupper($user['user_type']) }}</span>
        </h3>

        <div class="credential-row">
            <div class="credential-label">RA (Registro):</div>
            <div class="credential-value">{{ $user['ra'] }}</div>
        </div>

        <div class="credential-row">
            <div class="credential-label">Nome completo:</div>
            <div class="credential-value">{{ $user['fullname'] }}</div>
        </div>

        <div class="credential-row">
            <div class="credential-label">Senha Inicial:</div>
            <div class="credential-value password-highlight">{{ $user['password'] }}</div>
        </div>

        <div class="credential-row">
            <div class="credential-label">Código de Redefinição:</div>
            <div class="credential-value" style="background-color: #e7f3ff; border-color: #0066cc;">{{ $user['reset_code'] }}</div>
        </div>

        <div style="margin-top: 15px; padding: 10px; background-color: #e7f3ff; border-left: 4px solid #0066cc; font-size: 9pt;">
            <strong>📌 Importante:</strong>
            <ul style="margin: 5px 0; padding-left: 15px;">
                <li>Altere sua senha no primeiro acesso para garantir a segurança da sua conta</li>
                <li><strong>Guarde o código de redefinição</strong> - você precisará dele se esquecer sua senha</li>
                <li>Para redefinir sua senha, acesse a página de redefinição e informe seu RA + código</li>
            </ul>
        </div>
    </div>
    @endforeach

    <div class="footer">
        <p><strong>Suporte Técnico</strong></p>
        <p>Em caso de dúvidas ou problemas, entre em contato com o setor de TI.</p>
        <p style="margin-top: 10px; font-size: 8pt;">
            Este documento foi gerado automaticamente pelo sistema OPNsense Web UI.<br>
            © {{ date('Y') }} - Todos os direitos reservados.
        </p>
    </div>
</body>
</html>
