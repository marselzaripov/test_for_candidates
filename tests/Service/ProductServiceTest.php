<?php

namespace App\Tests\Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Entity\Tax;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel(([
            'environment' => 'test_env',
            'debug'       => false,
        ]));
        $this->em = $kernel->getContainer()
            ->get('doctrine.orm.entity_manager');
    }

    public function testPriceCalculation(): void
    {
        $product = new Product();
        $product->setName('BDSM equipment');
        $product->setPrice(888);

        $this->em->persist($product);
        $this->em->flush();

        $tax = new Tax();
        $tax->setCountry('Germany');
        $tax->setFormat('DEXXXXXXXXX');
        $tax->setPercent(20);

        $this->em->persist($tax);
        $this->em->flush();

        $couponTypes = [
            Coupon::PERCENT_TYPE,
            Coupon::FIXED_TYPE
        ];

        $coupon = new Coupon();
        $coupon->setCode('D15');
        $coupon->setTotal(15);
        $coupon->setType($couponTypes[array_rand($couponTypes)]);

        $this->em->persist($coupon);
        $this->em->flush();

        $productPrice = $product->getPrice();
        $priceWithTax = ($productPrice + ($productPrice*$tax->getPercent()/100));

        if ($coupon->getType() == Coupon::PERCENT_TYPE) {
            $resultPrice = $priceWithTax - ($priceWithTax*$coupon->getTotal()/100);
        } else {
            $resultPrice = $priceWithTax - $coupon->getTotal();
        }

        $productService = new ProductService();
        $testPrice = $productService->getPrice($product, $this->em, 'DE999999999', $coupon->getCode());

        $this->em->remove($product);
        $this->em->flush();

        $this->em->remove($tax);
        $this->em->flush();

        $this->em->remove($coupon);
        $this->em->flush();

        $this->assertTrue($testPrice == $resultPrice);
    }
}
