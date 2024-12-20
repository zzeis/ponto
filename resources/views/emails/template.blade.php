<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject ?? 'Email' }}</title>
</head>
<body>
    <h1>{{ $subject ?? 'Título do Email' }}</h1>
    <p>{{ $body ?? 'Corpo do email aqui' }}</p>
</body>
</html>
