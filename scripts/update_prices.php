<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Database.php';

use Juliomelo\PrecoAlerta\Models\Product;
use Juliomelo\PrecoAlerta\Models\User;
use Juliomelo\PrecoAlerta\Services\PriceService;
use Juliomelo\PrecoAlerta\Services\Mail;

$pdo = Database::conectar();

$productModel = new Product($pdo);
$userModel = new User($pdo);
$priceService = new PriceService();

$products = $productModel->getAllProducts();

foreach ($products as $product) {

    $newPrice = $priceService->getPriceFromUrl($product['link']);

    if ($newPrice === null) {
        continue;
    }

    $productModel->updateCurrentPrice(
        $product['id'],
        $product['usuario_id'],
        $newPrice
    );

    $productModel->addPriceHistory(
        $product['id'],
        $newPrice
    );

    if (
        $product['alerta_ativo'] &&
        $newPrice <= $product['preco_desejado'] &&
        !$product['alerta_enviado']
    ) {
        $user = $userModel->findUserById($product['usuario_id']);

        $subject = "Preço desejado atingido - Preço Alerta";

        $body = "
            <h3>Preço desejado atingido!</h3>
            <p>Olá, {$user['nome']}!</p>
            <p>O produto <strong>{$product['nome']}</strong> atingiu o preço desejado.</p>
            <p>Preço atual: R$ " . number_format($newPrice, 2, ',', '.') . "</p>
        ";

        Mail::send($user['email'], $subject, $body);

        $productModel->markAlertAsSent(
            $product['id'],
            $product['usuario_id']
        );
    }

    if ($newPrice > $product['preco_desejado']) {
        $productModel->resetAlertSent(
            $product['id'],
            $product['usuario_id']
        );
    }
}

echo "Monitoramento executado com sucesso.";