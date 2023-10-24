<?php

namespace App\Service;

use App\Entity\Product;
use App\Traits\CouponNumberTrait;
use App\Traits\TaxNumberTrait;
use Doctrine\ORM\EntityManagerInterface;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class ProductService
{
    use TaxNumberTrait, CouponNumberTrait;

    private $paymentProcessors = [
        'paypal' => PaypalPaymentProcessor::class.":pay",
        'stripe' => StripePaymentProcessor::class.":processPayment"
    ];

    public function getProductPrice(Product $product, EntityManagerInterface $em, string $taxNumber, ?string $couponNumber = null): float
    {
        $priceWithTax = $this->calculatePriceTax($em, $taxNumber, $product->getPrice());
        return $this->calculateCouponDiscount($em, $couponNumber, $priceWithTax);
    }

    public function purchaseProduct(Product $product, EntityManagerInterface $em, string $taxNumber, string $paymentProcessorType, ?string $couponCode = null): bool
    {
        $price = $this->getProductPrice($product, $em, $taxNumber, $couponCode);

        $paymentProcessor = $this->paymentProcessors[$paymentProcessorType] ?? null;

        if (!$paymentProcessor) {
            return false;
        }

        list($class, $action) = explode(":", $paymentProcessor);
        (new $class())->$action($price);

        return true;
    }
}

