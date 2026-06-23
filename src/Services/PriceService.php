<?php

namespace Juliomelo\PrecoAlerta\Services;

class PriceService {

    public function getPriceFromUrl($url) {

        $productId = $this->extractProductId($url);

        if (!$productId) {
            return null;
        }

        $apiUrl = "https://dummyjson.com/products/" . $productId;

        $response = @file_get_contents($apiUrl);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);

        if (!isset($data['price'])) {
            return null;
        }

        $basePrice = (float) $data['price'];

        // Simula variação de -10% até +10%
        $variation = rand(-10, 10) / 100;

        $finalPrice = $basePrice + ($basePrice * $variation);

        return round($finalPrice, 2);
    }

    private function extractProductId($url) {

        $parts = parse_url($url);

        if (!isset($parts['path'])) {
            return null;
        }

        preg_match('/\d+/', $parts['path'], $matches);

        return $matches[0] ?? null;
    }
}