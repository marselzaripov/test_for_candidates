<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use App\Request\CalculatePriceRequest;
use App\Request\PurchaseRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/v1/product/calculate', methods: ['GET']),]
    public function calculatePrice(CalculatePriceRequest $request, EntityManagerInterface $em, ProductService $service): Response
    {
        $request->validate();

        $data = $request->getRequest()->toArray();
        $product = $em->getRepository(Product::class)->find($data['id']);

        if (!$product) {
            return $this->json([
                'message' => '404'
            ], 404);
        }

        return $this->json([
            'price' => $service->getProductPrice($product, $em, $data['taxNumber'], $data['couponCode']),
        ]);
    }

    #[Route('/api/v1/product/purchase', methods: ['POST'])]
    public function purchase(PurchaseRequest $request, EntityManagerInterface $entityManager, ProductService $service): Response
    {
        $request->validate();

        $data = $request->getRequest()->toArray();
        $product = $entityManager->getRepository(Product::class)->find($data['product_id']);

        if (!$product) {
            return $this->json([
                'message' => '404'
            ], 404);
        }

        $ok = $service->purchaseProduct($product, $entityManager, $data['tax_number'], $data['payment_processor'], $data['coupon_code']);

        if (!$ok) {
            return $this->json([
                'message' => 'Bad request'
            ], 400);
        }

        return $this->json([
            'message' => 'Success'
        ]);
    }
}
