<?php
// src/Controller/BasketController.php
namespace App\Controller;

use App\Entity\Basket;
use App\Entity\BasketProduct;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    #[Route('/products', name: 'product_list')]
    public function list(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        $stripePublicKey = $this->getParameter('stripe_public_key');

        return $this->render('basket/product_list.html.twig', [
            'products' => $products,
            'stripe_public_key' => $stripePublicKey,
        ]);
    }

    #[Route('/basket/create', name: 'create_basket', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = $request->request->all();
        $productIds = $data['products'] ?? [];

        if (empty($productIds)) {
            return new Response(json_encode(['error' => 'No products selected.']), 400, ['Content-Type' => 'application/json']);
        }

        $products = $entityManager->getRepository(Product::class)->findBy(['id' => $productIds]);

        if (empty($products)) {
            return new Response(json_encode(['error' => 'No valid products found.']), 400, ['Content-Type' => 'application/json']);
        }

        $basket = new Basket();
        $basket->setCreatedAt(new \DateTimeImmutable());
        $basket->setIdUser($this->getUser());

        foreach ($products as $product) {
            $quantity = $data["quantity-{$product->getId()}"] ?? 1;
            $basketProduct = new BasketProduct();
            $basketProduct->setProduct($product);
            $basketProduct->setQuantity((int)$quantity);
            $basketProduct->setBasket($basket);
            $entityManager->persist($basketProduct);
        }

        $entityManager->persist($basket);
        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new Response(json_encode(['error' => 'Failed to save basket.', 'details' => $e->getMessage()]), 500, ['Content-Type' => 'application/json']);
        }

        return new Response(json_encode(['id' => $basket->getId()]), 200, ['Content-Type' => 'application/json']);
    }
}
