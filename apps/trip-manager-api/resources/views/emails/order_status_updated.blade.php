<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização de Status do Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #007bff;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .header h1 {
            color: #007bff;
            font-size: 24px;
            margin: 0;
        }
        .content {
            padding: 20px 0;
            line-height: 1.6;
        }
        .content p {
            margin-bottom: 10px;
        }
        .highlight {
            font-weight: bold;
            color: #007bff;
        }
        .order-details {
            background-color: #f9f9f9;
            border-left: 5px solid #e0e0e0;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .order-details p {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #888888;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Atualização do Seu Pedido</h1>
        </div>
        <div class="content">
            <p>Olá, <span class="highlight">{{ $order->getUser()->getName()}}</span>!</p>
            <p>Temos uma atualização importante sobre o seu pedido.</p>

            <div class="order-details">
                <p>O status do seu pedido <span class="highlight">#{{ $order->getId() }}</span> foi atualizado para:</p>
                <p style="font-size: 20px; text-align: center; font-weight: bold;">{{ $order->getStatus() }}</p>
            </div>
        </div>
        <div class="footer">
            <p>Este é um e-mail automático, por favor, não responda.</p>
            <p>&copy; 2025. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>