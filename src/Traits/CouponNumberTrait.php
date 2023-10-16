<?php


namespace App\Traits;

use App\Entity\Coupon;
use Doctrine\ORM\EntityManagerInterface;

trait CouponTrait
{
    public function calculateCoupon(EntityManagerInterface $entityManager, ?string $couponCode, float $productPrice): float
    {
        if (!$couponCode) return $productPrice;

        $coupon = $entityManager->getRepository(Coupon::class)->findOneBy([
            'code' => $couponCode
        ]);

        if (!$coupon || ($coupon->getType() != Coupon::PERCENT_TYPE && $coupon->getType() != Coupon::FIXED_TYPE))
        {
            return $productPrice;
        }
        else if ($coupon->getType() == Coupon::PERCENT_TYPE)
        {
            return $productPrice - ($productPrice*$coupon->getTotal()/100);
        }
        else
        {
            return $productPrice - $coupon->getTotal();
        }
    }
}
