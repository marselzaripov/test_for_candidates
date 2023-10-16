<?php

namespace App\Service;

use App\Repository\ProductRepository;

class PriceService
{
    public function __construct(private ProductRepository $productRepository )
    {
    }

    public function getProducts()
    {

    }
}

