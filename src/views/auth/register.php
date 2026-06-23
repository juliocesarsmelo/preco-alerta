<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Preço Alerta</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
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

    <h2>Cadastro</h2>
    <hr>

    <br>
    <form method="POST" action="index.php?route=register">
        <input type="text" name="name" placeholder="Nome"><br><br>
        <input type="email" name="email" placeholder="E-mail"><br><br>
        <input type="password" name="password" placeholder="Senha"><br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <br><br>
    <a class="btn btn-back" href="index.php?route=home">Voltar</a>
</body>
</html>