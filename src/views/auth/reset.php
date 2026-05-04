<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha | Preço Alerta</title>
</head>
<body>
    <h2>Redefinir Senha</h2>
    <hr>

    <br>
    <form method="POST">
        <input type="hidden" name="token" value="<?= $_GET['token'] ?>">
        
        <input type="password" name="password" placeholder="Nova senha">
        
        <button type="submit">Confirmar</button>
    </form>
</body>
</html>