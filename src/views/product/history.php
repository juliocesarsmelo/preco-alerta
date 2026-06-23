<?php /** @var array $product */ ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Preços | Preço Alerta</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <h2>Histórico de Preços</h2>

    <h3><?= $product['nome'] ?></h3>

    <?php if (empty($history)): ?>
        <p>Nenhum histórico registrado ainda.</p>
    <?php else: ?>

        <?php foreach ($history as $item): ?>
            <p>
                R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                -
                <?= date('d/m/Y H:i', strtotime($item['coletado_em'])) ?>
            </p>
        <?php endforeach; ?>

    <?php endif; ?>

    <a class="btn btn-back" href="index.php?route=products">Voltar</a>
</body>
</html>