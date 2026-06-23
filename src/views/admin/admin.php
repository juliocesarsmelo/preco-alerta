<?php /** @var array $users */ ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Preço Alerta</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <h2>Painel Administrativo</h2>

    <p>Bem-vindo(a), <?= $_SESSION['name'] ?></p>
    <hr>
    
    <h3>Usuários</h3>

    <?php foreach ($users as $user): ?>

        <p>
            <strong><?= $user['nome'] ?></strong> - <?= $user['email'] ?> 
            (<?= $user['perfil'] ?>)

            <?php if ($user['ativo']): ?>
                <a class="btn btn-danger" href="index.php?route=toggle-user&id=<?= $user['id'] ?>&status=0">
                    Desativar
                </a>
            <?php else: ?>
                <a class="btn btn-success" href="index.php?route=toggle-user&id=<?= $user['id'] ?>&status=1">
                    Ativar
                </a>
            <?php endif; ?>

        </p>

    <?php endforeach; ?>

    <hr>
    <h3>Monitoramento</h3>

    <a class="btn btn-primary" href="index.php?route=update-all-prices">
        Atualizar todos os preços
    </a>
    <hr>
    
    <br>
    <a class="btn btn-back" href="index.php?route=home">Voltar</a>
</body>
</html>