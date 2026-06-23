<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Preço Alerta</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <h1>Bem-vindo, <?= $_SESSION['name'] ?>!</h1>
    <hr>
    
    <br><br>
    <div class="card">
        <a class="menu-link" href="index.php?route=products">Produtos Monitorados</a>
    </div>

    <div class="card">
        <a class="menu-link" href="index.php?route=profile">Meu Perfil</a>
    </div>

    <?php if ($_SESSION['perfil'] === 'admin'): ?>
    <div class="card">
        <a class="menu-link" href="index.php?route=admin">Painel Administrativo</a>
    </div>
    <?php endif; ?>

    <div class="card">
        <a class="menu-link" href="index.php?route=logout">Sair</a>
    </div>
</body>
</html>