<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Preço Alerta</title>
</head>
<body>
    <h2>Login</h2>

    <form method="POST" action="index.php?rota=login">
        <input type="email" name="email" placeholder="Email"><br><br>
        <input type="password" name="password" placeholder="Senha"><br><br>

        <button type="submit">Entrar</button>
    </form>

    <a href="index.php?rota=register">Criar conta</a>
</body>
</html>