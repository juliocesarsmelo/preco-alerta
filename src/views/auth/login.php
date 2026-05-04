<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Preço Alerta</title>
</head>
<body>
    <?php if (isset($_SESSION['erro'])): ?>
        <p style="color:red"><?= $_SESSION['erro'] ?></p>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <p style="color:green"><?= $_SESSION['sucesso'] ?></p>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <h2>Login</h2>
    <hr>

    <br>
    <form method="POST" action="index.php?route=login">
        <input type="email" name="email" placeholder="Email"><br><br>
        <input type="password" name="password" placeholder="Senha"><br><br>

        <button type="submit">Entrar</button>
    </form>
    
    <br><br>
    <a href="index.php?route=forgot">Esqueci minha senha</a> | 
    <a href="index.php?route=register">Criar conta</a>
</body>
</html>