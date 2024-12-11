<!DOCTYPE html>
<html>
<head>
    <title>Redefinição de Senha</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>Redefinição de Senha</h2>
        <p>Olá {{ $user->name }},</p>
        <p>Você solicitou a redefinição da sua senha. Clique no botão abaixo:</p>
        <a href="{{ $resetUrl }}" 
           style="display: inline-block; background-color: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Redefinir Senha
        </a>
        <p>Este link expira em {{ config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') }} minutos.</p>
        <p>Se você não solicitou a redefinição, ignore este e-mail.</p>
    </div>
</body>
</html>