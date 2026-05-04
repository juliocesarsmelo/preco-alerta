<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Preço Alerta</title>
</head>
<body>
    <h1>Bem-vindo, <?= $_SESSION['name'] ?>!</h1>
    <hr>
    
    <br><br>
    <?php if (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'admin'): ?>
        <a href="index.php?route=admin">Painel Admin</a>
    <?php endif; ?> | 
    <a href="index.php?route=profile">Perfil</a> |
    <a href="index.php?route=logout">Sair</a>
</body>
</html>