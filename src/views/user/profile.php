<?php /** @var array $user */ ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | Preço Alerta</title>
</head>
<body>
    <h2>Meu Perfil</h2>

    <?php if (isset($_SESSION['erro'])): ?>
        <p style="color:red"><?= $_SESSION['erro'] ?></p>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <p style="color:green"><?= $_SESSION['sucesso'] ?></p>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?route=update-profile">
        <input type="text" name="name" value="<?= $user['nome'] ?>"><br><br>
        <input type="email" name="email" value="<?= $user['email'] ?>"><br><br>

        <button type="submit">Salvar</button>
    </form>

    <br>
    <form method="POST" action="index.php?route=delete-account" 
      onsubmit="return confirm('Tem certeza que deseja excluir sua conta?');">
        <button type="submit" style="color:red;">
            Excluir minha conta
        </button>
    </form>

    <br>
    <a href="index.php?route=home">Voltar</a>
</body>
</html>