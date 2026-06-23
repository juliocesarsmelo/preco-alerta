<?php /** @var array $products */ ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos Monitorados | Preço Alerta</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>

    <h2>Produtos Monitorados</h2>
    <hr>

    <?php if (isset($_SESSION['erro'])): ?>
        <p style="color:red">
            <?= $_SESSION['erro'] ?>
        </p>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <p style="color:green">
            <?= $_SESSION['sucesso'] ?>
        </p>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?route=create-product">

        <input type="text"
            name="name"
            placeholder="Nome do produto">
        <br><br>

        <input type="text"
            name="link"
            placeholder="Link do produto">
        <br><br>

        <input type="number"
            step="0.01"
            name="target_price"
            placeholder="Preço desejado">
        <br><br>

        <button type="submit">
            Cadastrar
        </button>
        
    </form><br><br>

    <h3>Meus Produtos</h3>

    <hr>

    <?php foreach ($products as $product): ?>
        <p>
            <strong><?= $product['nome'] ?></strong>
            <br>
            Preço desejado:
            R$ <?= $product['preco_desejado'] ?>
            <br>
            Preço atual:
            R$ <?= number_format($product['preco_atual'], 2, ',', '.') ?>
            <br>
            <?php if ($product['ultimo_preco_alertado']): ?>
                Último preço alertado:
                R$ <?= number_format($product['ultimo_preco_alertado'], 2, ',', '.') ?>
            <?php endif; ?><br>
            <a class="btn btn-primary" href="<?= $product['link'] ?>" target="_blank">
                Ver produto
            </a>
            <a class="btn btn-secondary" href="index.php?route=price-history&id=<?= $product['id'] ?>">
                Ver histórico
            </a>
            <a class="btn btn-secondary" href="index.php?route=update-price&id=<?= $product['id'] ?>">
                Atualizar preço
            </a>
            <?php if ($product['alerta_ativo']): ?>
            <a class="btn btn-secondary" href="index.php?route=toggle-alert&id=<?= $product['id'] ?>&status=0">
                Desativar alerta
            </a>
            <?php else: ?>
                <a class="btn btn-secondary" href="index.php?route=toggle-alert&id=<?= $product['id'] ?>&status=1">
                    Ativar alerta
                </a>
            <?php endif; ?>  
            <a class="btn btn-danger" href="index.php?route=delete-product&id=<?= $product['id'] ?>"
            onclick="return confirm('Deseja remover este produto?')">
                Excluir
            </a>
            <hr><br>
        </p>
    <?php endforeach; ?>

    <a class="btn btn-back" href="index.php?route=home">Voltar</a>

</body>
</html>