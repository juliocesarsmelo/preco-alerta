<?php

namespace Juliomelo\PrecoAlerta\Controllers;

use Juliomelo\PrecoAlerta\Services\PriceService;
use Juliomelo\PrecoAlerta\Models\Product;
use Juliomelo\PrecoAlerta\Services\Mail;
use Juliomelo\PrecoAlerta\Models\User;
use PDO;

class ProductController {

    private Product $product;
    private User $user;

    public function __construct(PDO $pdo) {
        $this->product = new Product($pdo);
        $this->user = new User($pdo);
    }

    public function create() {

        $name = trim($_POST['name'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $targetPrice = trim($_POST['target_price'] ?? '');

        if (!$name || !$link || !$targetPrice) {

            $_SESSION['erro'] = "Preencha todos os campos";

            header("Location: index.php?route=products");
            exit;
        }

        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            $link = 'https://' . $link;
        }

        $this->product->createProduct(
            $_SESSION['user'],
            $name,
            $link,
            $targetPrice
        );  

        $_SESSION['sucesso'] = "Produto cadastrado!";

        header("Location: index.php?route=products");
        exit;
    }

    public function index() {

        $products = $this->product->getProductsByUser(
            $_SESSION['user']
        );

        require __DIR__ . '/../Views/product/index.php';
    }

    public function toggleAlert() {

        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if (!$id) {

            $_SESSION['erro'] = "Produto inválido";

            header("Location: index.php?route=products");
            exit;
        }

        $this->product->toggleAlert(
            $id,
            $status,
            $_SESSION['user']
        );

        $_SESSION['sucesso'] = "Alerta atualizado";

        header("Location: index.php?route=products");
        exit;
    }

    public function delete() {

        $id = $_GET['id'] ?? null;

        if (!$id) {

            $_SESSION['erro'] = "Produto inválido";

            header("Location: index.php?route=products");
            exit;
        }

        $this->product->deleteProduct(
            $id,
            $_SESSION['user']
        );

        $_SESSION['sucesso'] = "Produto removido";

        header("Location: index.php?route=products");
        exit;
    }

    public function updatePrice() {

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['erro'] = "Produto inválido";
            header("Location: index.php?route=products");
            exit;
        }

        $product = $this->product->findProductById($id, $_SESSION['user']);

        if (!$product) {
            $_SESSION['erro'] = "Produto não encontrado";
            header("Location: index.php?route=products");
            exit;
        }

        $priceService = new PriceService();

        $newPrice = $priceService->getPriceFromUrl($product['link']);

        if ($newPrice === null) {
            $_SESSION['erro'] = "Não foi possível consultar o preço pela API.";
            header("Location: index.php?route=products");
            exit;
        }

        $this->product->updateCurrentPrice(
            $id,
            $_SESSION['user'],
            $newPrice
        );

        $this->product->addPriceHistory($id, $newPrice);

        if (
            $product['alerta_ativo'] &&
            $newPrice <= $product['preco_desejado'] &&
            !$product['alerta_enviado']
        ) {
            $subject = "Preço desejado atingido - Preço Alerta";

            $body = "
                <h3>Preço desejado atingido!</h3>

                <p>Olá, {$_SESSION['name']}!</p>

                <p>O produto <strong>{$product['nome']}</strong> atingiu o preço desejado.</p>

                <p><strong>Preço atual:</strong> R$ " . number_format($newPrice, 2, ',', '.') . "</p>

                <p><strong>Preço desejado:</strong> R$ " . number_format($product['preco_desejado'], 2, ',', '.') . "</p>

                <p>
                    <a href='{$product['link']}'>Ver produto</a>
                </p>
            ";

            Mail::send($_SESSION['email'], $subject, $body);

            $this->product->markAlertAsSent(
                $id,
                $_SESSION['user']
            );
        }

        if ($newPrice > $product['preco_desejado']) {
            $this->product->resetAlertSent(
                $id,
                $_SESSION['user']
            );
        }

        $_SESSION['sucesso'] = "Preço atualizado com sucesso!";

        header("Location: index.php?route=products");
        exit;
    }

    public function history() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['erro'] = "Produto inválido";
            header("Location: index.php?route=products");
            exit;
        }

        $product = $this->product->findProductById($id, $_SESSION['user']);

        if (!$product) {
            $_SESSION['erro'] = "Produto não encontrado";
            header("Location: index.php?route=products");
            exit;
        }

        $history = $this->product->getPriceHistory($id, $_SESSION['user']);

        require __DIR__ . '/../Views/product/history.php';
    }

    public function updateAllPrices() {

        $priceService = new PriceService();

        $products = $this->product->getAllProducts();

        foreach ($products as $product) {

            $newPrice = $priceService->getPriceFromUrl(
                $product['link']
            );

            if ($newPrice === null) {
                $_SESSION['erro'] = "Não foi possível consultar o preço pela API.";
                header("Location: index.php?route=products");
                exit;
            }

            $this->product->updateCurrentPrice(
                $product['id'],
                $product['usuario_id'],
                $newPrice
            );

            $this->product->addPriceHistory(
                $product['id'],
                $newPrice
            );

            if (
                $newPrice <= $product['preco_desejado']
                && $product['ultimo_preco_alertado'] != $newPrice
            ) {

                $user = $this->user->findUserById(
                    $product['usuario_id']
                );

                $subject = "Preço desejado atingido - Preço Alerta";

                $body = "
                    <h3>Preço desejado atingido!</h3>

                    <p>Olá, {$user['nome']}!</p>

                    <p>O produto
                    <strong>{$product['nome']}</strong>
                    atingiu o preço desejado.</p>

                    <p>
                    Preço atual:
                    R$ " . number_format($newPrice, 2, ',', '.') . "
                    </p>
                ";

                Mail::send(
                    $user['email'],
                    $subject,
                    $body
                );

                $this->product->updateLastAlertPrice(
                    $product['id'],
                    $product['usuario_id'],
                    $newPrice
                );
            }
        }

        $_SESSION['sucesso'] =
            "Todos os preços foram atualizados.";

        header("Location: index.php?route=admin");
        exit;
    }
}