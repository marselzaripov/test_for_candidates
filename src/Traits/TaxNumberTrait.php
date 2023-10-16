<?php

namespace App\Traits;

use App\Entity\Tax;
use Doctrine\ORM\EntityManagerInterface;

trait TaxTrait
{
    public function calculatePriceTax(EntityManagerInterface $entityManager, string $taxNumber, float $productPrice): float
    {
        $taxFormat = $this->getTaxNumberFormat($taxNumber);

        $tax = $entityManager->getRepository(Tax::class)->findOneBy([
            'format' => $taxFormat
        ]);

        return (!$tax) ? $productPrice : ($productPrice + ($productPrice*$tax->getPercent()/100));
    }

    public function getTaxNumberFormat(string $taxNumber): string
    {
        return substr($taxNumber, 0, 2).preg_replace('/[0-9]/', 'X', preg_replace('/[a-zA-Z]/', 'Y', substr($taxNumber, 2)));
    }
}
